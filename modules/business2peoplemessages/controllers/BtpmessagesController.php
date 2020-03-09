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

namespace app\modules\business2peoplemessages\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\controllers\IdbController;
use idbyii2\models\db\Business2PeopleMessagesModel;
use Yii;
use yii\helpers\Url;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class BtpmessagesController
 *
 * @package app\modules\business2peoplemessages\controllers
 */
class BtpmessagesController extends IdbController
{

    /**
     * @return string
     */
    public function actionDetails()
    {
        $id = Yii::$app->request->get('id');
        $message = Business2PeopleMessagesModel::findOne(['id' => $id]);

        if (!$message->reviewed) {
            $message->reviewed = true;
            $message->save();
        }

        $this->getMessages();

        $dismissLink = Url::toRoute(Yii::$app->homeUrl, true);

        if (
            !empty(Yii::$app->request->referrer)
            && Yii::$app->request->referrer !== Url::toRoute('details', true)
        ) {
            $dismissLink = Yii::$app->request->referrer;
        }

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'details',
                    'contentParams' => compact('id', 'message', 'dismissLink')
                ]
            ]
        );
    }

    /**
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $people_user = $_GET['people_user'];

        $model = Business2PeopleMessagesModel::find()
                                             ->where(['id' => $id])
                                             ->andWhere(['people_user' => $people_user])
                                             ->one();

        if ($model) {
            $model->delete();
        }

        return $this->redirect(Yii::$app->homeUrl);
    }
}


################################################################################
#                                End of file                                   #
################################################################################
