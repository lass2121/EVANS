<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'BAB IV Rencana Anggaran';
$this->params['breadcrumbs'][] = ['label' => 'Proposals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-daftar-organisasi-index">

<div class="container" style="margin-left: 1%">
        <div class="row">
            <div class="col-sm-7">
                <h1 style="margin-bottom: 2%"><?= Html::encode($this->title) ?></h1>
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'daftar-update-form',
                        'action' => ['master-daftar-organisasi/update'],
                    ]);
                 ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => '',
                        'pager'        => [
                            'class' => '\yii\widgets\LinkPager',
                        ],                
                        'tableOptions' => [
                            'class' => 'table table-bordered table-hover table-light',
                            'style' => 'background-color:white;'
                        ],
                        'columns' => [
                            [
                                'attribute' => 'masterKategori.masterJenisPendapatan.DESKRIPSI',
                                'label' => 'Jenis Anggaran'
                            ],
                            'masterKategori.KATEGORI',
                            'masterKategori.DESKRIPSI',
                            [
                                'attribute' => 'VALUE',
                                'label' => 'Jumlah'
                            ],
                            
                        ],
                    ]); ?>
                    <?php ActiveForm::end(); ?>
            </div>

        <div class="col-sm-5">
        <h4 style="margin: 10% 5% 5% 5%"><b>Input Anggaran<b></h4>
            <?= $this->render('_formbab4', [
                'model_kategori' => $model_kategori,
            ]) ?>
        </div>
    </div>
</div>
