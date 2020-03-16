<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use idbyii2\helpers\Translate;
use yii\grid\GridView;
use yii\helpers\{Html, Url};

/* @var $this yii\web\View */
/* @var $model idbyii2\models\db\PeopleDataType */

$this->title = Translate::_('people', 'See all my data');
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

            ['name' => $this->title],
        ],
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
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],

                                        [
                                            'attribute' => 'display_name',
                                            'label' => Translate::_('people', 'Display Name')
                                        ],
                                        [
                                            'attribute' => 'value',
                                            'label' => Translate::_('people', 'Value')
                                        ],
                                        [
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{count}',
                                            'contentOptions' => ['style' => 'width: 260px; text-align: center;'],
                                            'header' => Translate::_('people', 'Number of businesses using your data'),
                                            'buttons' => [
                                                'count' => function ($url, $model, $key) {
                                                    return Html::a(
                                                        '1',
                                                        Url::to(['permissions']),
                                                        [
                                                            'data' => [
                                                                'method' => 'post',
                                                                'params' => [
                                                                    'uuid' => $model['column'],
                                                                    'relatedBusiness' => $model['relatedBusiness'],
                                                                    'value' => $model['value'],
                                                                    'required' => $model['required'],
                                                                    'display_name' => $model['display_name'],
                                                                    'businessName' => $model['businessName']
                                                                ],
                                                            ],
                                                            'class' => 'btn btn-dark btn-purple btn-sm',
                                                            'data-toggle' => 'tooltip',
                                                            'data-placement' => 'bottom',
                                                            'title' => $model['businessName']
                                                        ]
                                                    );
                                                }
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

