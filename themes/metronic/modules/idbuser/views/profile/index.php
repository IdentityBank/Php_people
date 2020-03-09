<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\Html;

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
            ['name' => Translate::_('people', 'My profile')]
        ],
        'buttons' => [
            Html::a(
                Translate::_('people', 'Change contact details'),
                ['change-contact'],
                ['class' => 'btn kt-subheader__btn-secondary fix-line-height']
            ),
            Html::a(
                Translate::_('people', 'Change password'),
                ['change-password'],
                ['class' => 'btn btn-danger kt-subheader__btn-options']
            )
        ]
    ]
];

function newItem($title, $value)
{
    return '
    <div class="kt-widget4__item">
        <div class="kt-widget4__info">
            <span class="kt-widget4__username">' . $title . '</span><br>
            <span class="kt-widget4__text">' . $value . '</span><br>
        </div>
    </div>
';
}

$script = "
$('#delete-button').click(function(){
$('#static').modal('show')});
";
$this->registerJs($script, yii\web\View::POS_END);


?>

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
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"><i class="la la-user"></i></span>
                                <h3 class="kt-portlet__head-title">
                                    <?= $name . " " . $surname ?>
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="kt-widget4">
                                <?= newItem(Translate::_('people', 'Login name'), $userId) ?>
                                <?= newItem(Translate::_('people', 'Account number'), $accountNumber) ?>
                                <?= newItem(Translate::_('people', 'Email'), $email) ?>
                                <?= newItem(Translate::_('people', 'Mobile phone'), $phone) ?>
                            </div>
                            <hr>
                            <div class="kt-widget12">
                            <?php if($hasRequest): ?>
                                <h4 class="text-center delete-button--txt">
                                    <?= Translate::_(
                                            'people',
                                            'Your personal Identity Bank account will be deleted in {days} day(s). Click this button to keep my account and not delete it.',
                                            compact('days')
                                    ) ?>
                                </h4>
                                <?= Html::a(
                                    Translate::_('people', 'Keep my personal Identity Bank account'),
                                    ['delete-account'],
                                    [
                                        'onclick' => "return confirm('" . Translate::_(
                                                'people',
                                                'Do you want to cancel account removing process?'
                                            ) . "');",
                                        'class' => 'btn btn-danger'
                                    ]
                                ) ?>
                            <?php else: ?>
                                <?= Html::button(Translate::_('people', 'Delete my personal Identity Bank account'),
                                    [
                                            'id' => 'delete-button',
                                            'class' => 'btn btn-danger',
                                    ]) ?>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="static" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><span class="modal-title"><?= Translate::_('people', 'You have decided to close your Identity Bank account.'); ?><span></h4>
            </div>
            <div class="modal-body">
                <p> <?= Translate::_('people', 'This action will delete all connections you have with businesses. When you do this we immediately delete all data we store about you from your personal account. However, we cannot delete data that is held about you by businesses. You can do this before you close your account by using the Delete All Data option and then closing your account.') ?> </p>
            </div>
            <div class="modal-footer">
                <?= Html::a(
                    Translate::_('people', 'Keep my account open'),
                    ['/business/who-uses'],
                    [
                        'onclick' => "return confirm('" . Translate::_(
                                'people',
                                'Do you want to cancel account removing process?'
                            ) . "');",
                        'class' => 'btn btn-success'
                    ]
                ) ?>
                <?= Html::a(
                    Translate::_('people', 'Continue to delete my account'),
                    ['delete-account'],
                    [
                        'onclick' => "return confirm('" . Translate::_(
                                'people',
                                'Do you want to continue account removing process?'
                            ) . "');",
                        'class' => 'btn btn-danger'
                    ]
                ) ?>
                <!-- <button type="button" data-dismiss="modal" class="btn btn-success"> //Translate::_('people', 'Keep my account open')></button>
                <button type="button" data-dismiss="modal" class=" btn btn-danger"> Translate::_('people', 'Continue to delete my account')></button> -->
            </div>
        </div>
    </div>
</div>