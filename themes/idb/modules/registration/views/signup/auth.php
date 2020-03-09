<?php

use app\assets\AppFormAsset;
use app\helpers\Translate;
use app\themes\idb\assets\IdbWizardAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$formAsset = AppFormAsset::register($this);
$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'Authorisation codes');
$tryCount = 3;
?>

<div class="container">
    <div class="container-inner">
        <?= $this->render('partials/_steps', ['wizardAsset' => $wizardAsset, 'count' => 4]); ?>
        <?php if ($tryCount > 0): ?>
            <?php $form = ActiveForm::begin(['id' => 'signupForm']); ?>
            <div class="row">
                <div class="col-lg-10" style="float: none;margin: 0 auto;">
                    <div class="sp-column">
                        <div class="sp-module">
                            <div class="sp-module-content">
                                <div>
                                    <?php if ($model->getErrors()) { ?>
                                        <?= Html::tag(
                                            'div',
                                            $form->errorSummary($model),
                                            ['class' => 'alert alert-danger']
                                        ) ?>
                                    <?php } ?>
                                    <?php if (Yii::$app->session->hasFlash('tryMessage')): ?>
                                        <div class="alert alert-warning alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">
                                                ×
                                            </button>
                                            <h4>
                                                <i class="icon fa fa-check"></i><?= Yii::$app->session->getFlash(
                                                    'tryMessage'
                                                ) ?>
                                            </h4>
                                            <?= Translate::_('people', 'Try left') . ': ' . $tryCount ?>
                                        </div>
                                    <?php endif; ?>
                                    <div style="display: block;background-color: #ffffff; border: 5px solid #EAEDF1; padding: 10px 10px 10px 10px">
                                        <h3><?= Translate::_('people', 'Check for email and SMS codes') ?></h3>

                                        <?= $form->field($model, 'smsCode')->textInput(
                                            ['placeholder' => Translate::_('people', 'SMS code')]
                                        ) ?>
                                        <?= $form->field($model, 'emailCode')->textInput(
                                            ['placeholder' => Translate::_('people', 'Email Code')]
                                        ) ?>
                                    </div>
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
                            'Text' => Translate::_('people', 'Continue signup'),
                            'Action' => 'Submit',
                            'Help' => Translate::_('people', 'Continue')
                        ],
                        [
                            'Text' => Translate::_('people', 'Cancel signup'),
                            'Action' => ['/signup/error'],
                            'Help' => Translate::_('people', 'Cancel')
                        ]
                    ) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-12" style="float: none;margin: 0 auto;">
                    <div class="sp-column">
                        <div class="sp-module">
                            <div class="sp-module-content" style="color: black;">
                                <div class="invoice">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <h2 class="page-header">
                                                <i class="fa fa-globe"></i> <?= Translate::_(
                                                    'people',
                                                    'You have exceeded the maximum number of attempts allowed.'
                                                ) ?>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
