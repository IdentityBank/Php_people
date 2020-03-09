<?php

use app\themes\idb\assets\IdbAsset;
use idbyii2\helpers\StaticContentHelper;
use yii\helpers\Html;

$assetBundle = IdbAsset::register($this);
$section2Enabled = (!empty($this->params['section2Enabled']) ? ($this->params['section2Enabled']) : 'show');
// $this->params['section2Enabled'] = 'hide';
$sectionExtraEnabled = (!empty($this->params['sectionExtraEnabled']) ? ($this->params['sectionExtraEnabled']) : 'hide');
$sectionExtraBody = (!empty($this->params['sectionExtraBody']) ? ($this->params['sectionExtraBody']) : '');
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= $assetBundle->getAssetUrl() ?>images/favicon.png" rel="shortcut icon"
          type="image/vnd.microsoft.icon"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="//fonts.googleapis.com/css?family=Handlee:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=latin"
          rel="stylesheet" type="text/css"/>
    <link href="//fonts.googleapis.com/css?family=Ubuntu:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic-ext"
          rel="stylesheet" type="text/css"/>
    <?php $this->head() ?>
</head>

<body class="site helix-ultimate com-content view-featured layout-default task-none itemid-181 nl-nl ltr layout-fluid offcanvas-init offcanvs-position-right">
<?php $this->beginBody() ?>
<div class="body-wrapper">
    <div class="body-innerwrapper">

        <section id="sp-menu">
            <div class="container">
                <div class="container-inner">
                    <div class="row">
                        <div id="sp-logo" class="col-8 col-sm-10 col-md-10 col-lg-5 ">
                            <div class="sp-column ">
                                <div class="logo">
                                    <a href="https://www.identitybank.eu"><img class="logo-image"
                                                                               src="<?= $assetBundle->getAssetUrl(
                                                                               ) ?>images/logo.png" alt="Identity Bank"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($section2Enabled === 'show') { ?>
            <section id="sp-section-2">
                <div class="row">
                    <div id="sp-title" class="col-lg-12 ">
                        <div class="sp-column ">
                            <div class="sp-page-title">
                                <div class="container">
                                    <h2 class="sp-page-title-heading"><?= Html::encode($this->title) ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?>

        <?php if ($sectionExtraEnabled === 'show') {
            echo $sectionExtraBody;
        } ?>

        <section id="sp-main-body" style="padding-bottom: 140px;">
            <?= $content ?>
        </section>

        <footer id="sp-footer" class="navbar-default navbar-fixed-bottom">
            <div class="container">
                <div class="container-inner">
                    <div class="row">
                        <div id="sp-footer1" class="col-lg-12 ">
                            <div class="sp-column ">
                                <div class="sp-module ">
                                    <div class="sp-module-content">
                                        <div class="custom">
                                            <p style="text-align: center;">
                                                <?= StaticContentHelper::getFooter(
                                                    ['footer_language' => Yii::$app->language]
                                                ); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
