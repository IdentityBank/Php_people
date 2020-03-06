<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\components\IdbPagination;
use idbyii2\helpers\Translate;
use yii\helpers\{Url};
use yii\widgets\ActiveForm;

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
        'buttons' => []
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

                            <div class="alert alert-outline-brand alert-dismissible fade show" role="alert" style="display:flex; justify-content: space-around">
                                <a target="_blank" href="<?= $dpoData['dpoPrivacyNotice']?? '' ?>">
                                    <i class="fa fa-external-link-alt margin-r-5"></i>
                                    <?= Translate::_('people', 'Privacy Notice')?>
                                </a>
                                <a target="_blank" href="<?= $dpoData['dpoTermsAndCondition']?? '' ?>">
                                    <i class="fa fa-external-link-alt margin-r-5"></i>
                                    <?= Translate::_('people', 'Terms and condition')?>
                                </a>
                                <a target="_blank" href="<?= $dpoData['dpoCookiePolicy']?? '' ?>">
                                    <i class="fa fa-external-link-alt margin-r-5"></i>
                                    <?= Translate::_('people', 'Cookie policy')?>
                                </a>
                                <a target="_blank" href="<?= $dpoData['dpoDataProcessingAgreements']?? '' ?>">
                                    <i class="fa fa-external-link-alt margin-r-5"></i>
                                    <?= Translate::_('people', 'Data processing agreements')?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php if (!empty($dpoData['dpoEmail'])): ?>
                                        <h2><?= Translate::_('people', 'Email contact') ?></h2>
                                        <div class="card-title collapsed" data-toggle="collapse"
                                             data-target="#dpo_email" aria-expanded="false" aria-controls="collapseOne7"
                                             onmouseover="" style="cursor: pointer;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                 viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                    <path
                                                        d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z"
                                                        fill="#000000" fill-rule="nonzero"></path>
                                                    <path
                                                        d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z"
                                                        fill="#000000" fill-rule="nonzero" opacity="0.3"
                                                        transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) "></path>
                                                </g>
                                            </svg>
                                            <?= $dpoData['dpoEmail'] ?>
                                        </div>

                                        <div id="dpo_email" class="collapse">

                                            <div class="card">
                                                <div class="card-header">
                                                    <div>
                                                        <i class="flaticon2-crisp-icons"></i> <?= Translate::_('people', 'Write to DPO') ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="card-body">
                                                        <?php $form = ActiveForm::begin(
                                                            ['options' => ['class' => 'lockscreen-credentials']]
                                                        ); ?>

                                                        <?= $form->field($model, 'title')->textInput()->label(
                                                            Translate::_('people', 'Title:')
                                                        ) ?>
                                                        <?= $form->field($model, 'message')->textarea()->label(
                                                            Translate::_('people', 'Message:')
                                                        ) ?>

                                                        <?= $form->field($model, 'email')->hiddenInput(
                                                            ['value' => $dpoData['dpoEmail']]
                                                        )->label(false); ?>
                                                        <?= $form->field($model, 'businessId')->hiddenInput(
                                                            ['value' => $businessId]
                                                        )->label(false); ?>

                                                        <button style="width: 100%;"
                                                                class="btn btn-primary"><?= Translate::_(
                                                                'people',
                                                                'Send'
                                                            ) ?></button>

                                                        <?php ActiveForm::end(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    <?php endif; ?>

                                    <?php if (!empty($dpoData['dpoMobile'])): ?>
                                        <h2><?= Translate::_('people', 'Mobile contact') ?></h2>
                                        <a href="tel:<?= $dpoData['dpoMobile'] ?>"><?= $dpoData['dpoMobile'] ?></a>
                                    <?php endif; ?>

                                    <?php if (!empty($dpoData['dpoAddress'])): ?>
                                        <h2 style="margin-top: 20px;"><?= Translate::_('people', 'Address') ?></h2>
                                        <p><?= $dpoData['dpoAddress'] ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($dpoData['dpoOther'])): ?>
                                        <h2 style="margin-top: 20px;"><?= Translate::_('people', 'DPO contact') ?></h2>
                                        <p><?= $dpoData['dpoOther'] ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <?php if(!empty($gdpr['dataTypes'])): ?>
                                        <h2 style="margin-top: 20px;"><?= Translate::_('people', 'Retention period') ?></h2>
                                        <ul>
                                            <?php if(!empty($gdpr['dataTypes'][array_key_first($gdpr['dataTypes'])]['minimum'])): ?>
                                                <li><?= Translate::_('people', 'Possible to delete: {days}Days',[
                                                        'days' =>$gdpr['dataTypes'][array_key_first($gdpr['dataTypes'])]['minimum']
                                                    ])?></li>
                                            <?php endif; ?>
                                            <?php if(!empty($gdpr['dataTypes'][array_key_first($gdpr['dataTypes'])]['maximum'])): ?>
                                                <li><?= Translate::_('people', 'Delete after: {days}Days',[
                                                        'days' =>$gdpr['dataTypes'][array_key_first($gdpr['dataTypes'])]['maximum']
                                                    ])?></li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <?php if(!empty($gdpr['dataTypes'][array_key_first($gdpr['dataTypes'])]['listDataProcessors'])): ?>
                                        <h2 style="margin-top: 20px;"><?= Translate::_('people', 'Data processors') ?></h2>
                                        <ul>
                                        <?php foreach($gdpr['dataTypes'][array_key_first($gdpr['dataTypes'])]['listDataProcessors'] as $processor): ?>
                                            <li><?= $processor ?></li>
                                        <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

