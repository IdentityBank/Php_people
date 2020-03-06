<?php

use app\assets\AppAsset;
use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();
$mainAssetBundle = AppAsset::register($this);
$mainAssetAppUrl = $mainAssetBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'assetAppUrl' => $assetAppUrl,
];

$this->title = $title;
$this->registerCssFile("$assetUrl/assets/css/demo1/pages/general/login/login-5.css");
$this->registerJsFile(
    "$assetUrl/assets/vendors/general/inputmask/dist/min/jquery.inputmask.bundle.min.js",
    ['depends' => [JqueryAsset::className()]]
);

?>

<style>
    .mfa-store-logo {
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }
</style>

<div class="kt-grid kt-grid--ver kt-grid--root">
    <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v5 kt-login--signin" id="create_mfa">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile"
             style="background-image: url(<?= $assetUrl ?>/assets/media/bg/bg-3.jpg);">
            <div class="kt-login__left">
                <div class="kt-login__wrapper">
                    <div class="kt-login__content">
                        <div class="kt-login__logo">
                            <img src="<?= $mainAssetAppUrl ?>images/idblogo.png" height="85px">
                        </div>
                        <div class="kt-login__title">
                            <h3><?= $userId ?></h3>
                        </div>
                        <div class="kt-login__desc">
                            <?= Translate::_('people', 'Run your virtual MFA app and scan the QR code.') ?>
                        </div>
                        <div class="kt-login__form-action">
                            <?= Html::img($mfaQr, ['alt' => 'MFA QR']) ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="min-height:50px;">
                                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                                   class="mfa-store-logo" style="right: 1%;"
                                   id="targurlgoogle" target="_blank">
                                    <img src="<?= $googleImg ?>"
                                         width="128" height="50"
                                         alt="Google Authenticator - Android Apps on Google Play">
                                </a>
                            </div>
                            <div class="col-md-6" style="min-height:50px;">
                                <a href="https://itunes.apple.com/app/google-authenticator/id388497605"
                                   class="mfa-store-logo" style="left: 45%;"
                                   id="targurlmac" target="_blank">
                                    <img src="<?= $appleImg ?>"
                                         width="117" height="34"
                                         alt="Google Authenticator on the App Store">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-login__divider">
                <div></div>
            </div>

            <div class="kt-login__right">
                <div class="kt-login__wrapper">
                    <div class="kt-login__signin">
                        <div class="kt-login__head">
                            <div class="kt-login__desc"><?= Translate::_(
                                    'people',
                                    'After the application is configured, provide two consecutive authentication codes to verify.'
                                ) ?>
                            </div>
                            <h3 class="kt-login__title"><br></h3>
                        </div>
                        <?php $form = ActiveForm::begin(
                            [
                                'fieldConfig' => [
                                    'template' => "{input}"
                                ]
                            ]
                        ); ?>

                        <div>
                            <?= $form->field($model, 'code')->textInput(
                                [
                                    'placeholder' => Translate::_('people', 'Authentication Code 1'),
                                    'style' => 'text-align: center;'
                                ]
                            ) ?>
                            <?= $form->field($model, 'code_next')->textInput(
                                [
                                    'placeholder' => Translate::_('people', 'Authentication Code 2'),
                                    'style' => 'text-align: center;'
                                ]
                            ) ?>
                            <?= Html::submitButton(
                                Translate::_('people', 'Authenticate Virtual MFA'),
                                [
                                    'class' => 'btn btn-success',
                                    'style' => 'text-align: center; width: 100%;',
                                    'id' => 'save'
                                ]
                            ) ?>
                            <?= $form->field($model, 'mfa')->hiddenInput()->label(false); ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <?php if (PeopleConfig::get()->isMfaSkipEnabled()) : ?>
                        <?php $form = ActiveForm::begin(['id' => 'skip-mfa-form']); ?>
                        <div>
                            <?= Html::hiddenInput('action', 'skip-mfa') ?>
                            <?= Html::submitButton(
                                Translate::_('people', 'Skip MFA'),
                                [
                                    'title' => Translate::_(
                                        'people',
                                        'We strongly recommend turning on Multi-Factor Authentication to protect your account.'
                                    ),
                                    'class' => 'btn btn-danger',
                                    'style' => 'text-align: center; width: 100%;',
                                    'id' => 'skip'
                                ]
                            ) ?>
                        </div><br>
                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                    <div class="kt-login__head">
                        <div class="kt-login__desc">
                            <a href="logout"><?= Translate::_('people', 'Or sign in as a different user') ?></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_footer', $params) ?>
<script>
    function initPage() {
        $("#dynamicmodel-code").inputmask({"mask": "999 999"});
        $("#dynamicmodel-code").focus();
        $("#dynamicmodel-code_next").inputmask({"mask": "999 999"});
        var footer = document.getElementsByTagName("footer")[0];
        footer.style.marginLeft = "0px";
    }
    <?php $this->registerJs("initPage();", View::POS_END); ?>
</script>
