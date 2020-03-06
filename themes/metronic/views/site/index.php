<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();
$params = [
    'content' => '_indexContent',
    'menu_active_section' => '[menu][site]',
    'menu_active_item' => '[menu][site][index]',
    'assetUrl' => $assetUrl,
    'assetAppUrl' => $assetAppUrl,
];

$this->title = Translate::_('people', 'Control panel');
?>
<?= Yii::$app->controller->renderPartial('_template', $params) ?>
