<?php

use idbyii2\helpers\StaticContentHelper;

?>

<footer>
    <div class="kt-footer  kt-footer--extended  kt-grid__item" id="kt_footer"
         style="background-image: url('<?= $assetUrl ?>/assets/media/bg/bg-2.jpg');">
        <div class="kt-footer__bottom">
            <div class="kt-container ">
                <div class="kt-footer__wrapper">
                    <div class="kt-footer__logo">
                        <div class="kt-footer__copyright">
                            <?= StaticContentHelper::getFooter(['footer_language' => Yii::$app->language]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
