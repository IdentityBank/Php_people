<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;

if (empty($params)):
    Yii::$app->end();
endif;

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();
$params = array_merge(
    $params,
    [
        'menu_active_section' => '[menu][site]',
        'menu_active_item' => '[menu][site][index]',
        'assetUrl' => $assetUrl,
        'assetAppUrl' => $assetAppUrl,
    ]
);
?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_template', $params) ?>
