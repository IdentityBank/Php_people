<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use idbyii2\widgets\VerificationCodeView;
use yii\bootstrap\ActiveForm;
use yii\helpers\{Html, Url};

$params = [
    'assetUrl' => $assetUrl
];

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'assetAppUrl' => $assetAppUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            [
                'name' => Translate::_('people', 'My profile'),
                'action' => Url::toRoute(['/idbuser/profile'], true)
            ],
            [
                'name' => Translate::_('people', 'Change password'),
            ]
        ],
        'buttons' => [
            Html::submitButton(
                Translate::_('people', 'Next'),
                ['class' => 'btn kt-subheader__btn-secondary fix-line-height']
            )
        ]
    ]
];

?>
<?php $form = ActiveForm::begin(); ?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
<div class="kt-container">
    <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_alerts') ?>
</div>
<div class="kt-container  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                <div class="kt-portlet__body kt-portlet__body--fit">

                    <div class="kt-portlet idb-no-margin">
                        <div class="kt-portlet__body">
                            <div class="kt-widget4">
                                <?php if ($model->getErrors()) { ?>
                                    <?= Html::tag(
                                        'div',
                                        $form->errorSummary($model),
                                        ['class' => 'alert alert-danger']
                                    ) ?>
                                <?php } ?>
                                <div class="jumbotron" style="background-color: white; text-align:center;">
                                    <h2><?= Translate::_(
                                            'people',
                                            'You have been sent a code to the email address you provided.'
                                        ) ?></h2>
                                    <br>
                                    <hr>
                                    <br>
                                    <h2 align="center"><?= Translate::_(
                                            'people',
                                            'Enter the missing digits from the email code.'
                                        ) ?></h2>

                                    <?= VerificationCodeView::widget(['code' => [0 => $codeFirst, 2 => $codeThird]]) ?>
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
    </div>
</div>

<?php ActiveForm::end(); ?>
