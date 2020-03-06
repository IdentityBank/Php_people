<?php

use app\helpers\Translate;

?>

<div class="row">
    <div class="col-lg-12" style="float: none;margin: 0 auto;">
        <?= $wizardAsset->generateWizard(
            [
                'Icon' => 'glyphicon-circle-arrow-right',
                'Title' => Translate::_('people', 'Start')
            ],
            [
                'Icon' => 'glyphicon-user',
                'Title' => Translate::_('people', 'Confirmation')
            ],
            [
                'Icon' => 'glyphicon-envelope',
                'Title' => Translate::_('people', 'T&Cs')
            ],
            [
                'Icon' => 'glyphicon-phone',
                'Title' => Translate::_('people', 'Authorisation')
            ],
            [
                'Icon' => 'glyphicon-log-in',
                'Title' => Translate::_('people', 'Login')
            ],
            $count
        ) ?>
    </div>
</div>
<br>
