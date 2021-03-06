<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MaprintScales */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="maprint-scales-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'maprint_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'scale')->textInput() ?>

    <?= $form->field($model, 'label')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Aplicar Alterações', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
