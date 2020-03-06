<?php

use app\assets\AppFormAsset;
use app\helpers\Translate;
use app\themes\idb\assets\IdbAsset;
use app\themes\idb\assets\IdbWizardAsset;
use idbyii2\widgets\VerificationCodeView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$idbAsset = IdbAsset::register($this);
$formAsset = AppFormAsset::register($this);
$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'Check your mobile phone');

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

                                    <div class="jumbotron" style="background-color: white;">
                                        <h2><?= Translate::_(
                                                'people',
                                                'You have been sent a code to the mobile phone number you provided.'
                                            ) ?>

                                            <br>
                                            <hr>
                                            <br>
                                            <h2 align="center"><?= Translate::_(
                                                    'people',
                                                    'Enter the SMS code below'
                                                ) ?></h2>

                                            <?= VerificationCodeView::widget(
                                                ['code' => [0 => $codeFirst, 2 => $codeThird]]
                                            ) ?>
                                    </div>

                                    <?php if ($model->captchaEnabled) {
                                        echo Yii::$app->signUpCaptcha->config(
                                            [
                                                'inputName' => 'SignUpAuthForm[verificationCode]',
                                                'inputId' => 'signupauthform-verificationcode'
                                            ]
                                        )->run();
                                    } ?>

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
                            'Text' => Translate::_('people', 'Continue account setup'),
                            'Action' => 'Submit',
                            'Help' => Translate::_('people', 'Continue')
                        ],
                        [
                            'Text' => Translate::_('people', 'Cancel account setup'),
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
