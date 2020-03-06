<?php

use app\assets\ICheckAsset;
use app\helpers\Translate;
use app\themes\idb\assets\IdbAsset;
use app\themes\idb\assets\IdbWizardAsset;
use idbyii2\helpers\StaticContentHelper;
use yii\bootstrap\ActiveForm;
use yii\web\View;

$this->title = Translate::_('people', 'Privacy Notice');
$idbAsset = IdbAsset::register($this);
$icheck = ICheckAsset::register($this);
$wizardAsset = IdbWizardAsset::register($this);
$privacyContent = StaticContentHelper::getPrivacyNotice(Yii::$app->language);
$privacyDateTime = strtok($privacyContent, "\n");
$privacyContent = preg_replace('/^.+\n/', '', $privacyContent);
const SEND_EMAIL_ENABLED = false;

?>

<div class="container">
    <div class="container-inner">
        <?= $this->render('partials/_steps', ['wizardAsset' => $wizardAsset, 'count' => 3]); ?>
        <?php $form = ActiveForm::begin(['id' => 'signupForm']); ?>
        <div class="row">
            <div class="col-lg-12" style="background: white;">
                <div class="sp-column">
                    <div class="sp-module">
                        <div class="sp-module-content" style="color: black;">
                            <div class="invoice">
                                <div style="display: block;background-color: #ffffff; padding: 10px 10px 10px 10px">
                                    <div class="row">
                                        <div class="col-xs-12">

                                            <h3 class="page-header">
                                                <i class="fa fa-globe"></i> <?= Translate::_(
                                                    'people',
                                                    'Privacy Notice'
                                                ) ?>
                                                <small class="pull-right"><?= $privacyDateTime ?></small>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12" id="tac-textarea"
                                             style="overflow-y: scroll; height:300px;">
                                            <?= $privacyContent ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2 class="page-header"></h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="alert alert-wizard">
                                        <strong>
                                            <?= Translate::_(
                                                'people',
                                                'Please read our Term and Conditions as given above.'
                                            ) ?>
                                        </strong>
                                        <strong id="tac-scroll-info">
                                            <br>
                                            <?= Translate::_(
                                                'people',
                                                'You have to scroll to the bottom to continue.'
                                            ) ?>
                                        </strong>
                                        <div id="tac-actions" style="display: none;">
                                            <label>
                                                <input type="checkbox" name="TermsAndConditionsAgreement"
                                                       aria-required="true" required/>&nbsp;
                                                <?= Translate::_(
                                                    'people',
                                                    'When you have done this, and are in agreement, click on the checkbox and then the green arrow to proceed.'
                                                ) ?>
                                                &nbsp;
                                            </label>
                                            <?php if (SEND_EMAIL_ENABLED): ?>
                                                <label>
                                                    <?= Translate::_(
                                                        'people',
                                                        'Send me contents of term and condition into my email.'
                                                    ) ?>
                                                    &nbsp;
                                                    <input type="checkbox" name="SendTermsAndConditions" checked/>&nbsp;
                                                </label>
                                            <?php endif ?>

                                        </div>
                                    </div>
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
                        'Text' => Translate::_('people', 'Continue account setup'),
                        'Action' => 'Submit',
                        'Id' => 'tac-buttons-next',
                        'Help' => Translate::_('people', 'Continue')
                    ],
                    [
                        'Text' => Translate::_('people', 'Cancel account setup'),
                        'Action' => ['/signup/error'],
                        'Help' => Translate::_(
                            'people',
                            'Click on the disagree option if you do not wish to continue your signup to Identity Bank.'
                        )
                    ]
                ) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
    function dropFooterFixedBottom() {
        var footer = document.getElementById("sp-footer");
        footer.classList.remove("navbar-fixed-bottom");
    }

    function initCheckBox() {
        $('#tac-buttons-next').attr("disabled", true);
        $('#tac-textarea').on('scroll', function () {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                $('#tac-actions').show();
                $('#tac-scroll-info').hide();
                $('#tac-buttons-next').attr("disabled", false);
            }
        });
        $('input[type=\"checkbox\"], input[type=\"radio\"]').iCheck({
            checkboxClass: 'icheckbox_flat-orange',
            radioClass: 'icheckbox_flat-orange'
        })
    }

    <?php $this->registerJs("dropFooterFixedBottom();initCheckBox();", View::POS_END); ?>
</script>
