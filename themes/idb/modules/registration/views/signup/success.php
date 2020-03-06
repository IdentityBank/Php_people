<?php

use app\assets\AppFormAsset;
use app\assets\ICheckAsset;
use app\helpers\Translate;
use app\themes\idb\assets\IdbAsset;
use app\themes\idb\assets\IdbWizardAsset;
use yii\helpers\Html;
use yii\web\View;

$idbAsset = IdbAsset::register($this);
$icheck = ICheckAsset::register($this);
$formAsset = AppFormAsset::register($this);
$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'Account signup complete!');

?>

<div class="container">
    <div class="container-inner">
        <?= $this->render('partials/_steps', ['wizardAsset' => $wizardAsset, 'count' => 5]); ?>
        <div class="row">
            <div class="col-lg-12" style="float: none;margin: 0 auto;">
                <div class="sp-column"
                     style="display: block;background-color: #ffffff; border: 5px solid #EAEDF1; padding: 30px;">
                    <div class="sp-module">
                        <div class="sp-module-content">
                            <h2 class="page-header" style="margin-top: 20px;">
                                <b><?= Translate::_('people', 'Thank you for your registration!') ?></b>
                            </h2>
                            <p style="margin-bottom:25px;">
                                <?= Translate::_(
                                    'people',
                                    'Your account has been successfully created and is now ready for you to login. Before you login to your account, please take a moment to save your account details as shown below and download, print, and securely store your printed account password recovery token.'
                                ) ?>
                            </p>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-2" align="right">
                                        <button id="copy-login-button" class="btn btn-app bg-dark">
                                            <i style="font-size: 28px;"
                                               class="glyphicon glyphicon-copy"></i><br/> <?= Translate::_(
                                                'people',
                                                'Copy to clipboard'
                                            ) ?>
                                        </button>
                                    </div>
                                    <div class="col-xs-7">
                                        <p style="margin-top: 8px;"><b><?= Translate::_('people', 'Login name') ?>
                                                :</b> <?= $login->userId ?></p>
                                        <p><b><?= Translate::_('people', 'Account number') ?>
                                                :</b> <?= $login->accountNumber ?></p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><b><?= Translate::_('people', 'Password Recovery Token') ?>:</b></p>
                            <div class="row">
                                <div class="col-xs-3" align="center" style="padding: 15px;">
                                    <?=
                                    Html::a(
                                        '<i class="glyphicon glyphicon-floppy-disk"></i> ' . Translate::_(
                                            'people',
                                            'Download'
                                        ),
                                        ['get-token', 'id' => $id],
                                        [
                                            'class' => 'btn btn-danger btn-block',
                                            'id' => "id-button-recovery-token",
                                            'target' => '_blank',
                                            'data-toggle' => 'tooltip',
                                            'title' => Translate::_(
                                                'people',
                                                'Will open the generated Password token in new window'
                                            )
                                        ]
                                    );
                                    ?>
                                </div>
                                <div class="col-xs-9" id="id-confirmation-recovery-token" style="padding: 15px;">
                                    <label>
                                        <input type="checkbox" id="id-password-recovery-token-downloaded"
                                               name="PasswordRecoveryTokenDownloaded"
                                               aria-required="true" required/>&nbsp;
                                        <?= Translate::_(
                                            'people',
                                            'I have downloaded the Password Recovery Token, printed it out, and securely stored the printed copy. I have permanently deleted all copies of the downloaded PDF file from my devices.'
                                        ) ?>
                                        &nbsp;
                                    </label>
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
                <div class="wizard">
                    <ul class="nav nav-tabs" role="tablist"></ul>
                </div>
                <?php $buttonText = Translate::_(
                    'people',
                    'Click here to proceed and login to your account'
                ); ?>
                <?php $buttonHint = Translate::_(
                    'people',
                    'You must confirm the action above and click the check box to continue.'
                ); ?>
                <?= Html::a(
                    $buttonText,
                    ['login', 'id' => $id],
                    [
                        'id' => 'login-portal-button',
                        'class' => 'btn btn-warning btn-lg btn-block',
                        'style' => 'margin-top: 5px; display: none;'
                    ]
                ) ?>
                <?= Html::button(
                    $buttonText,
                    [
                        'id' => 'login-portal-button-not-active',
                        'class' => 'btn btn-warning btn-lg btn-block',
                        'style' => 'margin-top: 5px;',
                        'onmouseover' => 'clickNotActive("onmouseover")',
                        'onmouseout' => 'clickNotActive("onmouseout")',
                        'title' => $buttonHint
                    ]
                ) ?>
            </div>
        </div>

    </div>
</div>

<script>
    function clickNotActive(action) {
        if (action == "onmouseover") {
            $('#id-confirmation-recovery-token').addClass("alert-danger");
        } else {
            $('#id-confirmation-recovery-token').removeClass("alert-danger");
        }
    }

    function copyLoginData() {
        let textArea = document.createElement("textarea");
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.padding = 0;
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';
        textArea.style.background = 'transparent';
        textArea.style.textAlign = 'middle';
        textArea.value = '<?= Translate::_('people', 'Login name') . ': ' . $login->userId . '\\n' ?>';
        textArea.value += '<?= Translate::_('people', 'Account number') . ': ' . $login->accountNumber ?>';
        document.body.appendChild(textArea);

        textArea.select();
        document.execCommand('copy');

        document.body.removeChild(textArea);

        $('#copy-login-button').prop('disabled', true);
        $('#copy-login-button').removeClass("bg-dark").addClass("bg-green-active");


        setTimeout(function () {
            $('#copy-login-button').removeClass("bg-green-active").addClass("bg-dark");
            $('#copy-login-button').prop('disabled', false);
        }, 10000);
    }

    function initForm() {
        $('#login-portal-button-not-active').attr("disabled", true);
        $("#copy-login-button").click(function (event) {
            event.preventDefault();
            copyLoginData();
        });
        $('input[type=\"checkbox\"], input[type=\"radio\"]').iCheck({
            checkboxClass: 'icheckbox_flat-orange',
            radioClass: 'iradio_flat-orange'
        });
        $('#id-password-recovery-token-downloaded').on("ifChanged", function () {
            if (this.checked) {
                $('#login-portal-button-not-active').hide();
                $('#login-portal-button').show();
                $('#id-password-recovery-token-downloaded').prop("disabled", true);
            }
        });
    }
    <?php $this->registerJs("initForm();", View::POS_END); ?>
</script>
