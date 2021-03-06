<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Proposal */

$this->title = 'BAB I Pendahuluan';
$this->params['breadcrumbs'][] = ['label' => 'Proposals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <?php $form = ActiveForm::begin(); ?>
        <div class="hide">
            <?= $form->field($model, 'ID_BAB_1')->hiddenInput(['value' => '99999'])->label(false) ?>

            <?= $form->field($model, 'ID_PROPOSAL')->hiddenInput(['value' => "$id"])->label(false) ?>
        </div>

        <div class="row" style="margin-top: 5%;">
            <div class="col-sm-6 col-sm-offset-3">
            <h4 ><b>BAB I Pendahuluan <b></h4>
                <div class="card" style="background-color: white; padding: 5%; margin: 5% 0 5% 0; border-radius: 7px 7px 7px 7px;">
                    <div style="margin-bottom:5%;">
                        <?php 
                            if($file_bab_1 != ''){
                                echo "File Bab 1 : " . Html::a($file_bab_1, ['monitoring-proposal/download', 'filename' => $file_bab_1], ['class' => 'profile-link']); 
                            }
                        ?>
                    </div>
                    
                    
                    <?php $hint = "Unggah pindaian BAB I.</br><span style='color: red;'><i>*Format docs/pdf maks 5mb</i></span>" ?>

                    <?= $form->field($model, 'FILE_BAB_1', [
                    'template' => '
                        <div>
                            {input}
                        </div>
                        {error}
                        {hint}
                        ',
                    
                    ])->widget(FileInput::classname(), [
                        'pluginOptions' => [
                            'allowedFileExtensions'=>['docs','pdf'],
                            'showPreview' => false,
                            'showCaption' => true,
                            'showRemove' => true,
                            'showUpload' => false,
                        ],
                        'options' => [
                            'accept' => '.docs, .pdf',
                            'multiple'=> false,

                        ],
                    ])->label('',['class' => ''])
                    ?>
                    <div class="help-block">
                        <?= $hint ?>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="form-group" style="float: right;margin-top: 2%">
                    <?= Html::a('Back', ['update' , 'id'=> $id ], [
                        'class' => 'btn btn-primary',
                        'style' => 'width: 100px']) 
                    ?>
                    <?= Html::submitButton('Save', array(
                            'class' => 'btn btn-primary',
                            'style' => 'width: 100px'
                        )
                    ) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
