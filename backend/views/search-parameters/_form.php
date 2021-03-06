<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchParameters */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="search-parameters-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'search_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'require')->checkbox() ?>

    <?= $form->field($model, 'type')->dropDownList(['values_list' => 'Lista de Valores', 'text' => 'Texto Livre'], ['prompt' => '---- Select Type of Parameter ----']) ?>

    <?= $form->field($model, 'value_field')->textInput() ?>

    <?= $form->field($model, 'description_field')->textInput() ?>

    <?= $form->field($model, 'sqlquery')->textarea(['rows' => 2]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
