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

$this->registerCssFile("$assetUrl/assets/css/demo1/pages/general/login/login-2.css");
$this->registerJsFile(
    "$assetUrl/assets/vendors/general/inputmask/dist/min/jquery.inputmask.bundle.min.js",
    ['depends' => [JqueryAsset::className()]]
);

$this->title = Translate::_('people', 'Manage MFA Device');
$userId = ((empty(Yii::$app->user->identity->userId)) ? '' : Yii::$app->user->identity->userId);
$accountNumber = ((empty(Yii::$app->user->identity->accountNumber)) ? '' : Yii::$app->user->identity->accountNumber);

?>

<style>
    .kt-login.kt-login--v2 .kt-login__wrapper .kt-login__container .kt-login__head .kt-login__title,
    .kt-login.kt-login--v2 .kt-login__wrapper .kt-login__container .kt-form .kt-login__actions .kt-login__btn-primary,
    .kt-login.kt-login--v2 .kt-login__wrapper .kt-login__container .kt-form .kt-login__actions .kt-login__btn-secondary {
        color: #646c9a;
    }
</style>

<div class="kt-grid kt-grid--ver kt-grid--root">
    <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v2 kt-login--signin" id="create_mfa">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile"
             style="background-image: url(<?= $assetUrl ?>/assets/media/bg/bg-3.jpg);">
            <div class="kt-grid__item kt-grid__item--fluid kt-login__wrapper">
                <div class="kt-login__container">
                    <div class="kt-login__logo">
                        <img src="<?= $mainAssetAppUrl ?>images/idblogo.png" height="85px">
                    </div>
                    <div class="kt-login__signin">
                        <div class="kt-login__head">
                            <h3 class="kt-login__title"><?= $userId ?></h3>
                        </div>
                        <?php $form = ActiveForm::begin(
                            [
                                'fieldConfig' => [
                                    'template' => "{input}"
                                ],
                                'options' => ['class' => 'm-login__form kt-form']
                            ]
                        ); ?>

                        <div class="kt-mfa__msg"><?= Translate::_('people', 'Enter the code from your authenticator app')?></div>

                        <div class="form-group kt-form__group">
                            <?= $form->field($model, 'code')->textInput(
                                [
                                    'class' => 'form-control kt-input',
                                    'style' => 'text-align: center;',
                                    'placeholder' => Translate::_('people', 'MFA Code')
                                ]
                            ) ?>
                            <div class="kt-login__actions">
                                <?= Html::submitButton(
                                    Translate::_('people', 'Login'),
                                    [
                                        'class' => 'btn btn-brand btn-elevate kt-login__btn-primary',
                                        'id' => 'save'
                                    ]
                                ) ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>

                    <div class="kt-login__account">
                        <?= Html::a(Translate::_('people', 'MFA Recovery'), ['/mfarecovery/email-verification']) ?>
                    </div>
                    <div class="kt-login__account">
                        <a href="logout"><?= Translate::_('people', 'Or sign in as a different user') ?></a>
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
        var footer = document.getElementsByTagName("footer")[0];
        footer.style.marginLeft = "0px";
    }
    <?php $this->registerJs("initPage();", View::POS_END); ?>
</script>
