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

namespace app\modules\events\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\controllers\IdbController;
use app\helpers\DataHelper;
use app\helpers\Translate;
use idbyii2\components\PortalApi;
use idbyii2\helpers\IdbAccountId;
use idbyii2\models\db\PeopleNotification;
use idbyii2\models\idb\IdbBankClientPeople;
use Yii;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class ProfileController
 *
 * @package app\modules\idbuser\controllers
 */
class DefaultController extends IdbController
{

    public function actionReviewCycleAllow($id)
    {
        PeopleNotification::findOne($id)->delete();
        return $this->redirect(Yii::$app->request->referrer ?? Yii::$app->homeUrl);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionReviewCycleRemove($id)
    {
        $notification = PeopleNotification::findOne($id);
        $data = json_decode($notification->data, true);
        $ids = IdbAccountId::parse($data['peopleId']);

        $portalBusinessApi = PortalApi::getBusinessApi();
        $request = $portalBusinessApi->requestDeleteFromBusiness($data['businessId']);
        if(is_bool($request) && $request) {
            $model = IdbBankClientPeople::model($ids['idbid']);
            $model->deleteRelationBusiness2People($data['businessId'], $data['peopleId']);
            $notification->delete();

            Yii::$app->session->setFlash('successMessage', Translate::_('people', 'Removed successfully'));
        } else {
            Yii::$app->session->setFlash('dangerMessage', Translate::_('people', 'There was a problem please try again'));
        }

        return $this->redirect(Yii::$app->homeUrl);
    }


}



################################################################################
#                                End of file                                   #
################################################################################
