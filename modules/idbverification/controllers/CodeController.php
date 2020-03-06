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

namespace app\modules\idbverification\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\controllers\IdbController;
use app\helpers\Translate;
use Exception;
use idbyii2\components\PortalApi;
use idbyii2\enums\EmailActionType;
use idbyii2\helpers\EmailTemplate;
use idbyii2\helpers\Sms;
use idbyii2\helpers\VerificationCode;
use idbyii2\models\db\PeopleUserData;
use idbyii2\models\db\SignupPeople;
use idbyii2\models\form\EmailVerificationForm;
use idbyii2\models\form\SmsVerificationForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

################################################################################
# Class(es)                                                                    #
################################################################################

class CodeController extends IdbController
{
    private static $params = [
        'menu_active_section' => '[menu][tools]',
        'menu_active_item' => '[menu][tools][idbdata]',
    ];

    /**
     * @return string|Response
     */
    public function actionEmail()
    {
        $request = Yii::$app->request;
        $model = new EmailVerificationForm();
        $model->captchaEnabled = false;
        $verificationSession = Yii::$app->session->get('verificationModuleCode', []);

        if (
        $this->verifyCode(
            $request->post('code'),
            'emailCode',
            [
                'infoMessage' => Translate::_('people', 'Email code was incorrect please try again.')
            ]
        )
        ) {
            Yii::$app->session->set('tryCount', 3);

            return $this->redirect(['sms']);
        }

        $code = SignupPeople::generateVeryficationCodeStatic();
        Yii::$app->session->set('emailCode', $code);

        $email = ArrayHelper::getValue($verificationSession, 'email', Yii::$app->user->identity->email);

        $portalBusinessApi = PortalApi::getBusinessApi();
        $organizationId = PeopleUserData::getUserDataByKeys(
            Yii::$app->user->identity->id,
            ['businessOrgazniationId']
        )[0];
        $portalBusinessApi->requestSendEmail(
            [
                'action' => EmailActionType::PEOPLE_EMAIL_VERIFICATION,
                'parameters' => array_merge(['code' => $code], $this->getMandatoryParameters()),
                'title' => Translate::_('people', 'Confirm code'),
                'to' => $email,
                'iso' => Yii::$app->language,
                'oid' => $organizationId->value
            ]
        );

        $code = explode('.', $code);
        $codeFirst = $code[0];
        $codeThird = $code[2];

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => ArrayHelper::merge
                (
                    self::$params,
                    [
                        'content' => 'email',
                        'contentParams' => compact('codeFirst', 'codeThird', 'model')
                    ]
                )
            ]
        );
    }

    /**
     * @param $code
     * @param $type
     * @param $flashMessages
     *
     * @return bool
     */
    private function verifyCode($code, $type, $flashMessages)
    {
        if (empty($code)) {
            return false;
        }

        if (!empty($code) && count($code) > 11) {

            if (Yii::$app->session->get($type) == VerificationCode::parseFromArray($code)) {
                return true;
            } else {
                foreach ($flashMessages as $key => $value) {
                    Yii::$app->session->setFlash($key, $value);
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getMandatoryParameters()
    {
        try {
            $firstName = PeopleUserData::getUserDataByKeys(
                Yii::$app->user->identity->id,
                ['name']
            )[0];

            $lastName = PeopleUserData::getUserDataByKeys(
                Yii::$app->user->identity->id,
                ['surname']
            )[0];

            $businessName = PeopleUserData::getUserDataByKeys(
                Yii::$app->user->identity->id,
                ['businessAccountName']
            )[0];

            return [
                'firstName' => $firstName->value,
                'lastName' => $lastName->value,
                'businessName' => $businessName->value
            ];
        } catch (Exception $e) {

            Yii::error($e->getMessage());
            return [];
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionSms()
    {
        $request = Yii::$app->request;
        $model = new SmsVerificationForm();
        $model->captchaEnabled = false;
        $verificationSession = Yii::$app->session->get('verificationModuleCode', []);

        if (
        $this->verifyCode(
            $request->post('code'),
            'smsCode',
            [
                'infoMessage' => Translate::_('people', 'Incorrect email or SMS code'),
                'tryCount' => Yii::$app->session->get('tryCount') - 1
            ]
        )
        ) {
            $verificationSession['status'] = 'success';
            Yii::$app->session->set('verificationModuleCode', $verificationSession);

            return $this->redirect(ArrayHelper::getValue($verificationSession, 'url', '/'));
        }

        $tryCount = Yii::$app->session->get('tryCount');

        $code = SignupPeople::generateVeryficationCodeStatic();
        Yii::$app->session->set('smsCode', $code);

        $mobile = ArrayHelper::getValue($verificationSession, 'mobile', Yii::$app->user->identity->mobile);

        Sms::sendVerificationCode(
            $mobile,
            $code
        );

        $code = explode('.', $code);
        $codeFirst = $code[0];
        $codeThird = $code[2];


        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => ArrayHelper::merge
                (
                    self::$params,
                    [
                        'content' => 'sms',
                        'contentParams' => compact('model', 'codeFirst', 'codeThird', 'tryCount')
                    ]
                )
            ]
        );
    }
}



################################################################################
#                                End of file                                   #
################################################################################
