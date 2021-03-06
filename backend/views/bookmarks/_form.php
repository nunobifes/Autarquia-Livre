<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Bookmarks */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bookmarks-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'viewer_id')->hiddenInput(array('value' => $_GET['viewer_id']))->label(false);
    } else {
        echo $form->field($model, 'viewer_id')->hiddenInput()->label(false);
    }
    ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'x_coordinate')->textarea(['rows' => 2]) ?>

    <?= $form->field($model, 'y_coordinate')->textarea(['rows' => 2]) ?>

    <!-- <?= $form->field($model, 'chage_data')->textInput() ?> -->

    <!-- <?= $form->field($model, 'setOrder')->textInput() ?> -->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Aplicar Alterações', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
