<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\components\IdbPagination;
use idbyii2\helpers\Translate;
use yii\grid\GridView;
use yii\helpers\{Html, Url};

/* @var $this yii\web\View */
/* @var $model idbyii2\models\db\PeopleDataType */

$this->title = Translate::_('people', 'Edit data for {businessName}', compact('businessName'));
$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetAppBundle->tooltipAssets();
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$oid = Yii::$app->session->get('oid');
$aid = Yii::$app->session->get('aid');
$dbid = Yii::$app->session->get('dbid');
$uid = Yii::$app->session->get('uid');


$dpoContactButton = Html::a(
    Translate::_('people', 'Business GDPR'),
    ['business/gdpr'],
    [
        'data' => [
            'method' => 'post',
            'params' => [
                'oid' => $oid,
                'aid' => $aid,
                'dbid' => $dbid,
                'uid' => $uid,
            ],
        ],
        'class' => ' btn kt-subheader__btn-secondary',
    ]
);


$buttonDeleteAll = Html::a(
    Translate::_('people', 'Delete all data'),
    ['business/delete-all-data'],
    [
        'data' => [
            'method' => 'post',
            'confirm' => Translate::_('people', 'Are you sure you want to delete all data?'),
            'params' => [
                'oid' => $oid,
                'aid' => $aid,
                'dbid' => $dbid,
                'uid' => $uid,
            ],
        ],
        'class' => 'btn btn-danger kt-subheader__btn-options',
    ]
);

$buttonSelectAll = Html::a(
    '<input style="margin-left:5px;" type="checkbox" ' . (Yii::$app->session->has($businessId . 'deleteAll')
        ? 'checked="checked"' : '') . ' />',
    ['business/delete-all-data'],
    [
        'data' => [
            'method' => 'post',
            'confirm' => Yii::$app->session->has($businessId . 'deleteAll')
                ? false
                : Translate::_(
                    'people',
                    'Are you sure you want to delete all data?'
                ),
            'params' => [
                'oid' => $oid,
                'aid' => $aid,
                'dbid' => $dbid,
                'uid' => $uid,
            ],
        ],
    ]
);

$buttonUpdate = Html::a(
    Translate::_('people', 'Send edits to business'),
    ['business/send-to-business'],
    ['class' => ' btn kt-subheader__btn-secondary']
);

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            [
                'name' => Translate::_('people', 'Who uses my data?'),
                'action' => Url::toRoute(['business/who-uses'], true)
            ],
            ['name' => Translate::_('people', 'Edit data for {businessName}', compact('businessName'))],
        ],
        'buttons' => [$dpoContactButton, $buttonDeleteAll, $buttonUpdate]
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
                    <?= Yii::$app->controller->renderPartial(
                        '@app/themes/metronic/modules/peopleuser/views/business/partials/businessName',
                        ['businessName' => $businessName]
                    ) ?>



                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">
                            <?= GridView::widget(
                                [
                                    'dataProvider' => $dataProvider,
                                    'showHeader' => true,
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],

                                        [
                                            'attribute' => 'display_name',
                                            'label' => Translate::_('people', 'Display Name')
                                        ],
                                        [
                                            'attribute' => 'used_for',
                                            'label' => Translate::_('people', 'Used For')
                                        ],
                                        [
                                            'attribute' => 'required',
                                            'label' => Translate::_('people', 'Required'),
                                            'format' => 'raw',
                                            'value' => function ($model, $index, $widget) {
                                                $checked = ($model['required'] === 'true');
                                                return Html::checkbox(
                                                    'required[]',
                                                    $checked,
                                                    ['value' => $index, 'disabled' => true]
                                                );
                                            },
                                        ],
                                        [
                                            'attribute' => 'value',
                                            'label' => Translate::_('people', 'Value')
                                        ],
                                        [
                                            'attribute' => 'new_value',
                                            'label' => Translate::_('people', 'New Value')
                                        ],
                                        [
                                            'attribute' => 'delete',
                                            'format' => 'raw',
                                            'label' => Translate::_('people', 'Delete'),
                                            'header' => 'Delete ' . $buttonSelectAll,
                                            'value' => function ($model, $index, $widget) {
                                                $confirm = false;
                                                if (!$model['delete']) {
                                                    $confirm = ($model['required'] && !$model['delete'])
                                                        ? Translate::_(
                                                            'people',
                                                            'You are going to delete required data for this business. This action will delete your account from this business. Are you sure you want to do this?'
                                                        )
                                                        : Translate::_(
                                                            'people',
                                                            'Are you sure you want to delete this data??'
                                                        );
                                                }
                                                $checkbox = Html::checkbox(
                                                    'delete[]',
                                                    $model['delete'],
                                                    [
                                                        'value' => $index,
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'top',
                                                        'title' => Translate::_(
                                                            'people',
                                                            'Select data to remove on send'
                                                        )
                                                    ]
                                                );

                                                return Html::a(
                                                    $checkbox,
                                                    ['business/delete-data'],
                                                    [
                                                        'data' => [
                                                            'method' => 'post',
                                                            'confirm' => $confirm,
                                                            'params' => [
                                                                'display_name' => $model['display_name'],
                                                                'value' => $model['value'],
                                                                'column' => $model['column'],
                                                                'required' => $model['required']
                                                            ],
                                                        ],
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
                                                        Url::to(['business/edit-data']),
                                                        [
                                                            'data' => [
                                                                'method' => 'post',
                                                                'params' => [
                                                                    'display_name' => $model['display_name'],
                                                                    'value' => $model['value'],
                                                                    'column' => $model['column'],
                                                                    'required' => $model['required']
                                                                ],
                                                            ],
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'top',
                                                            'title' => Translate::_('people', 'Edit data')
                                                        ]
                                                    );
                                                },
                                                'delete' => function ($url, $model, $key) {
                                                    return Html::a(
                                                        '<i class="flaticon-delete"></i>',
                                                        ['business/delete-data'],
                                                        [
                                                            'data' => [
                                                                'method' => 'post',
                                                                'confirm' => $model['required']
                                                                    ? Translate::_(
                                                                        'people',
                                                                        'You are going to delete required data for this business. This action will delete your account from this business. Are you sure you want to do this?'
                                                                    )
                                                                    : Translate::_(
                                                                        'people',
                                                                        'Are you sure you want to delete this data??'
                                                                    ),
                                                                'params' => [
                                                                    'display_name' => $model['display_name'],
                                                                    'value' => $model['value'],
                                                                    'column' => $model['column'],
                                                                    'required' => $model['required']
                                                                ],
                                                            ],
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'top',
                                                            'title' => Translate::_('people', 'Remove data')
                                                        ]
                                                    );
                                                },
                                            ]
                                        ]
                                    ],
                                    'pager' => [
                                        'prevPageLabel' => '<div class="btn btn-default"><i class="fa fa-arrow-circle-left"></i>'.Translate::_('people','Back').'</div>',
                                        'nextPageLabel' => '<div class="btn btn-default">'.Translate::_('people','Next').'<i class="fa fa-arrow-circle-right"></i></div>',
                                        'firstPageLabel' => '<div class="btn btn-default">'.Translate::_('people','First').'</div>',
                                        'lastPageLabel' => '<div class="btn btn-default">'.Translate::_('people','Last').'</div>',
                                        'maxButtonCount' => 10,
                                        'pageCssClass' => 'btn btn-default btn-pagination',
                                        'activePageCssClass' => 'active',
                                        'disabledPageCssClass' => 'disabled',
                                    ]
                                ]
                            ); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

