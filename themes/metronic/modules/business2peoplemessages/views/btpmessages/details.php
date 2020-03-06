<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Translate::_('people', 'Message details');
$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            [
                'name' => Translate::_('people', 'Message'),
            ]
        ],
        'buttons' => [
            Html::submitButton(
                Translate::_('people', 'Delete'),
                [
                    'class' => 'btn btn-danger kt-subheader__btn-options fix-line-height',
                    'data-confirm' => Translate::_('people', 'Delete this message?')
                ]
            ),
            Html::a(
                Translate::_('people', 'Dismiss'),
                $dismissLink,
                ['class' => ' btn kt-subheader__btn-secondary']
            )
        ]
    ],

];

$messageContent = json_decode($message->messagecontent, true);

?>
<form action="<?= Url::toRoute(['/business2peoplemessages/btpmessages/delete']) ?>" method="get">
    <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
    <div class="kt-container  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__body kt-portlet__body--fit">

                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <span class="kt-portlet__head-icon">
                                        <i class="flaticon2-envelope"></i>
                                    </span>
                                    <h3 class="kt-portlet__head-title">
                                        <?= $messageContent['subject'] ?? '' ?>
                                    </h3>
                                </div>
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        <small><?= $message->business_user ?></small>
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="kt-widget4">
                                    <?= $messageContent['message'] ?? '' ?>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="kt-widget4">
                                    <input type="hidden" name="id" value="<?= $message->id ?>">
                                    <input type="hidden" name="people_user" value="<?= $message->people_user ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

