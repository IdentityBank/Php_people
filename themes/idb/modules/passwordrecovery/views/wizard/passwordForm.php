<?php

use app\assets\MetronicAssetClean;
use app\helpers\Translate;
use app\themes\idb\assets\IdbWizardAsset;
use idbyii2\widgets\PasswordGenerator;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$metronicAppAsset = MetronicAssetClean::register($this);
$metronicAppAsset->fontAwesome($this);
$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'Complete your account details');


?>

<div class="container">
    <div class="container-inner">
        <?= $this->render('partials/_steps', ['wizardAsset' => $wizardAsset, 'count' => 4]); ?>
        <?php $form = ActiveForm::begin(['id' => 'signupForm']); ?>
        <div class="row">
            <div class="col-lg-10" style="float: none;margin: 0 auto;">
                <div class="sp-column">
                    <div class="sp-module">
                        <div class="sp-module-content">
                            <div style="display: block;background-color: #ffffff; border: 5px solid #EAEDF1; padding: 10px 10px 10px 10px">
                                <?php if ($model->getErrors()) { ?>
                                    <?= Html::tag(
                                        'div',
                                        $form->errorSummary($model),
                                        ['class' => 'alert alert-danger']
                                    ) ?>
                                <?php } ?>
                                <?php if (Yii::$app->session->hasFlash('idMessage')): ?>
                                    <div class="alert alert-warning alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×
                                        </button>
                                        <h4>
                                            <i class="icon fa fa-check"></i><?= Yii::$app->session->getFlash(
                                                'idMessage'
                                            ) ?>
                                        </h4>
                                        <?= Translate::_('people', 'Contact with administrator, or signup here') ?>
                                    </div>
                                <?php endif; ?>

                                <?= PasswordGenerator::widget(
                                    ['model' => $model, 'form' => $form, 'style' => 'people-idb']
                                ) ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-12" style="float: none;margin: 0 auto;">
                <?= $wizardAsset->generateWizardActions(
                    [
                        'Text' => Translate::_('people', 'Continue password recovery'),
                        'Action' => 'Submit',
                        'Help' => Translate::_('people', 'Continue')
                    ],
                    [
                        'Text' => Translate::_('people', 'Cancel password recovery'),
                        'Action' => ['/login'],
                        'Help' => Translate::_('people', 'Cancel')
                    ]
                ) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
