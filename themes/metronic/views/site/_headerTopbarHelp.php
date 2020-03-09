<?php

use app\helpers\PeopleConfig;
use app\helpers\Translate;
use yii\helpers\Html;

$contextHelpUrl = $this->params['contextHelpUrl'] ?? Translate::_('people', 'https://www.identitybank.eu/help/people');

?>

<?php if (PeopleConfig::get()->isYii2PeopleHelpEnabled()) : ?>
    <div class="kt-header__topbar-item">
        <div class="kt-header__topbar-wrapper">
        <span class="kt-header__topbar-icon">
        <?= Html::a(
            '<i class="flaticon2-help" style="font-size: 1.6rem;"></i>',
            $contextHelpUrl,
            ['class' => 'kt-header__topbar-icon', 'target' => '_blank']
        ) ?>
        </span>
        </div>
    </div>
<?php endif; ?>

