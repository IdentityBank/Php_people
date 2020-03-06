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
use idbyii2\audit\AuditComponent;
use idbyii2\helpers\IdbAccountId;
use idbyii2\models\db\Business2PeopleMessagesModel;
use idbyii2\models\db\PeopleNotification;
use idbyii2\models\db\PeopleUploadFileRequest;
use idbyii2\models\idb\IdbBankClientPeople;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class IdbController
 *
 * @package app\controllers
 */
class IdbController extends Controller
{

    /**
     * @return array
     */
    public function actions()
    {
        return
            [
                'error' =>
                    [
                        'class' => 'yii\web\ErrorAction',
                        'view' => '@app/views/site/error.php'
                    ],
            ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = array_merge_recursive(
            $behaviors,
            [
                'verbs' =>
                    [
                        'class' => VerbFilter::className(),
                        'actions' => [],
                    ],
                'access' =>
                    [
                        'class' => AccessControl::className(),
                        'rules' =>
                            [
                                [
                                    'actions' => ['login', 'error', 'idb-login', 'mfa', 'idb-api'],
                                    'allow' => true,
                                ],
                                [
                                    'allow' => true,
                                    'roles' => ['@'],
                                ],
                            ],
                        'denyCallback' => function ($rule, $action) {
                            Yii::$app->user->setReturnUrl(Yii::$app->request->url);
                            $this->redirect(Yii::$app->user->loginUrl)->send();
                        }
                    ],
            ]
        );

        return $behaviors;
    }

    /**
     * @param $action
     *
     * @return bool|void
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        /**
         * Allow external IDB requests
         */
        AuditComponent::actionAudit($action);
        if (
            $action->id === 'idb-login'
            || $action->id === 'idb-api'
        ) {
            $this->enableCsrfValidation = false;
        }

        $return = parent::beforeAction($action);

        if ($return) {

            if (Yii::$app->user->isGuest) {
                if (
                    (Yii::$app->request->url !== Url::toRoute(Yii::$app->user->loginUrl))
                    && ($action->id !== 'idb-login')
                    && ($action->id !== 'idb-api')
                ) {

                    Yii::$app->user->setReturnUrl(Yii::$app->request->url);

                    return $this->redirect(Yii::$app->user->loginUrl)->send();
                }
            } else {
                $return = Yii::$app->user->identity->validateMfa();
                if ($return) {
                    if ($action->id !== 'details' && $action->controller->id !== 'btpmessages') {
                        $this->getMessages();
                    }
                    $this->getNotifications();
                    $this->getUploadRequests();
                } else {
                    if ($action->id !== 'mfa') {
                        $this->redirect(['/mfa']);
                    }

                    return true;
                }
            }
        }

        return $return;
    }

    /**
     * Method to append Messages to view parameter ['messages'].
     */
    protected function getMessages()
    {
        Yii::$app->view->params['messages'] = [];

        Yii::$app->user->identity->configureIdentityDataForMessages();

        $messages = Business2PeopleMessagesModel::find()->where(
            ['people_user' => Yii::$app->user->identity->peopleDbUserId]
        )->all();

        Yii::$app->view->params['messages'] = $messages;

        $this->countUnreadMessages();
    }

    /**
     * Method to check if is some unread message and apend bool to view parameter ['messagesRing'].
     */
    protected function countUnreadMessages()
    {
        Yii::$app->view->params['messagesRing'] = Business2PeopleMessagesModel::find()->where(
                ['people_user' => Yii::$app->user->identity->peopleDbUserId, 'reviewed' => false]
            )->count() > 0;
    }

    private function getNotifications()
    {
        if (!empty(Yii::$app->user->id)) {
            $notifications = PeopleNotification::getNotificationsForUser(Yii::$app->user->id);

            Yii::$app->view->params['notifications'] = $notifications;
        }
    }

    private function getUploadRequests()
    {
        if (!empty(Yii::$app->user->id)) {
            $accountId = PeopleConfig::get()->getYii2PeopleAccountId();
            $idbClientRelation = IdbBankClientPeople::model($accountId);
            $relatedBusinesses = $idbClientRelation->getRelatedBusinesses(
                IdbAccountId::generatePeopleUserId(
                    $accountId,
                    Yii::$app->user->identity->id
                )
            );

            $search = '';
            if (!empty($relatedBusinesses['QueryData'])) {
                $counter = 0;
                foreach ($relatedBusinesses['QueryData'] as $business) {
                    if ($counter !== 0) {
                        $search .= ' OR ';
                    }
                    $search .= "pid='" . $business[0] . "'";

                    $counter++;
                }
            }

            $requests = PeopleUploadFileRequest::find()->where($search)->andWhere(['<>', 'type', 'complete'])->orderBy(['timestamp' => SORT_DESC])->all();

            Yii::$app->view->params['uploadRequests'] = $requests;
        }
    }

    /**
     * @param $page
     *
     * @return string
     */
    public function getPageTitle($page)
    {
        return Translate::_('people', 'Identity Bank') . " :: $page";
    }
}

################################################################################
#                                End of file                                   #
################################################################################
