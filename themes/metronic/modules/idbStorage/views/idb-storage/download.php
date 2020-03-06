<?php

use app\assets\FrontAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\Html;
use yii\helpers\Url;

$uploadAssets = FrontAsset::register($this);
$uploadAssets->idbStorageUpload();
$uploadAssets->idbStorageDownload();
$assetBundle = MetronicAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => Translate::_('people', 'Download'),
        'breadcrumbs' => [
            [
                'name' => \idbyii2\helpers\Translate::_('people', 'Shared files'),
                'action' => Url::toRoute(['index'], true)
            ],
            ['name' => Translate::_('people', 'Download')],
        ],
        'buttons' => [
            Html::a(
                Translate::_('people', 'Back'),
                ['files'],
                ['class' => 'btn kt-subheader__btn-secondary']
            )
        ]
    ]
];

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
                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body" style="text-align:center;">
                            <h2><?= $name ?></h2>
                            <h1 id="download-counter"><?= Translate::_('people', 'Download will start in few seconds.')?></h1>
                            <p><?= Translate::_('people', 'If download doesn\'t start then click button below')?></p>
                            <a style="max-width: 170px; margin: 0 auto;" download href="<?= $download ?>" class="btn btn-primary"><?= Translate::_('people', 'download') ?></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<?= PeopleConfig::jsOptions(
    [
        'downloadUrl' => $download,
        'secondsTxt' => ' ' . Translate::_('people', 'seconds') . '...',
        'downloadTxt' => Translate::_('people', 'Download will start in') . ' ',
        'startedTxt' => Translate::_('people', 'Download started!')
    ]
) ?>

