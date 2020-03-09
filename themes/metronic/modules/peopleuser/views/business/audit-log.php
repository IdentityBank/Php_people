<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use idbyii2\helpers\Translate;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataSetProvider yii\data\ActiveDataProvider */

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            ['name' => $this->title],
        ],

        'buttons' => [
            Html::a(
                Translate::_('people', 'Back'),
                ['/'],
                ['class' => 'btn kt-subheader__btn-secondary']
            )
        ]
    ]
];

?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <?= Yii::$app->controller->renderPartial(
                        '@app/themes/metronic/modules/peopleuser/views/business/partials/businessName',
                        ['businessName' => Translate::_('people', 'Your data has been used for the following reasons:')]
                    ) ?>

                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">

                            <?php Pjax::begin(); ?>
                            <?= GridView::widget(
                                [
                                    'dataProvider' => $dataProvider,
                                    'filterModel' => $searchModel,
                                    'columns' => $searchColumns,
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

