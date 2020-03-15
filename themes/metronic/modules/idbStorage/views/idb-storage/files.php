<?php

use app\assets\FrontAsset;
use app\assets\MetronicAppAsset;
use app\assets\MetronicAsset;
use app\helpers\Translate;
use idbyii2\helpers\File;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$uploadAssets = FrontAsset::register($this);
$uploadAssets->idbStorageUpload();
$assetBundle = MetronicAsset::register($this);
$assetUrl = $assetBundle->getAssetUrl();
$assetAppBundle = MetronicAppAsset::register($this);
$assetAppBundle->tooltipAssets();
$rightButton = null;
if($requestsCount) {
    $rightButton = Html::tag('div',
        Html::a(
            Translate::_('people', 'Upload requests'),
            ['upload-requests'],
            [
                'class' => 'btn btn-danger kt-subheader__btn-options',
                'data' => [
                    'method' => 'post',
                    'params' => $params
                ]
            ]
        ) .
        Html::tag('span', $requestsCount, ['class' => 'kt-badge kt-badge--primary']),
        ['class' => 'button-badge']
    );
}


$params = [
    'assetUrl' => $assetUrl,
    'subheader' => [
        'title' => $this->title,
        'breadcrumbs' => [
            [
                'name' => \idbyii2\helpers\Translate::_('people', 'Shared files'),
                'action' => Url::toRoute(['index'], true)
            ],
        ],
        'buttons' => [
            Html::a(
                Translate::_('people', 'Back'),
                ['index'],
                ['class' => 'btn kt-subheader__btn-secondary']
            ),
            $rightButton

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
            <div class="kt-portlet idb-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-portlet idb-no-margin kt-portlet--bordered">
                        <div class="kt-portlet__body">
                            <?= GridView::widget(
                                [
                                    'dataProvider' => $dataProvider,
                                    'showHeader' => true,
                                    'rowOptions' => function ($model, $key, $index, $grid) {
                                        return ['class' => 'clickable-row', 'data-href' => Url::toRoute(['download', 'oid' => $model->oid], true)];
                                    },
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],
                                        [
                                            'header' => '<span id="col-name-change-type">' . Translate::_(
                                                    'people',
                                                    'Filename'
                                                ) . '</span><br/>' .
                                                '<form method="post"><div class="input-group input-group-sm hidden-xs">
                                                                    <input class="search form-control pull-right" value="'
                                                . ArrayHelper::getValue($search, 'name', '') . '" name="search[name]" type="text"/>
                                                                    <div class="input-group-btn">
                                                                        <button type="submit" class="btn btn-default btn-search"><i class="fa fa-search"></i></button>
                                                                    </div>
                                                                    <input type="hidden" name="'
                                                . Yii::$app->request->csrfParam . '" value="'
                                                . Yii::$app->request->csrfToken . '" />
                                                                </div></form>',
                                            'value' => function ($model) {
                                                return $model->name;
                                            }
                                        ],
                                        [
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{summary} {download} {delete}',
                                            'contentOptions' => ['class' => 'click-disabled', 'style' => 'text-align: center;'],
                                            'header' => '',
                                            'headerOptions' => ['id' => 'idb_action', 'class' => 'no-sort', 'style' => 'width: 90px;'],
                                            'visibleButtons' => [
                                                'delete' => true,
                                                'summary' => true,
                                            ],
                                            'buttons' => [
                                                'summary' => function ($url, $model, $key) {
                                                    return Html::a(
                                                        '<i data-object-oid="' . $model->oid . '" data-object-id="' . $model->id . '" class="flaticon-info"></i>',
                                                        "#",
                                                        [
                                                            'class' => 'file-summaries-button',
                                                            'style' => 'cursor:pointer;',
                                                        ]
                                                    );
                                                },
                                                'download' => function ($url, $model, $key) {
                                                    return '<a href="' . Url::toRoute(['download', 'oid' => $model->oid], true) . '"><i class="fa fa-link"></i></a>';
                                                },
                                                'delete' => function ($url, $model, $key) use($vaultId) {
                                                    if($model->uid !== $vaultId) {
                                                        return '<a onclick="return confirm(' . "'" . Translate::_('people', 'You confirm your action') . "'" . ');" href="' . Url::toRoute(['delete', 'itemId' => $model->id], true) . '"><i class="flaticon-delete"></i></a>';
                                                    } else {
                                                        return '<i style="color:grey; cursor: not-allowed;" class="flaticon-delete" data-toggle="tooltip" data-placement="bottom" title="You don\'t have a permission to delete this file."></i>';
                                                    }
                                                }
                                            ]
                                        ]
                                    ],
                                    'pager' => [
                                        'prevPageLabel' => '<div class="btn btn-default"><i class="fa fa-arrow-circle-left"></i> ' . Translate::_('people', 'Back') . '</div>',
                                        'nextPageLabel' => '<div class="btn btn-default">' . Translate::_('people', 'Next') . ' <i class="fa fa-arrow-circle-right"></i></div>',
                                        'firstPageLabel' => '<div class="btn btn-default">' . Translate::_('people', 'First') . '</div>',
                                        'lastPageLabel' => '<div class="btn btn-default">' . Translate::_('people', 'Last') . '</div>',
                                        'maxButtonCount' => 10,
                                        'pageCssClass' => 'btn btn-default btn-pagination',
                                        'activePageCssClass' => 'active',
                                        'disabledPageCssClass' => 'disabled',
                                    ]
                                ]
                            ); ?>


                            <?php if ($options['peopleUpload']): ?>

                                <div style="display:none;" id="max-file-warning" class="alert alert-danger fade show"
                                     role="alert">
                                    <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
                                    <div class="alert-text"><?= Translate::_('people', 'Max file size: {uploadLimit}MB', compact('uploadLimit')) ?></div>
                                    <div class="alert-close">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-close"></i></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row" id="click-upload">
                                    <div class="m-dropzone dropzone m-dropzone--primary dz-clickable"
                                         id="m-dropzone-two">
                                        <?= Translate::_('people', 'Click or drop to upload.') ?>
                                    </div>
                                    <div class="clear-both"></div>
                                    <div id="max-file-size"><?= Translate::_('people', 'Max file size: {uploadLimit}MB', compact('uploadLimit')) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="progress-container">
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75"
             aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
    </div>
</div>

<form id="upload-form" method="POST" style="display: none;" enctype="multipart/form-data">
    <div class="hidden-inputs"></div>
    <input type="file" name="file"/>
</form>

<form action="<?= Url::toRoute(['save-file'], true) ?>" style="display: none;" id="complete-upload-form" method="POST">
    <input type="hidden" name="shareName"/>
    <input type="hidden" name="shareChecksum"/>
    <input type="hidden" name="shareSize"/>
    <input type="hidden" name="shareKey"/>
    <input type="hidden" name="shareOid"/>
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>"/>
</form>

<div class="modal" id="summary-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="kt-portlet">
                <div
                        class="kt-portlet__head kt-portlet__head--noborder  kt-ribbon kt-ribbon--clip kt-ribbon--right kt-ribbon--border-dash-hor kt-ribbon--warning">
                    <div class="kt-ribbon__target"
                         style="top: 12px; right: -11px;"><?= Translate::_('people', 'Info') ?>
                        <span class="kt-ribbon__inner"></span></div>
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            <?= Translate::_('people', 'File Summary') ?>
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div id="table-body">
                        <table>
                            <tr>
                                <td><b><?= Translate::_('people', 'Name') ?>:</b></td>
                                <td><span id="summary-name"></span></td>
                            </tr>
                            <tr>
                                <td><b><?= Translate::_('people', 'Download') ?>:</b></td>
                                <td><span><a id="summary-download"><?= Translate::_(
                                                'people',
                                                'download'
                                            ) ?></a></span>
                                </td>
                            </tr>
                            <tr>
                                <td><b><?= Translate::_('people', 'Upload time') ?>:</b></td>
                                <td><span id="summary-createtime"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Translate::_(
                        'people',
                        'Close'
                    ) ?></button>
            </div>
        </div>
    </div>
</div>

<?= PeopleConfig::jsOptions(
    [
        'checkUrl' => Url::toRoute('upload'),
        'summariesUrl' => Url::toRoute('summary'),
        'initObjectUrl' => Url::toRoute('init-object', true),
        'uploadLimit' => File::convertToBytes($uploadLimit . 'MB')
    ]
) ?>

