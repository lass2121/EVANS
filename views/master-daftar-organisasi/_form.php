<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MasterDaftarOrganisasi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="master-daftar-organisasi-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card" style="background-color: white;padding: 5% 5% 10% 5%;box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);margin-left: 5%">

    <?= $form->field($model, 'ID_ORGANISASI')->textInput(['maxlength' => true])->hiddenInput(['value' => '99999'])->label(false) ?>

    <?= $form->field($model, 'NAMA_ORGANISASI')->textInput(['maxlength' => true])->input('NAMA_ORGANISASI', ['placeholder' => "Nama Organisasi"])->label('Nama Organisasi') ?>

    <?= $form->field($model, 'ID_JENIS')->textInput(['maxlength' => true])->dropDownList(
                   $model->dataOrganisasi()
                )->label('Jenis Organisasi') ?>

    <?= $form->field($model, 'STATUS')->textInput(['maxlength' => true])->dropDownList(
                    ['1' => 'Aktif' , '0'=> 'Tidak Aktif']
                )->label('Status') ?>

    </div>
    
    <div class="form-group" style="float: right;margin-top: 8%">
    <?= Html::a('Back', ['master-daftar-organisasi/home'], [
            'class' => 'btn btn-primary',
            'style' => 'width: 100px']) 
        ?>
        <?= Html::submitButton('Create', array(
                'class' => 'btn btn-primary',
                'style' => 'width: 100px'
            )
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
