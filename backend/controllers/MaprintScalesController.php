<?php

namespace backend\controllers;

use Yii;
use app\models\MaprintScales;
use app\models\MaprintScalesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MaprintScalesController implements the CRUD actions for MaprintScales model.
 */
class MaprintScalesController extends Controller
{
    public $layout = 'admin_layout';
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'index', 'delete', 'update'],
                        'roles' => ['@'],
                    ],
//                    [
//                        'allow' => false,
//                        'actions' => ['create'],
//                        'roles' => ['@'],
//                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all MaprintScales models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MaprintScalesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MaprintScales model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MaprintScales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MaprintScales();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['maprint/update', 'id' => $model->maprint_id, 'viewer_id' => $_GET['viewer_id']]);
        } else {
            return $this->renderAjax('create', [
                        'model' => $model,
            ]);
            
        }
    }

    /**
     * Updates an existing MaprintScales model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['maprint/update', 'id' => $model->maprint_id, 'viewer_id' => $_GET['viewer_id']]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MaprintScales model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $maprint_id, $viewer_id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['maprint/update', 'id' => $maprint_id, 'viewer_id' => $viewer_id]);
    }

    /**
     * Finds the MaprintScales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaprintScales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaprintScales::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
