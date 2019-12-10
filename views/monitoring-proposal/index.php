<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Proposals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proposal-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Proposal', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    

    <!-- Ini Monitoring PIC Kegiatan -->
    <?php $tab_himpunan =  GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => ['class' => 'table table-bordered'],
        'options' => ['style' => 'background-color: white'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => '',
                'label' => 'Nama Proposal'
            ],
            [
                'attribute' => '',
                'label' => 'Periode'
            ],
            [
                'attribute' => '',
                'label' => 'Ketua Himpunan'
            ],
            [
                'attribute' => '',
                'label' => 'BEM'
            ],
            [
                'attribute' => '',
                'label' => 'Kaprodi'
            ],
            [
                'attribute' => '',
                'label' => 'Studev'
            ],
            [
                'attribute' => '',
                'label' => 'SA Manager'
            ],
            [
                'attribute' => '',
                'label' => 'Wakil Rektor 3'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'Detail',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('Detail',$url, ['alt' => 'detail']);
                    }
                ]
            ],
        ],
    ]); 
    ?>

    <!-- Ini Monitoring KPU -->
    <?php $tab_kpu =  GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => ['class' => 'table table-bordered'],
        'options' => ['style' => 'background-color: white'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => '',
                'label' => 'Nama Proposal'
            ],
            [
                'attribute' => '',
                'label' => 'Periode'
            ],
            [
                'attribute' => '',
                'label' => 'DKBM'
            ],
            [
                'attribute' => '',
                'label' => 'Studev'
            ],
            [
                'attribute' => '',
                'label' => 'SA Manager'
            ],
            [
                'attribute' => '',
                'label' => 'Wakil Rektor 3'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'Detail',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('Detail',$url, ['alt' => 'detail']);
                    }
                ]
            ],
        ],
    ]); 
    ?>

    <!-- Ini Monitoring UKM -->
    <?php $tab_ukm =  GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => ['class' => 'table table-bordered'],
        'options' => ['style' => 'background-color: white'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => '',
                'label' => 'Nama Proposal'
            ],
            [
                'attribute' => '',
                'label' => 'Periode'
            ],
            [
                'attribute' => '',
                'label' => 'BEM'
            ],
            [
                'attribute' => '',
                'label' => 'Studev'
            ],
            [
                'attribute' => '',
                'label' => 'SA Manager'
            ],
            [
                'attribute' => '',
                'label' => 'Wakil Rektor 3'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'Detail',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('Detail',$url, ['alt' => 'detail']);
                    }
                ]
            ],
        ],
    ]); 
    ?>

    <!-- <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ID_PROPOSAL',
            'ID_PROKER',
            'ID_PENGURUS',
            'ID_TENGGAT_WAKTU',
            'BANK',
            //'NO_REKENING',
            //'STATUS_DRAFT',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?> -->

    <?=
        TabsX::widget([
            'position' => TabsX::POS_ABOVE,
            'align' => TabsX::ALIGN_LEFT,
            'bordered' => true,
            'items' => [
                [
                    'label' => 'Himpunan',
                    'content' => $tab_himpunan,
                    'headerOptions' => [
                        'style'=>'background-color:white; width:15%;'
                    ],
                    
                ],
                [
                    'label' => 'KPU',
                    'content' =>  $tab_kpu,
                    'headerOptions' => [
                        'style'=>'background-color:white; width:15%;'
                    ],
                ],
                [
                    'label' => 'UKM',
                    'content' => $tab_ukm,
                    'headerOptions' => [
                        'style'=>'background-color:white; width:15%;'
                    ],
                ],
                
            ],
        ]);
    ?>

</div>
