<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FormsChield */
/* @var $form yii\widgets\ActiveForm */
?>

<!--Adicionado Para o Ajax Model-->
<!-- modal dialog for display pop up login -->
<div class="modal-dialog our-modal-dialog">
    <div class="modal-content">
        <div class="modal-header our-modal-header">
            <button type="button" class="close" data-dismiss="modal" ><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title our-modal-title" id="myModalLabel">Adicionar Ficha Filho</h4>
        </div>
        <div class="modal-body our-modal-body">
            <!--<div class="forms-parameters-form">-->
            <!-- start ActiveForm -->
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'form_id')->hiddenInput(array('value' => $_GET['form_id']))->label(false) ?>

            <?= $form->field($model, 'template')->textInput() ?>

            <?= $form->field($model, 'sqlselect')->textarea(['rows' => 2]) ?>

            <?= $form->field($model, 'sqlinsert')->textarea(['rows' => 2]) ?>

            <?= $form->field($model, 'sqlupdate')->textarea(['rows' => 2]) ?>

            <?= $form->field($model, 'sqldelete')->textarea(['rows' => 2]) ?>

            <div class="modal-footer our-modal-footer">
                <div class="form-group our-form-group">
                    <!--<div class="form-group">-->
                    <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Aplicar Alterações', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
