<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FormsChield */

$this->title = 'Create Forms Chield';
$this->params['breadcrumbs'][] = ['label' => 'Forms Chields', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forms-chield-create">

    <?= $this->render('_create', [
        'model' => $model,
    ]) ?>

</div>
