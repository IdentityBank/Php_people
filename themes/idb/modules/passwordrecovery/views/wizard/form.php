<?php

use app\assets\MetronicAssetClean;
use app\helpers\Translate;
use app\themes\idb\assets\IdbWizardAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$metronicAppAsset = MetronicAssetClean::register($this);
$metronicAppAsset->fontAwesome($this);
$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'Password Recovery');


?>

<div class="container">
    <div class="container-inner">
        <?= $this->render('partials/_steps', ['wizardAsset' => $wizardAsset, 'count' => 1]); ?>
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

                                <h2 align="center"><?= Translate::_(
                                        'people',
                                        'Enter the email address and the mobile phone number associated with this account.'
                                    ) ?></h2>

                                <?= $form->field($model, 'email')->input(
                                    'email',
                                    ['placeholder' => $model->getAttributeLabel('email')]
                                ) ?>
                                <?= $form->field($model, 'mobile')->textInput(
                                    ['placeholder' => $model->getAttributeLabel('mobile')]
                                ) ?>
                                <?= $form->field($model, 'token')->textarea(
                                    ['placeholder' => $model->getAttributeLabel('token'), 'rows' => 5]
                                )->hint(
                                    Translate::_(
                                        'people',
                                        'During account creation you downloaded, printed and stored the password recovery token.'
                                    ) . ' ' .
                                    Translate::_(
                                        'people',
                                        'You need to use a QR scanner to get the password token as text and fill the text in below.'
                                    )
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
                    ]
                ) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
