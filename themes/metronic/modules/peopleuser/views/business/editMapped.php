<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\Url;


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
                'name' => \idbyii2\helpers\Translate::_('people', 'Connected businesses'),
                'action' => Url::toRoute(['business/index'], true)
            ],
            ['name' => $this->title]
        ],
        'buttons' => [
            '<button type="submit" class="btn kt-subheader__btn-secondary fix-line-height">'
            . Translate::_('people', 'Save')
            . '</button>'
        ]
    ]
];

?>
<form method="post">
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
                            <div class="kt-portlet__body">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
                                       value="<?= Yii::$app->request->csrfToken; ?>"/>
                                <table class="edit-table">
                                    <?php foreach ($metadata['map'] as $mapKey => $map): ?>
                                        <tr>
                                            <td><strong><?= $mapKey ?></strong>:</td>
                                            <td><input type="text" name="<?= $mapKey ?>"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

