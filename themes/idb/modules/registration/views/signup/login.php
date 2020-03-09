<?php

use app\helpers\Translate;
use app\themes\idb\assets\IdbWizardAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'Complete connection to {businessName}', compact('businessName'));

?>

<div class="container">
    <div class="container-inner">
        <?= $this->render('partials/_steps', ['wizardAsset' => $wizardAsset, 'count' => 5]); ?>
        <?php $form = ActiveForm::begin(
            [
                'id' => 'signupForm',
            ]
        ); ?>
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
                                        <?= Translate::_('people', 'Contact administrator or signup here') ?>
                                    </div>
                                <?php endif; ?>

                                <h3 style="align-content: center">
                                    <?= Translate::_(
                                        'people',
                                        'To finish making the connection to {businessName} enter your password and click on ‘Complete connection’',
                                        compact('businessName')
                                    ) ?>
                                </h3>

                                <?= $form->field($model, 'userId', ['enableClientValidation' => false])->textInput(
                                    [
                                        'placeholder' => $model->getAttributeLabel('userId'),
                                        'readonly' => !empty($model->userId)
                                    ]
                                ) ?>
                                <?= $form->field($model, 'accountNumber')->textInput(
                                    [
                                        'placeholder' => $model->getAttributeLabel('accountNumber'),
                                        'readonly' => !empty($model->accountNumber)
                                    ]
                                ) ?>
                                <?= $form->field($model, 'accountPassword')->passwordInput(
                                    ['placeholder' => $model->getAttributeLabel('accountPassword')]
                                ) ?>
                                <?php if (PeopleConfig::get()->getYii2PeopleEnableAutoLogin()) { ?>
                                    <?= $form->field($model, 'rememberMe')->checkbox(
                                        ['template' => "{input} {label}\n{error}",]
                                    )->label(
                                        $model->getAttributeLabel('rememberMe')
                                    ) ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-12" style="float: none;margin: 0 auto;">
                <div class="wizard">
                    <div class="wizard-inner">
                        <div class="connecting-line"></div>
                        <ul class="nav nav-tabs" role="tablist"></ul>
                        <div class="field-block button-height wizard-controls clearfix">
                            <div class="wizard-spacer" style="height:20px"></div>
                            <?= Html::submitButton(
                                Translate::_('people', 'Complete connection'),
                                ['class' => 'btn btn-lg btn-success btn-arrow-right mid-margin-right wizard-next pull-right']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

