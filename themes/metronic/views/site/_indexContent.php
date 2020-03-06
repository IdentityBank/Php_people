<?php

use app\helpers\Translate;
use idbyii2\widgets\IconBox;
use yii\helpers\Url;

$accountName = ((empty(Yii::$app->user->identity->accountName)) ? '' : Yii::$app->user->identity->accountName);
$userId = ((empty(Yii::$app->user->identity->userId)) ? '' : Yii::$app->user->identity->userId);
if (empty($accountName)) {
    $accountName = strtoupper($userId);
}

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => ['title' => $accountName]
];

?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
<div class="kt-container">
    <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_alerts') ?>
</div>
<div class="kt-container  kt-grid__item kt-grid__item--fluid">
    <div class="row">

        <?= IconBox::widget(
            [
                'type' => 'brand',
                'icon' => 'flaticon-businesswoman',
                'title' => Translate::_('people', 'Who uses my data?'),
                'href' => Url::toRoute('business/who-uses', true)
            ]
        ); ?>
        <?= IconBox::widget(
            [
                'type' => 'success',
                'icon' => 'flaticon2-calendar-3',
                'title' => Translate::_('people', 'See all my data'),
                'href' => Url::toRoute('data/show-all', true)
            ]
        ); ?>
        <?= IconBox::widget(
            [
                'type' => 'warning',
                'icon' => 'flaticon-book',
                'title' => Translate::_('people', 'What\'s my data being used for?'),
                'href' => Url::toRoute('business/audit-log', true)
            ]
        ); ?>
        <?= IconBox::widget(
            [
                'type' => 'brand',
                'icon' => 'flaticon-file',
                'col' => '4 offset-md-4',
                'title' => Translate::_('people', 'Shared Files'),
                'href' => Url::toRoute('idb-storage/index', true)
            ]
        ); ?>

    </div>
</div>
