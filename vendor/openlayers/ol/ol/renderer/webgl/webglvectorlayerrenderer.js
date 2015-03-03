goog.provide('ol.renderer.webgl.VectorLayer');

goog.require('goog.array');
goog.require('goog.asserts');
goog.require('goog.events');
goog.require('ol.ViewHint');
goog.require('ol.extent');
goog.require('ol.layer.Vector');
goog.require('ol.render.webgl.ReplayGroup');
goog.require('ol.renderer.vector');
goog.require('ol.renderer.webgl.Layer');
goog.require('ol.vec.Mat4');



/**
 * @constructor
 * @extends {ol.renderer.webgl.Layer}
 * @param {ol.renderer.webgl.Map} mapRenderer Map renderer.
 * @param {ol.layer.Vector} vectorLayer Vector layer.
 */
ol.renderer.webgl.VectorLayer = function(mapRenderer, vectorLayer) {

  goog.base(this, mapRenderer, vectorLayer);

  /**
   * @private
   * @type {boolean}
   */
  this.dirty_ = false;

  /**
   * @private
   * @type {number}
   */
  this.renderedRevision_ = -1;

  /**
   * @private
   * @type {number}
   */
  this.renderedResolution_ = NaN;

  /**
   * @private
   * @type {ol.Extent}
   */
  this.renderedExtent_ = ol.extent.createEmpty();

  /**
   * @private
   * @type {function(ol.Feature, ol.Feature): number|null}
   */
  this.renderedRenderOrder_ = null;

  /**
   * @private
   * @type {ol.render.webgl.ReplayGroup}
   */
  this.replayGroup_ = null;

  /**
   * The last layer state.
   * @private
   * @type {?ol.layer.LayerState}
   */
  this.layerState_ = null;

};
goog.inherits(ol.renderer.webgl.VectorLayer, ol.renderer.webgl.Layer);


/**
 * @inheritDoc
 */
ol.renderer.webgl.VectorLayer.prototype.composeFrame =
    function(frameState, layerState, context) {
  this.layerState_ = layerState;
  var viewState = frameState.viewState;
  var replayGroup = this.replayGroup_;
  if (!goog.isNull(replayGroup) && !replayGroup.isEmpty()) {
    replayGroup.replay(context,
        viewState.center, viewState.resolution, viewState.rotation,
        frameState.size, frameState.pixelRatio, layerState.opacity,
        layerState.brightness, layerState.contrast, layerState.hue,
        layerState.saturation, frameState.skippedFeatureUids);
  }

};


/**
 * @inheritDoc
 */
ol.renderer.webgl.VectorLayer.prototype.disposeInternal = function() {
  var replayGroup = this.replayGroup_;
  if (!goog.isNull(replayGroup)) {
    var context = this.mapRenderer.getContext();
    replayGroup.getDeleteResourcesFunction(context)();
    this.replayGroup_ = null;
  }
  goog.base(this, 'disposeInternal');
};


/**
 * @inheritDoc
 */
ol.renderer.webgl.VectorLayer.prototype.forEachFeatureAtCoordinate =
    function(coordinate, frameState, callback, thisArg) {
  if (goog.isNull(this.replayGroup_) || goog.isNull(this.layerState_)) {
    return undefined;
  } else {
    var context = this.mapRenderer.getContext();
    var viewState = frameState.viewState;
    var layer = this.getLayer();
    var layerState = this.layerState_;
    /** @type {Object.<string, boolean>} */
    var features = {};
    return this.replayGroup_.forEachFeatureAtCoordinate(coordinate,
        context, viewState.center, viewState.resolution, viewState.rotation,
        frameState.size, frameState.pixelRatio,
        layerState.opacity, layerState.brightness, layerState.contrast,
        layerState.hue, layerState.saturation, frameState.skippedFeatureUids,
        /**
         * @param {ol.Feature} feature Feature.
         * @return {?} Callback result.
         */
        function(feature) {
          goog.asserts.assert(goog.isDef(feature));
          var key = goog.getUid(feature).toString();
          if (!(key in features)) {
            features[key] = true;
            return callback.call(thisArg, feature, layer);
          }
        });
  }
};


/**
 * @inheritDoc
 */
ol.renderer.webgl.VectorLayer.prototype.hasFeatureAtCoordinate =
    function(coordinate, frameState) {
  if (goog.isNull(this.replayGroup_) || goog.isNull(this.layerState_)) {
    return false;
  } else {
    var context = this.mapRenderer.getContext();
    var viewState = frameState.viewState;
    var layerState = this.layerState_;
    return this.replayGroup_.hasFeatureAtCoordinate(coordinate,
        context, viewState.center, viewState.resolution, viewState.rotation,
        frameState.size, frameState.pixelRatio,
        layerState.opacity, layerState.brightness, layerState.contrast,
        layerState.hue, layerState.saturation, frameState.skippedFeatureUids);
  }
};


/**
 * @inheritDoc
 */
ol.renderer.webgl.VectorLayer.prototype.forEachLayerAtPixel =
    function(pixel, frameState, callback, thisArg) {
  var coordinate = pixel.slice();
  ol.vec.Mat4.multVec2(
      frameState.pixelToCoordinateMatrix, coordinate, coordinate);
  var hasFeature = this.hasFeatureAtCoordinate(coordinate, frameState);

  if (hasFeature) {
    return callback.call(thisArg, this.getLayer());
  } else {
    return undefined;
  }
};


/**
 * Handle changes in image style state.
 * @param {goog.events.Event} event Image style change event.
 * @private
 */
ol.renderer.webgl.VectorLayer.prototype.handleStyleImageChange_ =
    function(event) {
  this.renderIfReadyAndVisible();
};


/**
 * @inheritDoc
 */
ol.renderer.webgl.VectorLayer.prototype.prepareFrame =
    function(frameState, layerState, context) {

  var vectorLayer = /** @type {ol.layer.Vector} */ (this.getLayer());
  goog.asserts.assertInstanceof(vectorLayer, ol.layer.Vector);
  var vectorSource = vectorLayer.getSource();

  this.updateAttributions(
      frameState.attributions, vectorSource.getAttributions());
  this.updateLogos(frameState, vectorSource);

  if (!this.dirty_ && (!vectorLayer.getUpdateWhileAnimating() &&
      frameState.viewHints[ol.ViewHint.ANIMATING] ||
      frameState.viewHints[ol.ViewHint.INTERACTING])) {
    return true;
  }

  var frameStateExtent = frameState.extent;
  var viewState = frameState.viewState;
  var projection = viewState.projection;
  var resolution = viewState.resolution;
  var pixelRatio = frameState.pixelRatio;
  var vectorLayerRevision = vectorLayer.getRevision();
  var vectorLayerRenderBuffer = vectorLayer.getRenderBuffer();
  var vectorLayerRenderOrder = vectorLayer.getRenderOrder();

  if (!goog.isDef(vectorLayerRenderOrder)) {
    vectorLayerRenderOrder = ol.renderer.vector.defaultOrder;
  }

  var extent = ol.extent.buffer(frameStateExtent,
      vectorLayerRenderBuffer * resolution);

  if (!this.dirty_ &&
      this.renderedResolution_ == resolution &&
      this.renderedRevision_ == vectorLayerRevision &&
      this.renderedRenderOrder_ == vectorLayerRenderOrder &&
      ol.extent.containsExtent(this.renderedExtent_, extent)) {
    return true;
  }

  if (!goog.isNull(this.replayGroup_)) {
    frameState.postRenderFunctions.push(
        this.replayGroup_.getDeleteResourcesFunction(context));
  }

  this.dirty_ = false;

  var replayGroup = new ol.render.webgl.ReplayGroup(
      ol.renderer.vector.getTolerance(resolution, pixelRatio),
      extent, vectorLayer.getRenderBuffer());
  vectorSource.loadFeatures(extent, resolution, projection);
  var renderFeature =
      /**
       * @param {ol.Feature} feature Feature.
       * @this {ol.renderer.webgl.VectorLayer}
       */
      function(feature) {
    var styles;
    if (goog.isDef(feature.getStyleFunction())) {
      styles = feature.getStyleFunction().call(feature, resolution);
    } else if (goog.isDef(vectorLayer.getStyleFunction())) {
      styles = vectorLayer.getStyleFunction()(feature, resolution);
    }
    if (goog.isDefAndNotNull(styles)) {
      var dirty = this.renderFeature(
          feature, resolution, pixelRatio, styles, replayGroup);
      this.dirty_ = this.dirty_ || dirty;
    }
  };
  if (!goog.isNull(vectorLayerRenderOrder)) {
    /** @type {Array.<ol.Feature>} */
    var features = [];
    vectorSource.forEachFeatureInExtentAtResolution(extent, resolution,
        /**
         * @param {ol.Feature} feature Feature.
         */
        function(feature) {
          features.push(feature);
        }, this);
    goog.array.sort(features, vectorLayerRenderOrder);
    goog.array.forEach(features, renderFeature, this);
  } else {
    vectorSource.forEachFeatureInExtentAtResolution(
        extent, resolution, renderFeature, this);
  }
  replayGroup.finish(context);

  this.renderedResolution_ = resolution;
  this.renderedRevision_ = vectorLayerRevision;
  this.renderedRenderOrder_ = vectorLayerRenderOrder;
  this.renderedExtent_ = extent;
  this.replayGroup_ = replayGroup;

  return true;
};


/**
 * @param {ol.Feature} feature Feature.
 * @param {number} resolution Resolution.
 * @param {number} pixelRatio Pixel ratio.
 * @param {Array.<ol.style.Style>} styles Array of styles
 * @param {ol.render.webgl.ReplayGroup} replayGroup Replay group.
 * @return {boolean} `true` if an image is loading.
 */
ol.renderer.webgl.VectorLayer.prototype.renderFeature =
    function(feature, resolution, pixelRatio, styles, replayGroup) {
  if (!goog.isDefAndNotNull(styles)) {
    return false;
  }
  var i, ii, loading = false;
  for (i = 0, ii = styles.length; i < ii; ++i) {
    loading = ol.renderer.vector.renderFeature(
        replayGroup, feature, styles[i],
        ol.renderer.vector.getSquaredTolerance(resolution, pixelRatio),
        this.handleStyleImageChange_, this) || loading;
  }
  return loading;
};
