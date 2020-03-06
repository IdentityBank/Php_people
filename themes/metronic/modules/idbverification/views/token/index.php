<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use idbyii2\widgets\FlashMessage;
use yii\bootstrap\ActiveForm;
use yii\helpers\{Html, Url};

$params = [
    'assetUrl' => $assetUrl
];

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'assetAppUrl' => $assetAppUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            [
                'name' => Translate::_('people', 'My profile'),
                'action' => Url::toRoute(['/idbuser/profile'], true)
            ],
            [
                'name' => Translate::_('people', 'Change password'),
            ]
        ],
        'buttons' => [
            Html::submitButton(
                Translate::_('people', 'Next'),
                ['class' => 'btn kt-subheader__btn-secondary fix-line-height']
            )
        ]
    ]
];

?>
<?php $form = ActiveForm::begin(); ?>

<?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_subheader', $params) ?>
<div class="kt-container">
    <?= Yii::$app->controller->renderPartial('@app/themes/metronic/views/site/_alerts') ?>
</div>
<div class="kt-container  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                <div class="kt-portlet__body kt-portlet__body--fit">

                    <div class="kt-portlet idb-no-margin">
                        <div class="kt-portlet__body">
                            <div class="kt-widget4">
                                <?php if ($model->getErrors()) { ?>
                                    <?= Html::tag(
                                        'div',
                                        $form->errorSummary($model),
                                        ['class' => 'alert alert-danger']
                                    ) ?>
                                <?php } ?>
                                <?= FlashMessage::widget(
                                    [
                                        'success' => Yii::$app->session->hasFlash('success')
                                            ? Yii::$app->session->getFlash('success') : null,
                                        'error' => Yii::$app->session->hasFlash('error') ? Yii::$app->session->getFlash(
                                            'error'
                                        ) : null,
                                        'info' => Yii::$app->session->hasFlash('info') ? Yii::$app->session->getFlash(
                                            'info'
                                        ) : null,
                                    ]
                                ); ?>


                                <h2><?= Translate::_(
                                        'people',
                                        'Enter the email address and the mobile phone number associated with this account.'
                                    ) ?></h2>

                                <?= $form->field($model, 'email')->textInput(
                                    ['placeholder' => $model->getAttributeLabel('email')]
                                ) ?>
                                <?= $form->field($model, 'mobile')->textInput(
                                    ['placeholder' => $model->getAttributeLabel('mobile')]
                                ) ?>
                                <?= $form->field($model, 'token')->textarea(
                                    ['placeholder' => $model->getAttributeLabel('token'), 'rows' => 5]
                                )->hint(
                                    Translate::_(
                                        'people',
                                        'During account creation you downloaded, printed and stored the password recovery token.'
                                    ) . ' ' .
                                    Translate::_(
                                        'people',
                                        'You need to use a QR scanner to get the password token as text and fill the text in below.'
                                    )
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
