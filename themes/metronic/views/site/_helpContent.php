<?php

use app\helpers\Translate;

$accountName = ((empty(Yii::$app->user->identity->accountName)) ? '' : Yii::$app->user->identity->accountName);
$userId = ((empty(Yii::$app->user->identity->userId)) ? '' : Yii::$app->user->identity->userId);
if (empty($accountName)) {
    $accountName = strtoupper($userId);
}

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => Translate::_('people', 'Help'),
        'breadcrumbs' => [
            [
                'name' => Translate::_('people', 'Help'),
            ]
        ],
    ],
];

?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid">
    <div class="row">


    </div>
</div>
