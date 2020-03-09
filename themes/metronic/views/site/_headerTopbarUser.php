<?php

use app\helpers\Translate;
use yii\helpers\Html;
use yii\helpers\Url;

$accountNumber = ((empty(Yii::$app->user->identity->accountNumber)) ? '' : Yii::$app->user->identity->accountNumber);
$userId = ((empty(Yii::$app->user->identity->userId)) ? '' : Yii::$app->user->identity->userId);
?>

<div class="kt-header__topbar-item kt-header__topbar-item--user">

    <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
        <span class="kt-header__topbar-icon"><i class="flaticon2-user-outline-symbol"></i></span>
    </div>

    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">

        <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
             style="background-image: url(<?= $assetUrl ?>/assets/media/misc/bg-1.jpg)">
            <div class="kt-user-card__name">
                <span><?= Translate::_('people', 'Login name') ?>:&nbsp;</span><b><?= $userId ?></b>
                <hr>
                <span style="font-size: smaller;"><?= Translate::_(
                        'people',
                        'Account number'
                    ) ?><br><?= $accountNumber ?></span>
            </div>
        </div>


        <div class="kt-notification">
            <a href="<?= Url::to(['/profile']) ?>" class="kt-notification__item">
                <div class="kt-notification__item-icon">
                    <i class="flaticon2-calendar-3 kt-font-success"></i>
                </div>
                <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title kt-font-bold">
                        <?= $userId ?>
                    </div>
                </div>
            </a>
            <div class="kt-notification__custom kt-space-between">
                <div></div>
                <?= Html::a(
                    Translate::_('people', 'Logout'),
                    ['/site/logout'],
                    ['class' => 'btn btn-label btn-label-brand btn-sm btn-bold']
                ) ?>
            </div>
        </div>

    </div>
</div>
