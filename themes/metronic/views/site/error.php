<?php

use app\assets\MetronicAsset;
use app\helpers\Translate;

$errorStyle = PeopleConfig::get()->getThemeErrorPage();

$assetBundle = MetronicAsset::register($this);
$assetUrl = $assetBundle->layoutError($this, $errorStyle);
$assetUrl = $assetBundle->getAssetUrl();

$exception = Yii::$app->errorHandler->exception;

if ($exception) {
    $statusCode = ((empty($exception->statusCode)) ? null : $exception->statusCode);
    $name = $exception->getName();
    $message = $exception->getMessage();
}
$this->title = Translate::_('people', 'Error') . " - $name";
?>

<div class="kt-grid kt-grid--ver kt-grid--root kt-page">
    <?php if ($errorStyle == 1) { ?>
        <div class="kt-grid__item kt-grid__item--fluid kt-grid  kt-error-v1"
             style="background-image: url(<?= $assetUrl ?>assets/media/error/bg1.jpg);">
            <div class="kt-error-v1__container">
                <h1 class="kt-error-v1__number"><?= $statusCode ?></h1>
                <p class="kt-error-v1__desc"><?= $message ?></p>
            </div>
        </div>
    <?php } elseif ($errorStyle == 2) { ?>
        <div class="kt-grid__item kt-grid__item--fluid kt-grid  kt-error-v2"
             style="background-image: url(<?= $assetUrl ?>assets/media/error/bg2.jpg);">
            <div class="kt-error_container">
                <span class="kt-error_title2 kt-font-light"><h1><?= $statusCode ?></h1></span>
                <span class="kt-error_desc kt-font-light"><?= $message ?></span>
            </div>
        </div>
    <?php } elseif ($errorStyle == 3) { ?>
        <div class="kt-grid__item kt-grid__item--fluid kt-grid  kt-error-v3"
             style="background-image: url(<?= $assetUrl ?>assets/media/error/bg3.jpg);">
            <div class="kt-error_container">
                <span class="kt-error_number"><h1><?= $statusCode ?></h1></span>
                <p class="kt-error_title kt-font-light"><?= $message ?></p>
                <p class="kt-error_subtitle"></p>
                <p class="kt-error_description"></p>
            </div>
        </div>
    <?php } elseif ($errorStyle == 4) { ?>
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-error-v4"
             style="background-image: url(<?= $assetUrl ?>assets/media/error/bg4.jpg);">
            <div class="kt-error_container">
                <h1 class="kt-error_number"><?= $statusCode ?></h1>
                <p class="kt-error_title"></p>
                <p class="kt-error_description"><?= $message ?></p>
            </div>
        </div>
    <?php } elseif ($errorStyle == 5) { ?>
        <div class="kt-grid__item kt-grid__item--fluid kt-grid  kt-error-v5"
             style="background-image: url(<?= $assetUrl ?>assets/media/error/bg5.jpg);">
            <div class="kt-error_container">
                <span class="kt-error_title"><h1><?= $statusCode ?></h1></span>
                <p class="kt-error_subtitle"><?= $message ?></p>
                <p class="kt-error_description"></p>
            </div>
        </div>
    <?php } elseif ($errorStyle == 6) { ?>
        <div class="kt-grid__item kt-grid__item--fluid kt-grid  kt-error-v6"
             style="background-image: url(<?= $assetUrl ?>assets/media/error/bg6.jpg);">
            <div class="kt-error_container">
                <div class="kt-error_subtitle kt-font-light"><h1><?= $statusCode ?></h1></div>
                <p class="kt-error_description kt-font-light"><?= $message ?></p>
            </div>
        </div>
    <?php } ?>
</div>
