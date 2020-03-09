<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
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
                'name' => Translate::_('people', 'Change contact data'),
            ]
        ],
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
                            <div class="kt-widget4" style="text-align: center;">
                                <h3 style="padding: 30px;"><?= Translate::_(
                                        'people',
                                        'Your contact details has been successfully modified. Before you leave this page , please take a moment to download, print, and securely store your printed account password recovery token.'
                                    ) ?></h3>
                                <?=
                                Html::a(
                                    '<i class="fa far fa-save"></i> ' . Translate::_(
                                        'people',
                                        'Download'
                                    ),
                                    ['get-token'],
                                    [
                                        'class' => 'btn btn-danger btn-xl',
                                        'id' => "id-button-recovery-token",
                                        'target' => '_blank',
                                        'style' => 'margin-bottom: 30px;',
                                        'data-toggle' => 'tooltip',
                                        'title' => Translate::_(
                                            'people',
                                            'Will open the generated Password token in new window'
                                        )
                                    ]
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
