<?php

use app\helpers\Translate;
use yii\helpers\Url;

$menuItems = [
    [
        'name' => Translate::_('people', 'Who uses my data?'),
        'action' => Url::toRoute('/business/who-uses', true),
    ],
    [
        'name' => Translate::_('people', 'See all my data'),
        'action' => Url::toRoute('/data/show-all', true),
    ],
    [
        'name' => Translate::_('people', 'What\'s my data being used for?'),
        'action' => Url::toRoute('/business/audit-log', true),
    ],
    [
        'name' => Translate::_('people', 'Shared files'),
        'action' => Url::toRoute('/idb-storage/index', true),
    ],
];

?>

<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
<div class="kt-header-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_header_menu_wrapper" style="opacity: 1;">
    <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile ">
        <ul class="kt-menu__nav">
            <?php foreach ($menuItems as $menuItem) : ?>
                <li class="kt-menu__item  kt-menu__item--submenu kt-menu__item--rel">
                    <a href="<?= $menuItem['action']; ?>" class="kt-menu__link">
                        <span class="kt-menu__link-text" style="font-size: larger">
                            <?= $menuItem['name'] ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
            </li>
        </ul>
    </div>
</div>
