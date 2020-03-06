<?php

?>

<?php if (Yii::$app->session->hasFlash('successMessage')): ?>
    <div class="alert alert-success fade show" role="alert">
        <div class="alert-text"><?= Yii::$app->session->getFlash('successMessage') ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('infoMessage')): ?>
    <div class="alert alert-info fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
        <div class="alert-text"><?= Yii::$app->session->getFlash('infoMessage') ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('warningMessage')): ?>
    <div class="alert alert-warning fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text"><?= Yii::$app->session->getFlash('warningMessage') ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('dangerMessage')): ?>
    <div class="alert alert-danger fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
        <div class="alert-text"><?= Yii::$app->session->getFlash('dangerMessage') ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
<?php endif; ?>


