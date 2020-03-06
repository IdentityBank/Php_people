<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use idbyii2\widgets\IconBox;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataSetProvider yii\data\ActiveDataProvider */

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetAppBundle->tooltipAssets();
$assetAppBundle->businessIndexAssets();
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            ['name' => $this->title]
        ]
    ]
];

$counter = 1;
?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid">
    <div class="row">

        <?php foreach ($data as $model): ?>
        <?= IconBox::widget(
            [
                'icon' => 'flaticon-businesswoman',
                'random' => 'true',
                'col' => 3,
                'type' => 'brand',
                'href' => Url::toRoute(['files'], true),
                'title' => $model['name'],
                'data' => [
                    'method' => 'post',
                    'params' => [
                        'oid' => $model['oid'],
                        'aid' => $model['aid'],
                        'dbid' => $model['dbid'],
                        'uid' => $model['uid']
                    ],
                ],


            ]
        ) ?>
        <?php if ($counter % 4 == 0): ?>
    </div>
    <div class="row">
        <?php endif; ?>
        <?php $counter++; ?>
        <?php endforeach; ?>
    </div>
</div>


