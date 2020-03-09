<?php

use app\assets\MetronicAsset;
use app\helpers\Translate;
use yii\helpers\Html;
use yii\helpers\Url;

$assetBundle = MetronicAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();

$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => Translate::_('people', 'Upload requests'),
        'breadcrumbs' => [
            [
                'name' => \idbyii2\helpers\Translate::_('people', 'Shared files'),
                'action' => Url::toRoute(['index'], true)
            ],
            ['name' => Translate::_('people', 'Upload requests')],
        ],
        'buttons' => [
            Html::a(
                Translate::_('people', 'Back'),
                ['files'],
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
                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">
                            <div class="col-md-8 offset-md-2">
                                <table id="upload-requests-table" style="margin: 0 auto;">
                                    <thead>
                                        <tr>
                                            <th><?= Translate::_('people', 'Request name') ?></th>
                                            <th style="padding-left: 40px;"><?= Translate::_('people', 'Uploads') ?></th>
                                            <th style="padding-left: 40px;"><?= Translate::_('people', 'Requested files') ?></th>
                                            <th style="padding-left: 40px;"><?= Translate::_('people', 'Message') ?></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($requests as $request): ?>
                                            <tr onclick="window.location='<?= Url::toRoute(['upload-request', 'id' => $request->id], true) ?>'">
                                                <td><?= $request->name ?></td>
                                                <td style="padding-left: 40px;"><?= $request->uploads ?></td>
                                                <td style="padding-left: 40px;"><?= $request->upload_limit ?></td>
                                                <td style="padding-left: 40px;"><?= $request->message ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>