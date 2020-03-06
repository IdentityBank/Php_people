<?php

use idbyii2\widgets\Loading;

$params = [
    'assetUrl' => $assetUrl,
    'assetAppUrl' => $assetAppUrl,
];
if (!empty($contentParams) && is_array($contentParams)) {
    foreach ($contentParams as $key => $value) {
        $params[$key] = $value;
    }
}

?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_header_mobile', $params) ?>
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">


            <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_header', $params) ?>

            <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
                <div class="kt-content kt-content--fit-top  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor"
                     id="kt_content">
                    <?= Yii::$app->controller->renderPartial($content, $params) ?>
                </div>
            </div>
            <?= Loading::widget(['preventReady' => $params['preventReady'] ?? false,'preventLoading' => $params['preventLoading'] ?? false]) ?>
            <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_footer', $params) ?>
        </div>
    </div>
</div>


<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>
