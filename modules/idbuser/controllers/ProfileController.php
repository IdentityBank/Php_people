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

namespace app\modules\idbuser\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\controllers\IdbController;
use app\helpers\Translate;
use DateTime;
use Exception;
use idbyii2\helpers\Localization;
use idbyii2\models\db\PeopleDeleteRequest;
use idbyii2\models\db\PeopleUserData;
use idbyii2\models\form\ChangeContactForm;
use idbyii2\models\form\ChangePasswordForm;
use idbyii2\models\identity\IdbPeopleUser;
use kartik\mpdf\Pdf;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class ProfileController
 *
 * @package app\modules\idbuser\controllers
 */
class ProfileController extends IdbController
{

    /**
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $hasRequest = PeopleDeleteRequest::hasRequest(Yii::$app->user->identity->peopleDbUserId);
        $days = PeopleDeleteRequest::getDaysToDelete(Yii::$app->user->identity->peopleDbUserId);

        $accountName = ((empty(Yii::$app->user->identity->accountName)) ? '' : Yii::$app->user->identity->accountName);
        $userId = ((empty(Yii::$app->user->identity->userId)) ? '' : Yii::$app->user->identity->userId);
        $accountNumber = ((empty(Yii::$app->user->identity->accountNumber)) ? ''
            : Yii::$app->user->identity->accountNumber);
        $authKey = ((empty(Yii::$app->user->identity->authKey)) ? '' : Yii::$app->user->identity->authKey);
        $login = ((empty(Yii::$app->user->identity->login)) ? '' : Yii::$app->user->identity->login);
        $email = ((empty(Yii::$app->user->identity->email)) ? '' : Yii::$app->user->identity->email);
        $phone = ((empty(Yii::$app->user->identity->mobile)) ? '' : Yii::$app->user->identity->mobile);
        $name = ((empty(Yii::$app->user->identity->name)) ? '' : Yii::$app->user->identity->name);
        $surname = ((empty(Yii::$app->user->identity->surname)) ? '' : Yii::$app->user->identity->surname);

        $this->view->title = Translate::_('people', 'User profile');

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'index',
                    'contentParams' => compact(
                        'accountName',
                        'login',
                        'userId',
                        'accountNumber',
                        'authKey',
                        'email',
                        'phone',
                        'name',
                        'surname',
                        'hasRequest',
                        'days'
                    )
                ]
            ]
        );
    }

    /**
     * @return string|Response
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionChangePassword()
    {
        $model = new ChangePasswordForm;

        if (!empty(Yii::$app->request->post('ChangePasswordForm'))) {
            $model->load(Yii::$app->request->post());

            if ($model->validate()) {
                $passwordValidated = Yii::$app->user->identity->validatePassword($model->oldPassword);

                if ($passwordValidated) {
                    $userDataModel = PeopleUserData::instantiate(['uid' => Yii::$app->user->identity->getId()]);
                    $userDataModel = PeopleUserData::findOne(
                        [
                            'uid' => Yii::$app->user->identity->getId(),
                            'key_hash' => $userDataModel->getKeyHash(Yii::$app->user->identity->getId(), 'password')
                        ]
                    );
                    if ($userDataModel) {
                        $saved = $userDataModel->updatePassword(
                            $model->password,
                            Yii::$app->user->identity->accountNumber
                        );
                        if (!$saved) {
                            $model->addErrors($userDataModel->getErrors());
                        } else {
                            Yii::$app->session->setFlash(
                                'successMessage',
                                Translate::_('people', 'Password successfully changed.')
                            );

                            $model = new ChangePasswordForm;
                        }
                    }
                } else {
                    $model->addError(
                        'oldPassword',
                        Translate::_('people', 'Incorrect password entered please try again.')
                    );
                }

            }
        }
        $this->view->title = Translate::_('people', 'Change password');

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'changepassword',
                    'menu_active_section' => '[menu][account]',
                    'menu_active_item' => '[menu][account][user_profile]',
                    'contentParams' => ['model' => $model,]
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function actionPasswordchanged()
    {
        $this->view->title = Translate::_('people', 'Password changed');

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'passwordchanged',
                    'menu_active_section' => '[menu][account]',
                    'menu_active_item' => '[menu][account][user_profile]',
                ]
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function actionDeleteAccount()
    {
        $peopleId = Yii::$app->user->identity->peopleDbUserId;
        if (PeopleDeleteRequest::hasRequest($peopleId)) {
            $deleteRequest = PeopleDeleteRequest::find()->where(compact('peopleId'))->one();
            try {
                $deleteRequest->delete();

                Yii::$app->session->setFlash(
                    'successMessage',
                    Translate::_(
                        'people',
                        'You\'re data is no more in delete queue.'
                    )
                );
            } catch (Throwable $e) {
                Yii::error('DELETE ACCOUNT ERROR!');
                Yii::error($e->getMessage());
                Yii::$app->session->setFlash(
                    'dangerMessage',
                    Translate::_(
                        'people',
                        'Ther\'s something goes wrong. Please Contact with administrator.'
                    )
                );
            }
        } else {
            $deleteRequest = new PeopleDeleteRequest();
            $deleteRequest->peopleId = $peopleId;
            $deleteRequest->timestamp = Localization::getDatabaseDateTime(new DateTime());
            $deleteRequest->save();

            Yii::$app->session->setFlash(
                'successMessage',
                Translate::_(
                    'people',
                    'Your account will be deleted in 30 days. You can cancel this process at any time.'
                )
            );
        }

        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * @return string|Response
     */
    public function actionChangeContact()
    {
        $verificationSession = Yii::$app->session->get('verificationModuleToken', []);
        $url = Url::to(Yii::$app->request->url, true);

        if (
            ArrayHelper::getValue($verificationSession, 'url', '/') === $url
            && ArrayHelper::getValue($verificationSession, 'status', 'failed') === 'success'
        ) {
            $request = Yii::$app->request;

            $model = new ChangeContactForm();

            if (!empty($request->post('ChangeContactForm'))) {
                $model->load($request->post());

                if ($model->validate()) {
                    Yii::$app->session->set('verificationModuleCode', [
                        'mobile' => $model->mobile,
                        'email' => $model->email
                    ]);

                    $this->redirect(['save-contact']);
                }
            } else {
                $model->email = Yii::$app->user->identity->email;
                $model->mobile = Yii::$app->user->identity->mobile;
            }

            return $this->render(
                '@app/themes/metronic/views/site/template',
                [
                    'params' => [
                        'menu_active_section' => '[menu][account]',
                        'menu_active_item' => '[menu][account][user_profile]',
                        'content' => 'contact',
                        'contentParams' => [
                            'model' => $model
                        ]
                    ]

                ]
            );
        } else {
            Yii::$app->session->set('verificationModuleToken', array_merge(Yii::$app->session->get('verificationModuleToken', []), ['url' => $url, 'status' => 'failed']));

            return $this->redirect(['/idbverification/token']);
        }
    }

    /**
     * @return string|Response
     */
    public function actionSaveContact()
    {
        $verificationSession = Yii::$app->session->get('verificationModuleCode', []);
        $url = Url::to(Yii::$app->request->url, true);

        if (
            ArrayHelper::getValue($verificationSession, 'url', '/') === $url
            && ArrayHelper::getValue($verificationSession, 'status', 'failed') === 'success'
        ) {
            try {
                $email = PeopleUserData::getUserDataByKeys(Yii::$app->user->identity->id, ['email'])[0];
                $email->value = $verificationSession['email'];

                $mobile = PeopleUserData::getUserDataByKeys(Yii::$app->user->identity->id, ['mobile'])[0];
                $mobile->value = $verificationSession['mobile'];

                $token = PeopleUserData::getUserDataByKeys(Yii::$app->user->identity->id, ['passwordToken'])[0];
                $token->value = Yii::$app->user->identity->generateToken($email->value, $mobile->value);
                Yii::$app->getModule('idbverification');
                if (!$mobile->save() || !$email->save()) {
                    throw new Exception("Cant save mobile or email.");
                }
            } catch (Exception $e) {
                Yii::$app->session->setFlash('dangerMessage', Translate::_('people', "We can't change email or mobile. Please contact with administrator."));
                return $this->redirect(['/']);
            }

            return $this->render(
                '@app/themes/metronic/views/site/template',
                [
                    'params' => [
                        'content' => 'token',
                        'menu_active_section' => '[menu][account]',
                        'menu_active_item' => '[menu][account][user_profile]',
                    ]
                ]
            );
        } else {
            Yii::$app->session->set('verificationModuleCode', array_merge(Yii::$app->session->get('verificationModuleCode', []), ['url' => $url, 'status' => 'failed']));

            return $this->redirect(['/idbverification/code/email']);
        }
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionGetToken()
    {
        $uid = Yii::$app->user->identity->id;

        $userData = [];

        if (!empty(Yii::$app->user->identity->userId)) {
            $userData[IdbPeopleUser::instance()->getAttributeLabel('loginName')] = Yii::$app->user->identity->userId;
        }
        if (!empty(Yii::$app->user->identity->accountNumber)) {
            $userData[IdbPeopleUser::instance()->getAttributeLabel('accountNumber')] = Yii::$app->user->identity->accountNumber;
        }

        $peopleData = new PeopleUserData();

        $passwordToken = PeopleUserData::find()->where(
            ['uid' => $uid, 'key_hash' => $peopleData->getKeyHash($uid, 'passwordToken')]
        )->one();

        $content = $this->renderPartial(
            '@idbyii2/static/templates/PDFs/passwordToken.php',
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
}



################################################################################
#                                End of file                                   #
################################################################################
