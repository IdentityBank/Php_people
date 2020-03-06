<?php

use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var yii\data\ActiveDataProvider $dataSetProvider */
/* @var array $mapToMetadata */
/* @var array $toMap */
/* @var string $mapToBusinessId */

$assetBundle = MetronicAsset::register($this);
$assetAppBundle = MetronicAppAsset::register($this);
$assetAppBundle->businessMapAssets();
$assetUrl = $assetBundle->getAssetUrl();
$assetAppUrl = $assetAppBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            [
                'name' => \idbyii2\helpers\Translate::_('people', 'Connected businesses'),
                'action' => Url::toRoute(['business/index'], true)
            ],
            ['name' => $this->title],
        ],
        'buttons' => [
            '<button id="save-map-button" type="button" class="btn kt-subheader__btn-secondary fix-line-height">'
            . Translate::_('people', 'Save')
            . '</button>'
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


                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">
                            <div class="alert alert-danger fade show hidden" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">
                                    <?= Translate::_(
                                        'people',
                                        'An error has occured. Please contact your system administrator.'
                                    ) ?>
                                </div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                            <p style="color:red;"><?= Translate::_(
                                    'people',
                                    'Drag boxes with data of main business:'
                                ) ?></p>
                            <div class="map-to-container">
                                <?php foreach ($mapToMetadata as $key => $data): ?>

                                    <div class="to-drag-map" data-name="<?= $data['display_name'] ?>"
                                         id="<?= $data['uuid'] ?>" data-uid="<?= $data['uuid'] ?>"
                                         data-value="<?= !empty($mapToData[$key + 1]) ? $mapToData[$key + 1]
                                             : 'null' ?>" draggable="true">
                                        <?= $data['display_name'] ?>: <?= $mapToData[$key + 1] ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <p style="color:red;"><?= Translate::_('people', 'and drop to row of your choice:') ?></p>

                            <div id="businesses">
                                <?php foreach ($toMap as $businessKey => $business): ?>
                                    <div class="business-container">
                                        <h2><?= $business['name'] ?></h2>
                                        <table class="map-business-container">
                                            <?php foreach ($business['metadata'] as $key => $metadata): ?>
                                                <tr class="business-record">
                                                    <td>
                                                        <b><?= $metadata['display_name'] ?></b>
                                                        <div class="old-data">Old
                                                            data: <?= !empty($business['data'][$key + 1])
                                                                ? $business['data'][$key + 1] : '' ?></div>
                                                    </td>
                                                    <td class="relative-drop">
                                                        <div id="drop-<?= $businessKey ?><?= $metadata['uuid'] ?>"
                                                             data-business="<?= $businessKey ?>"
                                                             data-uuid="<?= $metadata['uuid'] ?>" class="drop-map">Drop
                                                            data to map
                                                        </div>
                                                        <i data-business="<?= $businessKey ?>"
                                                           data-uuid="<?= $metadata['uuid'] ?>"
                                                           class="flaticon-circle remove-drop"></i>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const saveURL = '<?= Url::toRoute(['business/map-save'], true) ?>';
    const businessURL = '<?= Url::toRoute(['business/index'], true) ?>';
    const confirmTxt = '<?= Translate::_('people', 'Are your sure you want to unmap this element?') ?>';
    const dropTxt = '<?= Translate::_('people', 'Drop data to map') ?>';
    const fid = '<?= $mapToBusinessId ?>';
</script>
