<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

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

?>

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
                            <?php Pjax::begin(); ?>
                            <?= GridView::widget(
                                [
                                    'dataProvider' => $dataProvider,
                                    'showHeader' => false,
                                    'columns' => [
                                        [
                                            'class' => 'yii\grid\CheckboxColumn',
                                            'contentOptions' => [
                                                'style' => 'width: 30px; text-align: center;',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'title' => Translate::_('people', 'Select a business to map.')
                                            ],
                                            'checkboxOptions' => function ($model) {
                                                return [
                                                    'value' => $model['oid'],
                                                    'class' => 'map-checkbox'
                                                ];
                                            }
                                        ],
                                        'name:ntext',
                                        [
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{map} {edit} {audit-log} {delete}',
                                            'contentOptions' => ['style' => 'width: 120px; text-align:center;'],
                                            'buttons' => [
                                                'map' => function (
                                                    $url,
                                                    $model,
                                                    $key
                                                ) {     // render your custom button
                                                    return Html::a(
                                                        '<i class="flaticon-map"></i>',
                                                        '#',
                                                        [
                                                            'class' => 'button-map-to',
                                                            'data-oid' => $model['oid'],
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'top',
                                                            'title' => Translate::_(
                                                                'people',
                                                                'Map data (You need select businesses first)'
                                                            )
                                                        ]

                                                    );
                                                },
                                                'edit' => function (
                                                    $url,
                                                    $model,
                                                    $key
                                                ) {     // render your custom button
                                                    return Html::a(
                                                        '<i class="flaticon-edit"></i>',
                                                        Url::toRoute(['business/edit'], true),
                                                        [
                                                            'data' => [
                                                                'method' => 'post',
                                                                'params' => [
                                                                    'oid' => $model['oid'],
                                                                    'aid' => $model['aid'],
                                                                    'dbid' => $model['dbid'],
                                                                    'uid' => $model['uid']
                                                                ],
                                                            ],
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'top',
                                                            'title' => Translate::_(
                                                                'people',
                                                                'Edit data stored by a business'
                                                            )
                                                        ]
                                                    );
                                                },

                                                'audit-log' => function (
                                                    $url,
                                                    $model,
                                                    $key
                                                ) {     // render your custom button
                                                    return Html::a(
                                                        '<i class="flaticon-book"></i>',
                                                        Url::toRoute(['business/audit-log'], true),
                                                        [
                                                            'data' => [
                                                                'method' => 'post',
                                                                'params' => [
                                                                    'oid' => $model['oid'],
                                                                    'aid' => $model['aid'],
                                                                    'dbid' => $model['dbid'],
                                                                    'uid' => $model['uid']
                                                                ],
                                                            ],
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'top',
                                                            'title' => Translate::_('people', 'Who uses my data?')
                                                        ]
                                                    );
                                                },
                                                'delete' => function (
                                                    $url,
                                                    $model,
                                                    $key
                                                ) {     // render your custom button
                                                    return Html::a(
                                                        '<i class="flaticon-delete"></i>',
                                                        Url::toRoute(['business/delete'], true),
                                                        [
                                                            'data' => [
                                                                'method' => 'post',
                                                                'confirm' => Translate::_(
                                                                    'people',
                                                                    'Are you sure you want to delete this connection?'
                                                                ),
                                                                'params' => [
                                                                    'oid' => $model['oid'],
                                                                    'aid' => $model['aid'],
                                                                    'dbid' => $model['dbid'],
                                                                    'uid' => $model['uid']
                                                                ],
                                                            ],
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'top',
                                                            'title' => Translate::_('people', 'Remove business data')

                                                        ]
                                                    );
                                                },
                                            ]
                                        ]
                                    ],
                                ]
                            ); ?>
                            <?php Pjax::end(); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<form method="post" id="map-form" action="<?= Url::to(['business/map-data'], true) ?>">
    <input type="hidden" id="map-params" name="params" value=""/>
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>"/>
</form>
<script>
    var data = '<?= json_encode($dataProvider->getModels()) ?>';

    if (data !== '') {
        data = JSON.parse(data);
    }
</script>
