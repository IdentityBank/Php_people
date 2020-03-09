<?php

use app\assets\LoginAsset;
use app\helpers\Translate;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Translate::_('people', 'Login page');
$this->context->layout = 'clear';
LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="text-center">
<?php $this->beginBody() ?>

<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr class="style" width="70%">
    <p><?= Translate::_('people', 'Please fill out the following fields to login to Identity Bank') ?></p>

    <?php $form = ActiveForm::begin(
        [
            'id' => 'login-form',
            'options' => ['class' => 'form-signin'],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'sr-only'],
            ],
        ]
    ); ?>

    <?php if ($model->getErrors()) { ?>
        <?= Html::tag('div', $form->errorSummary($model, ['header' => '']), ['class' => 'alert alert-danger']) ?>
    <?php } ?>

    <?= $form->field($model, 'userId', ['enableClientValidation' => false])->textInput(
        ['placeholder' => $model->getAttributeLabel('userId')]
    ) ?>
    <?= $form->field($model, 'accountNumber')->textInput(
        ['placeholder' => $model->getAttributeLabel('accountNumber')]
    ) ?>
    <?= $form->field($model, 'accountPassword')->passwordInput(
        ['placeholder' => $model->getAttributeLabel('accountPassword')]
    ) ?>
    <?php if (PeopleConfig::get()->getYii2PeopleEnableAutoLogin()) { ?>
        <?= $form->field($model, 'rememberMe')->checkbox(['template' => "{input} {label}\n{error}",])->label(
            $model->getAttributeLabel('rememberMe')
        ) ?>
    <?php } ?>
    <?= Html::submitButton(
        Translate::_('people', 'Login'),
        ['class' => 'btn btn-lg btn-primary btn-block', 'name' => 'login-button']
    ) ?>

    <?php ActiveForm::end(); ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
