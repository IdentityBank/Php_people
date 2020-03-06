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

namespace app\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\helpers\PeopleConfig;
use app\helpers\Translate;
use app\models\IdbPeopleLoginForm;
use Exception;
use idbyii2\components\PortalApi;
use idbyii2\helpers\IdbMfaHelper;
use idbyii2\helpers\IdbPortalApiActions;
use idbyii2\helpers\IdbSecurity;
use idbyii2\helpers\IdbYii2Login;
use idbyii2\helpers\Localization;
use idbyii2\helpers\Totp;
use idbyii2\models\db\PeopleAuthlog;
use idbyii2\models\db\PeopleUserData;
use idbyii2\models\identity\IdbPeopleUser;
use idbyii2\models\identity\IdbUser;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class SiteController
 *
 * @package app\controllers
 */
class SiteController extends IdbController
{

    /**
     * @return mixed
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionHelp()
    {
        return $this->render('help');
    }

    /**
     * @param null $idbjwt
     *
     * @return mixed
     */
    public function actionIdbLogin($idbjwt = null)
    {
        return IdbYii2Login::idbLogin($idbjwt, 'people', $this, new IdbPeopleLoginForm());
    }

    /**
     * @param null $post
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionLogin($post = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        Yii::$app->session->destroy();
        $model = new IdbPeopleLoginForm();
        if (empty($post)) {
            $post = Yii::$app->request->post();
        }
        if ($model->load($post) && $model->login()) {
            PeopleAuthlog::login(Yii::$app->user->id);
            if (!empty(Yii::$app->user->getReturnUrl())) {
                if (!empty($post['jwt']) && $post['jwt']) {
                    return $this->goHome();
                } else {
                    $this->redirect(Yii::$app->user->getReturnUrl());
                }
                Yii::$app->end();
            }

            return $this->goHome();
        } else {
            $userId = trim($model->userId);
            $accountNumber = trim($model->accountNumber);
            $login = IdbPeopleUser::createLogin($userId, $accountNumber);
            $userAccount = IdbPeopleUser::findUserAccountByLogin($login);
            if ($userAccount) {
                PeopleAuthlog::error($userAccount->uid, ['p' => strlen($model->accountPassword) . "_" . time()]);
            }
        }

        return $this->render('login', ['model' => $model]);
    }

    /**
     * @return void
     */
    public function actionProfile()
    {
        $this->redirect(Url::to(['/idbuser/profile']));
    }

    /**
     * @param null $post
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionMfa()
    {
        $this->layout = 'fullscreen';
        $model = ['code', 'code_next', 'mfa'];
        $model = new DynamicModel($model);
        $post = Yii::$app->request->post();

        if (
            !empty($post['action'])
            && $post['action'] === 'skip-mfa'
            && PeopleConfig::get()->isMfaSkipEnabled()
        ) {
            $value = IdbUser::createLogin(
                Yii::$app->user->identity->userId,
                Yii::$app->user->identity->accountNumber
            );
            $model->code = $value;
            $model->mfa = json_encode(
                ['type' => 'skip', 'timestamp' => Localization::getDateTimeFileString(), 'value' => $value]
            );
            $modelData = PeopleUserData::instantiate(
                [
                    'uid' => Yii::$app->user->identity->id,
                    'key' => 'mfa',
                    'value' => $model->mfa
                ]
            );
            if (
                $modelData->save()
                && Yii::$app->user->identity->validateMfa($model)
            ) {
                return $this->goHome();
            }
            $model->code = null;
        }

        if (empty(Yii::$app->user->identity->mfa)) {
            $model->addRule(['code', 'code_next'], 'required')
                  ->addRule(['code', 'code_next'], 'string', ['max' => 16])
                  ->addRule(
                      'code',
                      'compare',
                      [
                          'compareAttribute' => 'code_next',
                          'operator' => '!==',
                          'message' => Translate::_('people', "Enter two consecutive authentication codes.")
                      ]
                  )
                  ->addRule(['mfa'], 'string', ['max' => 128]);

            if (
                !empty($post)
                && $model->load($post)
                && !empty($model->mfa)
                && !empty($model->code)
                && !empty($model->code_next)
                && ($model->code !== $model->code_next)
            ) {
                $model->code = preg_replace('/\s+/', "", $model->code);
                $model->code_next = preg_replace('/\s+/', "", $model->code_next);

                if (
                    Totp::verify($model->mfa, $model->code)
                    && Totp::verify($model->mfa, $model->code_next)
                    && ($model->code !== $model->code_next)
                ) {
                    $model->mfa = json_encode(
                        [
                            'type' => 'totp',
                            'timestamp' => Localization::getDateTimeFileString(),
                            'value' => $model->mfa
                        ]
                    );
                    $modelData = PeopleUserData::instantiate(
                        [
                            'uid' => Yii::$app->user->identity->id,
                            'key' => 'mfa',
                            'value' => $model->mfa
                        ]
                    );
                    if (
                        $modelData->save()
                        && Yii::$app->user->identity->validateMfa($model)
                    ) {
                        if (Yii::$app->session->has('firstLogin')) {
                            Yii::$app->session->remove('firstLogin');

                            return $this->redirect(['/business/edit']);
                        }

                        return $this->goHome();
                    }
                } else {
                    $errorMsg = Translate::_('people', 'Invalid code');
                    $model->addError('code', $errorMsg);
                    $model->addError('code_next', $errorMsg);
                }
            } else {
                $model->mfa = Yii::$app->user->identity->generateMfaSecurityKey();
            }

            return $this->render(
                'createMfa',
                ArrayHelper::merge(['model' => $model], IdbMfaHelper::getMfaViewVariables($model, PeopleConfig::get()))
            );
        } else {
            $model->addRule(['code'], 'required')
                  ->addRule('code', 'string', ['max' => 16]);

            if (
                PeopleConfig::get()->isMfaSkipEnabled()
                && $model->load(
                    [
                        'DynamicModel' => [
                            'code' => IdbUser::createLogin(
                                Yii::$app->user->identity->userId,
                                Yii::$app->user->identity->accountNumber
                            )
                        ]
                    ]
                )
                && Yii::$app->user->identity->validateMfa($model)
            ) {
                return $this->goHome();
            }
            $model->code = null;

            if (!empty($post)
            ) {
                if (
                    $model->load($post)
                    && Yii::$app->user->identity->validateMfa($model)
                ) {
                    if (!empty(Yii::$app->user->getReturnUrl())) {
                        $this->redirect(Yii::$app->user->getReturnUrl());
                        Yii::$app->end();
                    }

                    if (Yii::$app->session->has('firstLogin')) {
                        Yii::$app->session->remove('firstLogin');

                        return $this->redirect(['/business/edit']);
                    }

                    return $this->goHome();
                } else {
                    $this->actionLogout();
                }
            } else {
                if (Yii::$app->user->identity->validateMfa()) {
                    if (Yii::$app->session->has('firstLogin')) {
                        Yii::$app->session->remove('firstLogin');

                        return $this->redirect(['/business/edit']);
                    }

                    return $this->goHome();
                }
            }

            return $this->render('mfa', ['model' => $model]);
        }
    }

    /**
     * @return mixed
     */
    public function actionLogout()
    {
        PeopleAuthlog::logout(Yii::$app->user->id);
        Yii::$app->session->destroy();
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return array|false|string|null
     * @throws \yii\base\ExitException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIdbApi()
    {
        $portalBusinessApi = PortalApi::getBusinessApi();

        return IdbPortalApiActions::execute($portalBusinessApi, apache_request_headers(), $_REQUEST);
        Yii::$app->end();
    }
}

################################################################################
#                                End of file                                   #
################################################################################
