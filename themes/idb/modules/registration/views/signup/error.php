<?php

use app\helpers\Translate;
use app\themes\idb\assets\IdbWizardAsset;
use yii\helpers\Html;

$wizardAsset = IdbWizardAsset::register($this);
$this->title = Translate::_('people', 'A problem occurred during registration');

?>

<div class="container">
    <div class="container-inner">
        <div class="row">
            <div class="col-lg-12" style="float: none;margin: 0 auto;">
                <div class="sp-column">
                    <div class="sp-module">
                        <div class="sp-module-content">
                            <h3 align="center">
                                <?= Translate::_('people', 'Contact us via email: ') ?>
                                <?= Html::mailto(
                                    'support@identitybank.eu',
                                    'support@identitybank.eu?subject=Error report.',
                                    ['class' => 'support_mail', 'style' => 'color:#3c8dbc;']
                                ) ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

