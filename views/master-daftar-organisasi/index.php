<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Organisasi';
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
                                'attribute' => 'ID_ORGANISASI',
                                'label' => 'id'
                            ],
                            [   
                                'attribute' =>'NAMA_ORGANISASI',
                                'label' => 'Nama Organisasi'
                            ],
                            [
                                'attribute' => 'masterJenisOrganisasi.JENIS_ORGANISASI',
                                'label' => 'Jenis Organisasi'
                            ],
                            [
                                'format' => 'raw',
                                'header' => 'Status',
                                'value' => function($model, $key, $index, $column) {
                                    $option = array();
                                    foreach($model->dataStatus() as $key => $value){
                                        if($key==$model->STATUS){
                                            $option[] = '<option value="'.$key.'"Selected >'.$value.'</option>'; 
                                        }
                                        else{
                                            $option[] = '<option value="'.$key.'" >'.$value.'</option>'; 
                                        }
                                    }
                                    $select = '
                                    <select class="form-control" name="selectStatus['.$model->ID_ORGANISASI.']" >
                                    '.implode('',$option).'
                                    </select>';
                                    return $select;  
                                }
                            ],
                            [
                                'label' => 'Update',
                                'format' => 'raw',
                                'value' => function($model, $key, $index, $column) {
                                    $button = '
                                    <input type="submit" class="btn btn-info updateBtn" value="Update" name="updateBtn['.$model->ID_ORGANISASI.']">
                                    ';
                                    return $button;
                                }
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
                    ]); ?>
                    <?php ActiveForm::end(); ?>
            </div>

        <div class="col-sm-5">
        <h4 style="margin: 10% 5% 5% 5%"><b>Input Organisasi<b></h4>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>

