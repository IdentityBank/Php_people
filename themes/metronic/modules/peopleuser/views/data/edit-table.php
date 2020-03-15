<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use idbyii2\helpers\Translate;
use yii\grid\GridView;
use yii\helpers\{Html, Url};

/* @var $this yii\web\View */
/* @var $model idbyii2\models\db\PeopleDataType */

$this->title = Translate::_('people', 'Manage permissions to use my data');
$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetAppBundle->tooltipAssets();
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$oid = Yii::$app->session->get('oid');
$aid = Yii::$app->session->get('aid');
$dbid = Yii::$app->session->get('dbid');
$uid = Yii::$app->session->get('uid');

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            [
                'name' => Translate::_('people', 'See all my data'),
                'action' => Url::toRoute('/data/show-all', true)
            ],
            ['name' => $this->title],
        ],
        'buttons' => [
            Html::a(
                Translate::_('people', 'Send to business'),
                ['data/send-to-businesses'],
                ['class' => ' btn kt-subheader__btn-secondary']
            )
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


                    <div class="kt-portlet idb-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">
                            <?= GridView::widget(
                                [
                                    'dataProvider' => $dataProvider,
                                    'showHeader' => true,
                                    'summary'=>'',
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],

                                        [
                                            'header' => Translate::_('people', 'Business using this data'),
                                            'value' => function ($model, $index, $widget) {
                                                return $model['businessName'];
                                            }
                                        ],
                                        [
                                            'header' => Translate::_('people', 'Data required by business'),
                                            'attribute' => 'required',
                                            'format' => 'raw',
                                            'value' => function ($model, $index, $widget) {
                                                return Html::checkbox(
                                                    'required[]',
                                                    $model['required'],
                                                    ['value' => $index, 'disabled' => true]
                                                );
                                            },
                                        ],
                                        'value:ntext',
                                        'new_value:ntext',
                                        [
                                            'attribute' => 'delete',
                                            'format' => 'raw',
                                            'value' => function ($model, $index, $widget) {
                                                return Html::checkbox(
                                                    'delete[]',
                                                    $model['delete'],
                                                    [
                                                        'value' => $index,
                                                        'disabled' => true,
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'top',
                                                        'title' => Translate::_(
                                                            'people',
                                                            'Select data to remove on send'
                                                        )
                                                    ]
                                                );
                                            },
                                        ],
                                        [
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{edit} {delete}',
                                            'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
                                            'buttons' => [
                                                'edit' => function ($url, $model, $key) {
                                                    return Html::a(
                                                        '<i class="flaticon-edit"></i>',
                                                        Url::to(['edit-data']),
                                                        [
                                                            'data' => [
                                                                'method' => 'post',
                                                                'params' => [
                                                                    'display_name' => $model['display_name'],
                                                                    'value' => $model['value'],
                                                                    'column' => $model['column'],
                                                                ],
                                                            ],
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'top',
                                                            'title' => Translate::_('people', 'Edit data')
                                                        ]
                                                    );
                                                },
                                                'delete' => function ($url, $model, $key) {
                                                    if ($model['required']) {
                                                        return Html::a(
                                                            '<i class="flaticon-delete"></i>',
                                                            Url::toRoute(['data/delete-data'], true),
                                                            [
                                                                'data' => [
                                                                    'method' => 'post',
                                                                    'confirm' => Translate::_(
                                                                        'people',
                                                                        'You are going to delete required data for this business. This action will delete your account from this business. Are you sure you want to do this?'
                                                                    ),
                                                                    'params' => [
                                                                        'display_name' => $model['display_name'],
                                                                        'value' => $model['value'],
                                                                        'column' => $model['column']
                                                                    ],
                                                                ],
                                                                'data-toggle' => 'tooltip',
                                                                'data-placement' => 'top',
                                                                'title' => Translate::_('people', 'Remove data')
                                                            ]
                                                        );
                                                    } else {
                                                        return Html::a(
                                                            '<i class="flaticon-delete"></i>',
                                                            Url::toRoute(['data/delete-data'], true),
                                                            [
                                                                'data' => [
                                                                    'method' => 'post',
                                                                    'confirm' => Translate::_(
                                                                        'people',
                                                                        'Are you sure you want to delete this data??'
                                                                    ),
                                                                    'params' => [
                                                                        'display_name' => $model['display_name'],
                                                                        'value' => $model['value'],
                                                                        'column' => $model['column']
                                                                    ],
                                                                ],
                                                                'data-toggle' => 'tooltip',
                                                                'data-placement' => 'top',
                                                                'title' => Translate::_('people', 'Remove data')
                                                            ]
                                                        );
                                                    }
                                                },
                                            ]
                                        ]
                                    ],
                                ]
                            ); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

