<?php

use app\helpers\Translate;
use idbyii2\helpers\Localization;
use idbyii2\helpers\Qr;

$fontFamily = "'DejaVuSans, Arial, Helvetica, Helvetica, sans-serif'";

?>
<div align="center">
    <div style="clear: both;">
        <p align="right"
           style="position: absolute; float: right; color: black; font-size: small; font-family: <?= $fontFamily ?>;">
            <?= Localization::getDateTimePortalFormat() ?>
        </p>
    </div>
    <h1 align="center" style="color: black; font-size: xx-large; font-family: <?= $fontFamily ?>;">
        <b><?= Translate::_('people', 'Password Recovery Token') ?></b>
    </h1>
    <br>
    <p align="left">
        <code style="color: black; font-size: large;">
            <?php foreach ($userData as $userDataKey => $userDataValue) : ?>
                <b><?= Translate::external($userDataKey) ?>: </b><?= $userDataValue ?><br>
            <?php endforeach; ?>
        </code>
    </p>
    <h2 align="center" style="color: red; font-size: x-large; font-family: <?= $fontFamily ?>;">
        <b><?= Translate::_('people', 'VERY IMPORTANT') ?></b>
    </h2>
    <p align="left" style="color: black; font-size: medium; font-family: <?= $fontFamily ?>;">
        <?= Translate::_(
            'people',
            'This PDF contains very important information that you can use at some point in the future if you lose your password and need to regain access to your Identity Bank account.'
        ) ?><br>
    </p>
    <h1 align="center" style="color: black; font-size: large; font-family: <?= $fontFamily ?>;">
        <b><?= Translate::_('people', 'QR code') ?></b>
    </h1>
    <p align="center">
        <img src="<?= Qr::pngHtml($passwordToken->value, 8) ?>"/>
    </p>
    <p style="page-break-after: always;">&nbsp;</p>
    <h1 align="center" style="color: black; font-size: large; font-family: <?= $fontFamily ?>;">
        <b><?= Translate::_('people', 'Recovery Token') ?></b>
    </h1>
    <p align="left">
        <code style="color: black; font-size: medium;"><b><?= $passwordToken->value ?></b></code>
    </p>
    <h1 align="center" style="color: black; font-size: large; font-family: <?= $fontFamily ?>;">
        <b><?= Translate::_('people', 'What to do with this information') ?></b>
    </h1>
    <p align="left" style="color: black; font-size: medium; font-family: <?= $fontFamily ?>;">
        <?= Translate::_(
            'people',
            'You must download this document, print it out and securely save this printed document. By securely save we mean preferably stored in your company safe and/or stored with your company lawyer.'
        ) ?><br>
    </p>
    <br>
    <p align="left" style="color: black; font-size: medium; font-family: <?= $fontFamily ?>;">
        <?= Translate::_(
            'people',
            'NEVER leave copies of this document lying around! As an analogy, it’s the same as if you left the keys to a bank vault lying around for anybody to use! You would not do that with physical objects so think of this document in the same way it is just as precious.'
        ) ?><br>
    </p>
    <br>
    <p align="left" style="color: black; font-size: medium; font-family: <?= $fontFamily ?>;">
        <?= Translate::_(
            'people',
            'If you do not take the steps explained above we cannot help you to recover your Identity Bank account! This is because we are providing a safe secure system for your data to which there are no back doors. Not even for us.'
        ) ?><br>
    </p>
    <br>
    <p align="left" style="color: black; font-size: medium; font-family: <?= $fontFamily ?>;">
        <?= Translate::_(
            'people',
            'Once you have completed these instructions and securely stored this document, then make sure you clear the cache of your browser, close the browser window on your computer and delete all copies of this document, from all locations on all devices where it has been saved. This is an important step as this document is the master key to your account and must be well protected.'
        ) ?><br>
    </p>
    <br>
    <h1 align="center" style="color: black; font-size: large; font-family: <?= $fontFamily ?>;">
        <b><?= Translate::_('people', 'What to do if you need to regain access to your account') ?></b>
    </h1>
    <p align="left" style="color: black; font-size: medium; font-family: <?= $fontFamily ?>;">
        <?= Translate::_(
            'people',
            'Go to  <a href="https://www.identitybank.eu/passwordrecovery">https://www.identitybank.eu/passwordrecovery</a> and follow the instructions displayed.'
        ) ?><br>
    </p>
    <br>
</div>
