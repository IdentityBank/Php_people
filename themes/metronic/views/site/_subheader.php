<?php

use app\helpers\Translate;
use yii\helpers\Html;
use yii\helpers\Url;

$title = $subheader['title'] ?? '';
$desc = $subheader['desc'] ?? [
        'name' => '<i class="flaticon2-shelter"></i>',
        'action' => Url::toRoute('/', true),
        'options' => ['class' => 'kt-subheader__breadcrumbs-home']
    ];

?>

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                <a href="<?= Url::toRoute('/', true) ?>" style="color: white">
                    <i class="flaticon2-pin"></i>&nbsp;
                    <?= Translate::_('people', 'Dashboard'); ?>
                </a>
            </h3>

            <div class="kt-subheader__breadcrumbs">

                <?php if (!empty($subheader['breadcrumbs'])) : ?>
                    <?= Html::a($desc['name'], $desc['action'], $desc['options']) ?>
                    <?php foreach ($subheader['breadcrumbs'] as $breadcrumb): ?>
                        <span class="kt-subheader__breadcrumbs-separator"></span>
                        <a <?php if (!empty($breadcrumb['action'])): ?>
                            href="<?= ((empty($breadcrumb['action'])) ? '' : $breadcrumb['action']); ?>"
                        <?php endif; ?>
                                class="kt-subheader__breadcrumbs-link">
                            <?= $breadcrumb['name']; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

        </div>
        <?php if (!empty($subheader['buttons'])): ?>
            <div class="kt-subheader__toolbar">
                <div class="kt-subheader__wrapper buttons-idb">
                    <?php foreach ($subheader['buttons'] as $button): ?>
                        <?= $button ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
