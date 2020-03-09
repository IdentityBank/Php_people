<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\{Html, Url};
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */

$this->title = Translate::_('people', 'Edit data value');
$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetAppBundle->tooltipAssets();
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$backButton = Html::a(
    Translate::_('people', 'Back'),
    ['business/edit'],
    ['class' => 'btn btn-danger kt-subheader__btn-options']
);

$submitButton = Html::submitButton(
    Translate::_('people', 'Save'),
    ['class' => 'btn fix-line-height kt-subheader__btn-secondary']
);

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
        'buttons' => [$backButton, $submitButton]
    ]
];

?>
<?php $form = ActiveForm::begin(); ?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
<div class="kt-container  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                <div class="kt-portlet__body kt-portlet__body--fit">


                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">
                            <h1><?= Html::encode($model['display_name']) ?></h1>

                            <div class="people-data-type-form">
                                <div id="dataTypes">
                                    <div>
                                        <?= $form->field($model, 'value')
                                                 ->hiddenInput(['value' => $model['value']])
                                                 ->label(false); ?>
                                        <?= $form->field($model, 'value')
                                                 ->hiddenInput(['column' => $model['column']])
                                                 ->label(false); ?>
                                        <?= $form->field($model, 'display_name')->hiddenInput(
                                            ['value' => $model['display_name']]
                                        )->label(false); ?>
                                        <?= $form->field($model, 'value')->textInput(['disabled' => true])->label(
                                            Translate::_('people', 'Old value:')
                                        ) ?>
                                        <?= $form->field($model, 'new_value')->textInput()->label(
                                            Translate::_('people', 'New value:')
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
