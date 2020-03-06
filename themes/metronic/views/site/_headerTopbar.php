<?php

$params = [
    'assetUrl' => $assetUrl,
    'assetAppUrl' => $assetAppUrl
];

?>

<div class="kt-header__topbar kt-grid__item">

    <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_headerTopbarHelp', $params) ?>
    <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_headerTopbarNotifications', $params) ?>
    <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_headerTopbarUser', $params) ?>

</div>
