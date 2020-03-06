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
use idbyii2\helpers\PasswordToken;
use idbyii2\models\form\PasswordRecoveryForm;
use idbyii2\models\identity\IdbPeopleUser;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

################################################################################
# Class(es)                                                                    #
################################################################################

class TokenController extends IdbController
{
    private static $params = [
        'menu_active_section' => '[menu][tools]',
        'menu_active_item' => '[menu][tools][idbdata]',
    ];

    /**
     * @return string|Response
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new PasswordRecoveryForm();
        $model->captchaEnabled = false;

        if (!empty($request->post('PasswordRecoveryForm'))) {
            $passwordTokenHelper = new PasswordToken();
            $model->load($request->post());
            if ($model->validate()) {
                $passwordToken = $passwordTokenHelper->decodeToken($model->token);
                if (
                    (!empty($passwordToken))
                    && (
                        $model->email === $passwordToken['email']
                        && $model->mobile === $passwordToken['mobile']
                    )
                ) {
                    $identity = IdbPeopleUser::findIdentity($passwordToken['uid']);
                    if (!empty($identity)) {
                        $verificationSession = Yii::$app->session->get('verificationModuleToken', []);
                        $verificationSession['status'] = 'success';
                        Yii::$app->session->set('verificationModuleToken', $verificationSession);

                        return $this->redirect(ArrayHelper::getValue($verificationSession, 'url', '/'));
                    }
                } else {
                    $model->addError('token', Translate::_('people', 'Provided data is incorrect.'));
                    Yii::$app->getModule('idbverification')->init();
                }
            }
        }

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => ArrayHelper::merge
                (
                    self::$params,
                    [
                        'content' => 'index',
                        'contentParams' => compact('model')
                    ]
                )
            ]
        );
    }
}



################################################################################
#                                End of file                                   #
################################################################################
