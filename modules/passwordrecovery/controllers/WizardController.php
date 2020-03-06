<?php
# * ********************************************************************* *
# *                                                                       *
# *   People Portal                                                       *
# *   This file is part of people. This project may be found at:          *
# *   https://github.com/IdentityBank/Php_people.                         *
# *                                                                       *
# *   Copyright (C) 2020 by Identity Bank. All Rights Reserved.           *
# *   https://www.identitybank.eu - You belong to you                     *
# *                                                                       *
# *   This program is free software: you can redistribute it and/or       *
# *   modify it under the terms of the GNU Affero General Public          *
# *   License as published by the Free Software Foundation, either        *
# *   version 3 of the License, or (at your option) any later version.    *
# *                                                                       *
# *   This program is distributed in the hope that it will be useful,     *
# *   but WITHOUT ANY WARRANTY; without even the implied warranty of      *
# *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the        *
# *   GNU Affero General Public License for more details.                 *
# *                                                                       *
# *   You should have received a copy of the GNU Affero General Public    *
# *   License along with this program. If not, see                        *
# *   https://www.gnu.org/licenses/.                                      *
# *                                                                       *
# * ********************************************************************* *

################################################################################
# Namespace                                                                    #
################################################################################

namespace app\modules\passwordrecovery\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\helpers\Translate;
use idbyii2\enums\EmailActionType;
use idbyii2\helpers\CodeVerification;
use idbyii2\helpers\EmailTemplate;
use idbyii2\helpers\PasswordToken;
use idbyii2\helpers\Sms;
use idbyii2\models\db\SignupPeople;
use idbyii2\models\form\EmailVerificationForm;
use idbyii2\models\form\SmsVerificationForm;
use idbyii2\models\form\NewPasswordForm;
use idbyii2\models\form\PasswordRecoveryForm;
use idbyii2\models\identity\IdbPeopleUser;
use Yii;
use yii\web\Controller;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Default controller for the `passwordrecovery` module
 */
class WizardController extends Controller
{

    /** @var PasswordToken */
    private $passwordTokenHelper;

    /**
     * @param $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    public function beforeAction($action)
    {
        $this->passwordTokenHelper = new PasswordToken();

        return parent::beforeAction($action);
    }

    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new PasswordRecoveryForm();

        if (!empty($request->post('PasswordRecoveryForm'))) {
            $model->load($request->post());

            if ($model->validate()) {
                $passwordToken = $this->passwordTokenHelper->decodeToken($model->token);

                if (
                    (!empty($passwordToken))
                    && (
                        $model->email === $passwordToken['email']
                        && $model->mobile === $passwordToken['mobile']
                    )
                ) {
                    $identity = IdbPeopleUser::findIdentity($passwordToken['uid']);
                    if (!empty($identity)) {
                        Yii::$app->session->set('recoveryToken', $model->token);

                        return $this->redirect(['email-verification']);
                    }
                } else {
                    $model->addError('token', Translate::_('people', 'The provided data are not correct.'));
                }
            }
        }

        return $this->render('form', compact('model'));
    }

    /**
     * @return mixed
     */
    public function actionEmailVerification()
    {
        $request = Yii::$app->request;
        $model = new EmailVerificationForm();

        if (CodeVerification::checkCode($request->post('code'), Yii::$app->session->get('emailCode'))) {
            Yii::$app->session->set('tryCount', 3);

            return $this->redirect(['sms-verification']);
        } elseif (!empty($request->post('code'))) {
            Yii::$app->session->setFlash(
                'tryMessage',
                Translate::_('people', 'Incorrect email code entered please try again.')
            );
        }

        $token = $this->passwordTokenHelper->decodeToken(Yii::$app->session->get('recoveryToken'));

        $code = SignupPeople::generateVeryficationCodeStatic();
        Yii::$app->session->set('emailCode', $code);

        EmailTemplate::sendEmailByAction(
            EmailActionType::PEOPLE_EMAIL_VERIFICATION,
            [
                'code' => $code,
                'firstName' => $token['name'],
                'lastName' => $token['surname'],
                'businessName' => $token['businessAccountName'] ?? ''
            ],
            Translate::_('people', 'Confirm code'),
            $token['email'],
            Yii::$app->language
        );

        $code = explode('.', $code);
        $codeFirst = $code[0];
        $codeThird = $code[2];

        return $this->render('emailVerification', compact('model', 'codeFirst', 'codeThird'));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function actionSmsVerification()
    {
        $request = Yii::$app->request;
        $model = new SmsVerificationForm();
        $model->captchaEnabled = false;

        if (CodeVerification::checkCode($request->post('code'), Yii::$app->session->get('smsCode'))) {
            return $this->redirect(['password']);
        } elseif ($request->post('code')) {
            Yii::$app->session->set(
                'tryCount',
                Yii::$app->session->get('tryCount') - 1
            );

            Yii::$app->session->setFlash(
                'tryMessage',
                Translate::_('people', 'Incorrect SMS or email code entered')
            );
        }

        $tryCount = Yii::$app->session->get('tryCount');

        $token = $this->passwordTokenHelper->decodeToken(Yii::$app->session->get('recoveryToken'));

        $code = SignupPeople::generateVeryficationCodeStatic();
        Yii::$app->session->set('smsCode', $code);

        Sms::sendVerificationCode(
            $token['mobile'],
            $code
        );

        $code = explode('.', $code);
        $codeFirst = $code[0];
        $codeThird = $code[2];

        return $this->render('smsVerification', compact('model', 'codeFirst', 'codeThird', 'tryCount'));
    }

    /**
     * @return mixed
     */
    public function actionPassword()
    {
        $request = Yii::$app->request;
        $model = new NewPasswordForm();

        if (!empty($request->post('NewPasswordForm'))) {
            $model->load($request->post());
            if ($model->validate()) {
                $token = $this->passwordTokenHelper->decodeToken(Yii::$app->session->get('recoveryToken'));

                IdbPeopleUser::changePassword($token['uid'], $model->password);

                return $this->redirect(['/login']);
            }
        }

        return $this->render('passwordForm', compact('model'));
    }
}

################################################################################
#                                End of file                                   #
################################################################################
