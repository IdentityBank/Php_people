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

namespace app\modules\notifications\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\controllers\IdbController;
use idbyii2\models\db\PeopleNotification;
use Yii;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class NotificationsController
 *
 * @package app\modules\notifications\controllers
 */
class NotificationsController extends IdbController
{

    /**
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        $id = null;

        if (!empty(Yii::$app->request->get('id'))) {
            $id = Yii::$app->request->get('id');
        } else {
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }

        $model = PeopleNotification::findOne(['id' => $id]);
        if ($model == null) {
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        } else {
            $model->delete();
        }

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);

    }
}

################################################################################
#                                End of file                                   #
################################################################################
