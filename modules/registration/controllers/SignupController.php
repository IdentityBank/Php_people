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

namespace app\modules\registration\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\helpers\PeopleConfig;
use app\helpers\Translate;
use app\models\IdbPeopleLoginForm;
use idb\idbank\PeopleIdBankClient;
use idbyii2\audit\AuditComponent;
use idbyii2\components\PortalApi;
use idbyii2\enums\EmailActionType;
use idbyii2\helpers\CodeVerification;
use idbyii2\helpers\EmailTemplate;
use idbyii2\helpers\IdbAccountId;
use idbyii2\helpers\PasswordToken;
use idbyii2\helpers\Signup;
use idbyii2\helpers\Sms;
use idbyii2\models\db\PeopleAuthlog;
use idbyii2\models\db\PeopleUserData;
use idbyii2\models\db\SignupPeople;
use idbyii2\models\form\IdbPeopleSignUpAuthForm;
use idbyii2\models\form\IdbPeopleSignUpForm;
use idbyii2\models\form\EmailVerificationForm;
use idbyii2\models\form\SmsVerificationForm;
use idbyii2\models\idb\IdbBankClientPeople;
use idbyii2\models\identity\IdbPeopleUser;
use kartik\mpdf\Pdf;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class SignupController
 *
 * @package app\controllers
 */
class SignupController extends Controller
{

    /** @var SignupPeople */
    private $signUpModel = null;

    /**
     * @param $page
     *
     * @return string
     */
    public function getPageTitle($page)
    {
        return Translate::_('people', 'Identity Bank') . " :: $page";
    }

    /**
     * @param $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        AuditComponent::actionAudit($action);

        $this->enableCsrfValidation = false;

        switch ($action->id) {
            case 'have-account':
            case 'confirmation':
            case 'email-verification':
            case 'sms-verification':
            case 'auth':
            case 'privacy-policy':
            case 'success':
            case 'get-token':
            case 'login':
                $request = Yii::$app->request;
                /** @var SignupPeople $signUpModel */
                $signUpModel = SignupPeople::findByAuthKey($request->get('id'));

                if ($signUpModel === null) {
                    Yii::$app->session->setFlash(
                        'idMessage',
                        Translate::_('people', 'Your ID doesn\'t exist')
                    );

                    $this->redirect(['error']);

                    return false;
                } else {
                    $this->signUpModel = $signUpModel;
                }

                break;
        }

        $return = parent::beforeAction($action);

        return $return;
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionHaveAccount($id)
    {
        if (Yii::$app->request->isPost) {
            switch (Yii::$app->request->post('action')) {
                case 'login':
                    return $this->redirect(['login', 'id' => $id]);
                case 'register':
                    return $this->redirect(['confirmation', 'id' => $id]);
            }
        }

        $this->signUpModel->setDataChunk('haveAccount', Yii::$app->request->post('submit'));
        $this->signUpModel->save();

        return $this->render(
            'have-account',
            [
                'id' => $id,
                'businessAccountName' => $this->signUpModel->getDataChunk('businessAccountName'),
            ]
        );
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionConfirmation($id)
    {
        $model = new IdbPeopleSignUpForm();
        $request = Yii::$app->request;

        if ($request->isPost) {
            $post = array_key_exists('IdbPeopleSignUpForm', $request->post()) ? $request->post()['IdbPeopleSignUpForm']
                : null;

            if (is_array($post)) {
                $model->userId = $post['userId'];
                $model->name = $post['name'];
                $model->surname = $post['surname'];
                $model->email = $post['email'];
                $model->mobile = $post['mobile'];

                $model->password = $post['password'];
                $model->repeatPassword = $post['repeatPassword'];

                $model->businessAccountName = $this->signUpModel->getDataChunk('businessAccountName');
                $model->businessAccountid = $this->signUpModel->getDataChunk('businessAccountid');
                $model->businessDatabaseId = $this->signUpModel->getDataChunk('businessDatabaseId');
                $model->businessDatabaseUserId = $this->signUpModel->getDataChunk('businessDatabaseUserId');
                $model->businessAccountid = $this->signUpModel->getDataChunk('businessAccountid');
                $model->businessOrgazniationId = $this->signUpModel->getDataChunk('businessOrgazniationId');
                $model->businessUserId = $this->signUpModel->getDataChunk('businessUserId');
            }

            if ($model->validate()) {
                $this->signUpModel->setDataFromForm($model);
                $this->signUpModel->setDataChunk('authKey', $id);
                $this->signUpModel->save();

                return $this->redirect(['privacy-policy', 'id' => $id]);
            }
        }

        $model->name = $this->signUpModel->getDataChunk('name');
        $model->surname = $this->signUpModel->getDataChunk('surname');
        $model->mobile = $this->signUpModel->getDataChunk('mobile');
        $model->email = $this->signUpModel->getDataChunk('email');
        $model->withoutEmail = $this->signUpModel->getDataChunk('withoutEmail');
        $model->withoutMobile = $this->signUpModel->getDataChunk('withoutMobile');
        $model->businessAccountName = $this->signUpModel->getDataChunk('businessAccountName');

        return $this->render(
            'confirmation',
            [
                'id' => $id,
                'model' => $model
            ]
        );
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionAuth($id)
    {
        $model = new IdbPeopleSignUpAuthForm();
        $tryCount = $this->signUpModel->getDataChunk('tryCount');

        if (!empty(Yii::$app->request->post() ['IdbPeopleSignUpAuthForm'])) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {
                if (
                    $this->signUpModel->getDataChunk('smsCode') == $model->smsCode
                    && $this->signUpModel->getDataChunk('emailCode') == $model->emailCode
                ) {
                    $createIdbUserStatus = IdbPeopleUser::createFromSignUpModel($this->signUpModel);

                    $this->signUpModel->setDataChunk('uid', $createIdbUserStatus['uid']);
                    $this->sendLoginData();
                    $this->signUpModel->save();

                    return $this->redirect(['login', 'id' => $id]);
                } else {
                    $this->signUpModel->setDataChunk(
                        'tryCount',
                        $this->signUpModel->getDataChunk('tryCount') - 1
                    );
                    $tryCount = $this->signUpModel->getDataChunk('tryCount');
                    $this->signUpModel->save();
                    Yii::$app->session->setFlash(
                        'tryMessage',
                        Translate::_('people', 'Incorrect SMS or email code entered')
                    );
                }
            }
        }

        $data = [
            'id' => $id,
            'tryCount' => $tryCount,
            'model' => $model
        ];

        return $this->render('auth', $data);
    }

    /**
     * @param null $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function actionEmailVerification($id = null)
    {
        $request = Yii::$app->request;
        $model = new EmailVerificationForm();

        if (CodeVerification::checkCode($request->post('code'), $this->signUpModel->getdataChunk('emailCode'))) {
            $this->signUpModel->setDataChunk('currentState', 'sms-verification');
            $this->signUpModel->save();

            return $this->redirect(['sms-verification', 'id' => $id]);
        } elseif (!empty($request->post('code'))) {
            Yii::$app->session->setFlash(
                'tryMessage',
                Translate::_('people', 'Incorrect email code entered please try again.')
            );
        }

        $this->signUpModel->generateVeryficationCode('emailCode');
        $portalBusinessApi = PortalApi::getBusinessApi();

        $portalBusinessApi->requestSendEmail(
            [
                'action' => 'PEOPLE_EMAIL_VERIFICATION',
                'parameters' => [
                    'code' => $this->signUpModel->getDataChunk('emailCode'),
                    'firstName' => $this->signUpModel->getDataChunk('name'),
                    'lastName' => $this->signUpModel->getDataChunk('surname'),
                    'businessName' => $this->signUpModel->getDataChunk('businessAccountName')
                ],
                'title' => Translate::_('people', 'Confirm code'),
                'to' => $this->signUpModel->getDataChunk('email'),
                'iso' => Yii::$app->language,
                'oid' => $this->signUpModel->getDataChunk('businessOrgazniationId')
            ]
        );

        $code = explode('.', $this->signUpModel->getDataChunk('emailCode'));
        $codeFirst = $code[0];
        $codeThird = $code[2];

        return $this->render('emailVerification', compact('id', 'model', 'codeFirst', 'codeThird'));
    }

    /**
     * @param null $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function actionSmsVerification($id = null)
    {
        $request = Yii::$app->request;
        $model = new SmsVerificationForm();
        $model->captchaEnabled = false;
        $tryCount = $this->signUpModel->getDataChunk('tryCount');

        if (CodeVerification::checkCode($request->post('code'), $this->signUpModel->getDataChunk('smsCode'))) {
            $createIdbUserStatus = IdbPeopleUser::createFromSignUpModel($this->signUpModel);

            $this->signUpModel->setDataChunk('uid', $createIdbUserStatus['uid']);
            $this->sendLoginData();
            $this->signUpModel->save();

            return $this->redirect(['success', 'id' => $id]);
        } elseif (!empty($request->post('code'))) {
            $this->signUpModel->setDataChunk(
                'tryCount',
                --$tryCount
            );
            $this->signUpModel->save();
            Yii::$app->session->setFlash(
                'tryMessage',
                Translate::_('people', 'Incorrect SMS or email code entered')
            );
        }


        $this->signUpModel->generateVeryficationCode('smsCode');
        Sms::sendVerificationCode(
            $this->signUpModel->getDataChunk('mobile'),
            $this->signUpModel->getDataChunk('smsCode')
        );

        $code = explode('.', $this->signUpModel->getDataChunk('smsCode'));
        $codeFirst = $code[0];
        $codeThird = $code[2];

        return $this->render('smsVerification', compact('id', 'model', 'codeFirst', 'codeThird', 'tryCount'));
    }

    /**
     * Action to show account token.
     *
     * @param $id
     *
     * @return string
     */
    public function actionSuccess($id)
    {
        $uid = $this->signUpModel->getDataChunk('uid');

        $this->assignPasswordToken($uid);
        $login = IdbPeopleUser::findIdentity($uid);

        return $this->render('success', compact('id', 'login'));
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionPrivacyPolicy($id)
    {
        $request = Yii::$app->request;

        if (
            !empty($request->getBodyParam('TermsAndConditionsAgreement'))
            && ($request->getBodyParam('TermsAndConditionsAgreement') === 'on')
        ) {


            return $this->redirect(['email-verification', 'id' => $id]);
        }

        $this->signUpModel->setDataChunk('tac', 'agreed');
        $this->signUpModel->save();

        return $this->render('termsAndConditions', ['id' => $id]);
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionLogin($id)
    {
        $model = new IdbPeopleLoginForm();
        $model->load(Yii::$app->request->post());
        $user = IdbPeopleUser::findIdentity($this->signUpModel->getDataChunk('uid'));

        if ($user) {
            $model->userId = $user->userId;
            $model->accountNumber = $user->accountNumber;
        }
        if (
            !empty(Yii::$app->request->post())
            && $model->login()
        ) {
            $accountId = PeopleConfig::get()->getYii2PeopleAccountId();
            $businessDbUserId = $this->signUpModel->getDataChunk('businessDatabaseUserId');
            $peopleDbUserId = IdbAccountId::generatePeopleUserId(
                $accountId,
                Yii::$app->user->identity->id
            );
            $idbClient = IdbBankClientPeople::model($accountId);
            $data = [
                PeopleIdBankClient::DATA_TYPES => [
                    [
                        'attribute' => 'name',
                        'display' => 'name',
                        'value' => $this->signUpModel->getDataChunk('name')
                    ],
                    [
                        'attribute' => 'surname',
                        'display' => 'surname',
                        'value' => $this->signUpModel->getDataChunk('surname')
                    ],
                    [
                        'attribute' => 'mobile',
                        'display' => 'mobile',
                        'value' => $this->signUpModel->getDataChunk('mobile')
                    ],
                    [
                        'attribute' => 'email',
                        'display' => 'email',
                        'value' => $this->signUpModel->getDataChunk('email')
                    ],
                ]
            ];

            $idbClient->addData($peopleDbUserId, $data);
            $idbClient->setRelationBusiness2People($businessDbUserId, $peopleDbUserId);
            PeopleAuthlog::login(Yii::$app->user->id);
            Signup::convertSignUpPeopleToLog($this->signUpModel);

            return $this->redirect(['/']);
        }

        $userId = trim($model->userId);
        $accountNumber = trim($model->accountNumber);
        $login = IdbPeopleUser::createLogin($userId, $accountNumber);
        $userAccount = IdbPeopleUser::findUserAccountByLogin($login);
        if ($userAccount) {
            PeopleAuthlog::error($userAccount->uid, ['p' => strlen($model->accountPassword) . "_" . time()]);
        }

        $businessName = $this->signUpModel->getDataChunk('businessAccountName');

        return $this->render('login', compact('model', 'businessName'));
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetToken($id)
    {
        $uid = $this->signUpModel->getDataChunk('uid');
        $login = IdbPeopleUser::findIdentity($this->signUpModel->getDataChunk('uid'));
        $userData = [];
        if (!empty($login)) {
            if (!empty($login->userId)) {
                $userData[IdbPeopleUser::instance()->getAttributeLabel('loginName')] = $login->userId;
            }
            if (!empty($login->accountNumber)) {
                $userData[IdbPeopleUser::instance()->getAttributeLabel('accountNumber')] = $login->accountNumber;
            }
        }

        $businessData = new PeopleUserData();

        $passwordToken = PeopleUserData::find()->where(
            ['uid' => $uid, 'key_hash' => $businessData->getKeyHash($uid, 'passwordToken')]
        )->one();

        $content = $this->renderPartial(
            '@app/themes/idb/modules/registration/views/signup/pdfs/passwordToken.php',
            compact('passwordToken', 'userData')
        );

        $pdf = new Pdf(
            [
                // set to use core fonts only
                'mode' => Pdf::MODE_UTF8,
                // A4 paper format
                'format' => Pdf::FORMAT_A4,
                // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT,
                // stream to browser inline
                'destination' => Pdf::DEST_BROWSER,
                // your html content input
                'content' => $content,
                // format content from your own css file if needed or use the
                // set mPDF properties on the fly
                'options' => ['title' => 'IDBank recovery token'],
                'defaultFont' => 'DejaVuSans'
            ]
        );

        return $pdf->render();
    }

    /**
     * @return string
     */
    public function actionError()
    {
        return $this->render('error');
    }

    /**
     * @return void
     */
    private function sendLoginData()
    {
        $user = IdbPeopleUser::findIdentity($this->signUpModel->getDataChunk('uid'));

        EmailTemplate::sendEmailByAction(
            EmailActionType::PEOPLE_LOGIN_DATA,
            [
                'loginLink' => Url::toRoute(['/login'], true),
                'loginName' => $user->userId,
                'accountNumber' => $user->accountNumber,
                'firstName' => $this->signUpModel->getDataChunk('name'),
                'lastName' => $this->signUpModel->getDataChunk('surname'),
                'businessName' => $this->signUpModel->getDataChunk('businessAccountName')
            ],
            Translate::_('people', 'Login details'),
            $this->signUpModel->getDataChunk('email'),
            Yii::$app->language
        );
    }

    /**
     * Assign password token to created user
     *
     * @param $uid
     */
    private function assignPasswordToken($uid)
    {
        $businessData = new PeopleUserData();

        $passwordToken = PeopleUserData::find()->where(
            ['uid' => $uid, 'key_hash' => $businessData->getKeyHash($uid, 'passwordToken')]
        )->one();
        if (empty($passwordToken)) {
            $passwordTokenHelper = new PasswordToken();
            $passwordToken = PeopleUserData::instantiate(
                [
                    'uid' => $uid,
                    'key' => 'passwordToken',
                    'value' => $passwordTokenHelper->encodeToken($this->signUpModel->getDataJSONByNamespace())
                ]
            );

            $passwordToken->save();
        }
    }
}

################################################################################
#                                End of file                                   #
################################################################################
