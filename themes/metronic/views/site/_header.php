<?php

use yii\helpers\Url;

$params = [
    'assetUrl' => $assetUrl,
    'assetAppUrl' => $assetAppUrl,
];

?>

<div id="kt_header" class="kt-header  kt-header--fixed " data-ktheader-minimize="on">
    <div class="kt-container ">

        <div class="kt-header__brand   kt-grid__item" id="kt_header_brand">
            <a class="kt-header__brand-logo" href="<?= Url::toRoute(["/"]) ?>">
                <img alt="Logo" src="<?= $assetAppUrl ?>/images/logo.png" class="kt-header__brand-logo-default">
                <img alt="Logo" src="<?= $assetAppUrl ?>/images/logo.png" class="kt-header__brand-logo-sticky">
            </a>
        </div>

        <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_headerMenu', $params) ?>
        <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_headerTopbar', $params) ?>

    </div>
</div>
