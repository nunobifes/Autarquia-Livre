<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\GeographicEdit */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Geographic Edits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="geographic-edit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'viewer_id',
            'name:ntext',
            'layer:ntext',
            'type:ntext',
            'chage_data',
            'setOrder',
        ],
    ]) ?>

</div>
