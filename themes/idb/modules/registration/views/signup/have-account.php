<?php

use app\helpers\Translate;
use app\themes\idb\assets\IdbWizardAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var String $businessAccountName */

$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'Welcome to Identity Bank');

?>

<div class="container">
    <div class="container-inner">
        <?= $this->render('partials/_steps', ['wizardAsset' => $wizardAsset, 'count' => 1]); ?>
        <div class="row">
            <div class="col-lg-12" style="float: none;margin: 0 auto;">
                <div class="sp-column">
                    <div class="sp-module">
                        <div class="sp-module-content">
                            <h3 align="center">
                                <?= Translate::_(
                                    'people',
                                    '{businessName} uses Identity Bank to protect your personal data. You now have the opportunity to create a personal account which you can use to check and edit the information that {businessName} holds for you.',
                                    ['businessName' => $businessAccountName]
                                ) ?>
                            </h3>
                            <h3 align="center">
                                <?= Translate::_(
                                    'people',
                                    'Click on ‘Create account’ to continue. If you already have a personal account, please log in.'
                                ) ?>
                            </h3>
                            <br>
                            <div class="row">
                                <div class="col-lg-12" style="float: none;margin: 0 auto;">
                                    <div class="wizard">
                                        <div class="wizard-inner">
                                            <div class="connecting-line"></div>
                                            <ul class="nav nav-tabs" role="tablist"></ul>
                                            <div class="field-block button-height wizard-controls clearfix">
                                                <div class="wizard-spacer" style="height:20px"></div>
                                                <?php $form = ActiveForm::begin(['id' => 'signupForm']); ?>
                                                <?= Html::submitButton(
                                                    Translate::_('people', 'Login'),
                                                    [
                                                        'class' => 'btn btn-lg login-btn mid-margin-right wizard-prev pull-left',
                                                        'name' => 'action',
                                                        'value' => 'login'
                                                    ]
                                                ); ?>
                                                <?= Html::submitButton(
                                                    Translate::_('people', 'Create account'),
                                                    [
                                                        'class' => 'btn btn-lg btn-success btn-arrow-right mid-margin-right wizard-next pull-right',
                                                        'name' => 'action',
                                                        'value' => 'register'
                                                    ]
                                                ); ?>
                                                <?php ActiveForm::end(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

