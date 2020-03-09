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

namespace app\modules\peopleuser\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\controllers\IdbController;
use app\helpers\DataHelper;
use app\helpers\PeopleConfig;
use app\helpers\Translate;
use Exception;
use idb\idbank\PeopleIdBankClient;
use idbyii2\components\Messenger;
use idbyii2\components\PortalApi;
use idbyii2\helpers\IdbAccountId;
use idbyii2\models\db\IdbAuditLog;
use idbyii2\models\form\IdbContactForm;
use idbyii2\models\idb\IdbBankClientPeople;
use Yii;
use yii\base\DynamicModel;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

################################################################################
# Class(es)                                                                    #
################################################################################
// TODO: Refactor this controller

/**
 * Class BusinessController
 *
 * @package app\modules\peopleuser\controllers
 */
class BusinessController extends IdbController
{

    /** @var IdbBankClientPeople */
    private $idbClient = null;
    private $idbClientRelation = null;
    private $peopleDbUserId = null;
    private $relatedBusinesses = null;
    private $portalBusinessApi = null;
    private $accountId = null;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param $action
     *
     * @return bool|Response
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function beforeAction($action)
    {
        $return = parent::beforeAction($action);
        if (!$return) {
            return $return;
        }

        $this->accountId = PeopleConfig::get()->getYii2PeopleAccountId();
        $this->idbClient = IdbBankClientPeople::model($this->accountId);
        $this->idbClientRelation = IdbBankClientPeople::model($this->accountId);
        $this->portalBusinessApi = PortalApi::getBusinessApi();

        if (!empty(Yii::$app->user->identity->id)) {
            $this->peopleDbUserId = IdbAccountId::generatePeopleUserId(
                $this->accountId,
                Yii::$app->user->identity->id
            );
            $this->relatedBusinesses = $this->idbClientRelation->getRelatedBusinesses($this->peopleDbUserId);
        } else {
            return $this->redirect(['login']);
        }

        return $return;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $data = DataHelper::prepareData($this->relatedBusinesses['QueryData'], $this->portalBusinessApi);
        $dataProvider = new ArrayDataProvider(
            [
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['name', 'surname'],
                ],
            ]
        );

        $this->view->title = Translate::_('people', 'Connected businesses');
        $this->view->params['breadcrumbs'][] = [
            'name' => Translate::_('people', 'Connected businesses'),
            'action' => Url::toRoute(['business/index']),
        ];

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'index',
                    'contentParams' => [
                        'dataProvider' => $dataProvider,
                    ]
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function actionWhoUses()
    {
        $data = DataHelper::prepareData($this->relatedBusinesses['QueryData'], $this->portalBusinessApi);
        $this->view->title = Translate::_('people', 'Who uses my data?');
        $this->view->params['breadcrumbs'][] = [
            'name' => Translate::_('people', 'Who uses my data?'),
            'action' => Url::toRoute(['business/who-uses']),
        ];

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'uses.php',
                    'contentParams' => [
                        'data' => $data,
                    ]
                ]
            ]
        );
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionMapData()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            if (!empty($request->post('params'))) {
                $requestData = json_decode($request->post('params'), true);
                if (
                !empty(
                    $requestData['mapTo']
                    && !empty($requestData['ids'])
                    && count($requestData['ids']) > 0
                )
                ) {
                    Yii::$app->session->set('mapTo', $requestData['mapTo']);
                    Yii::$app->session->set('ids', $requestData['ids']);
                }
            }
        } elseif (!Yii::$app->session->has('mapTo') || !Yii::$app->session->has('ids')) {
            throw new NotFoundHttpException(404);
        }
        try {
            $mapToBusinessId = IdbAccountId::generateBusinessDbId(
                Yii::$app->session->get('mapTo')['oid'],
                Yii::$app->session->get('mapTo')['aid'],
                Yii::$app->session->get('mapTo')['dbid']
            );

            $mapToBusinessId .= '.uid.' . Yii::$app->session->get('mapTo')['uid'];

            $mapToData = $this->portalBusinessApi->requestBusinessDataForUser($mapToBusinessId)['QueryData'][0];

            $mapToMetadata = json_decode(
                $this->portalBusinessApi->requestBusinessMetadataForUser($mapToBusinessId)['Metadata'],
                true
            )['data'];
            $mapToName = Yii::$app->session->get('mapTo')['name'];

            $toMap = [];

            if (Yii::$app->session->has('ids')) {
                foreach (Yii::$app->session->get('ids') as $ids) {
                    $tmpId = IdbAccountId::generateBusinessDbId($ids['oid'], $ids['aid'], $ids['dbid']) . '.uid.'
                        . $ids['uid'];
                    $toMap[$tmpId] = [
                        'name' => $ids['name'],
                        'data' => $this->portalBusinessApi->requestBusinessDataForUser($tmpId)['QueryData'][0],
                        'metadata' => json_decode(
                            $this->portalBusinessApi->requestBusinessMetadataForUser($tmpId)['Metadata'],
                            true
                        )['data']
                    ];
                }
            }

            $this->view->title = Translate::_('people', 'Map businesses');
            $this->view->params['breadcrumbs'][] = [
                'name' => Translate::_('people', 'Map businesses'),
                'action' => Url::toRoute(['business/index']),
            ];

            return $this->render(
                '@app/themes/metronic/views/site/template',
                [
                    'params' => [
                        'content' => 'map',
                        'contentParams' => compact(
                            'mapToData',
                            'mapToMetadata',
                            'mapToBusinessId',
                            'mapToName',
                            'toMap'
                        )
                    ]
                ]
            );
        } catch (Exception $e) {
            Yii::$app->session->setFlash(
                'dangerMessage',
                Translate::_(
                    'people',
                    'An error has occured. Please contact your system administrator.'
                )
            );

            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
    }

    /**
     * Action to mapping business data through portalApi
     *
     * @return bool
     * @throws Exception
     */
    public function actionMapSave()
    {
        $request = Yii::$app->request;

        if ($request->isAjax) {
            if (!empty($request->post('map'))) {
                $this->idbClient = IdbBankClientPeople::model($this->accountId);

                if (empty($this->idbClient->getAccountMetadata())) {
                    $this->idbClient->createAccountMetadata();
                }

                $this->idbClient->setAccountMetadata($request->post());
                Yii::$app->session->setFlash(
                    'successMessage',
                    Translate::_(
                        'people',
                        'Successfully saved.'
                    )
                );

                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function actionEditMappedData()
    {
        $metadata = $this->idbClient->getAccountMetadata();
        if (empty($metadata) || empty($metadata['map'])) {
            Yii::$app->session->setFlash('warningMessage', Translate::_('people', 'You need to map data first'));

            return $this->redirect(['business/index']);
        }

        $request = Yii::$app->request;
        $edit = [];

        if (!empty($request->post())) {
            foreach ($request->post() as $postKey => $post) {
                if (
                    $postKey !== '_csrf'
                    && $post !== ''
                    && !empty($metadata['map'][$postKey])
                ) {
                    foreach ($metadata['map'][$postKey] as $dataKey => $data) {
                        $edit[$metadata['map'][$postKey][$dataKey]['business']][$dataKey] = $post;
                    }
                }
            }

            if ($this->portalBusinessApi->requestUpdateMappedBusiness($edit)) {
                Yii::$app->session->setFlash(
                    'successMessage',
                    Translate::_(
                        'people',
                        'The information you have amended has been sent to the business. The business might contact you to discuss the changes.'
                    )
                );
            } else {
                Yii::$app->session->setFlash(
                    'dangerMessage',
                    Translate::_(
                        'people',
                        'An error has occured. Please contact your system administrator.'
                    )
                );
            }

            return $this->redirect(['business/index']);
        }


        $this->view->title = Translate::_('people', 'Edit mapped data');
        $this->view->params['breadcrumbs'][] = [
            'name' => Translate::_('people', 'Map businesses'),
            'action' => Url::toRoute(['business/index']),
        ];

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'editMapped',
                    'contentParams' => compact('metadata')
                ]
            ]
        );
    }


    /**
     * @return string
     * @throws Exception
     */
    public function actionEdit()
    {
        $array = DataHelper::checkPost(Yii::$app->getRequest(), $this->relatedBusinesses);
        $businessId = IdbAccountId::generateBusinessDbUserId(
            $array['oid'],
            $array['aid'],
            $array['dbid'],
            $array['uid']
        );
        $data = $this->portalBusinessApi->requestBusinessDataForUser($businessId)['QueryData'];
        if (empty($data)) {
            return $this->redirect('/');
        }

        $businessData = json_decode($this->portalBusinessApi->requestBusinessNameForUser($businessId), true);
        $businessName = DataHelper::businessDisplayName($businessData['name'], $businessData['database']);
        $dataArray = DataHelper::getMetadataFromBusiness($data, $businessId, $this->portalBusinessApi);

        $dataProvider = new ArrayDataProvider(
            [
                'allModels' => $dataArray,
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['name', 'surname'],
                ],
            ]
        );

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'update',
                    'contentParams' => compact('dataProvider', 'businessName', 'businessId')
                ]
            ]
        );
    }


    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionDeleteData()
    {
        if (Yii::$app->getRequest()->isPost) {
            $post = Yii::$app->getRequest()->post();
            DataHelper::addDataToSessionFromPost($post, $this->portalBusinessApi);

            return $this->redirect(['business/edit']);
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionGdpr()
    {
        $model = new IdbContactForm();
        if (!empty(Yii::$app->request->post('IdbContactForm'))) {
            $businessId = Yii::$app->request->post('IdbContactForm')['businessId'];
            $model->load(Yii::$app->request->post());

            if ($model->validate()) {
                $messenger = Messenger::get();

                $messenger->email($model->email, $model->title, $model->message);
                Yii::$app->session->setFlash(
                    'successMessage',
                    Translate::_(
                        'people',
                        'Message was send successfully'
                    )
                );
            } else {
                Yii::$app->session->setFlash(
                    'dangerMessage',
                    Translate::_(
                        'people',
                        'Ups there something goes wrong, please try again.'
                    )
                );
            }
        } else {
            $array = DataHelper::checkPost(Yii::$app->getRequest(), $this->relatedBusinesses);
            $businessId = IdbAccountId::generateBusinessDbUserId(
                $array['oid'],
                $array['aid'],
                $array['dbid'],
                $array['uid']
            );
        }

        $businessData = json_decode($this->portalBusinessApi->requestBusinessNameForUser($businessId), true);
        $businessName = DataHelper::businessDisplayName($businessData['name'], $businessData['database']);
        $dpoData = $this->portalBusinessApi->requestBusinessPrivacyDetails($businessId);
        $gdpr = $this->portalBusinessApi->requestBusinessGPDR($businessId);

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'dpoContact',
                    'contentParams' => compact(
                        'businessName',
                        'businessData',
                        'gdpr',
                        'dpoData',
                        'model',
                        'businessId'
                    )
                ]
            ]
        );
    }


    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionEditData()
    {
        if (!Yii::$app->getRequest()->isPost) {
            return $this->redirect(['business/edit']);
        }

        $post = Yii::$app->getRequest()->post();

        $model = new DynamicModel(
            [
                'display_name',
                'value',
                'new_value',
                'column',
                'delete' => false,
                'required' => false,
            ]
        );

        if (array_key_exists('DynamicModel', $post)) {
            DataHelper::addDataToSessionFromDynamic($post, $model);

            return $this->redirect(['business/edit']);
        }

        if (!array_key_exists('new_value', $post)) {
            unset($post['_csrf']);
            foreach ($post as $key => $value) {
                $model[$key] = $value;
            }

            $businessData = json_decode(
                $this->portalBusinessApi->requestBusinessNameForUser(
                    IdbAccountId::generateBusinessDbId(
                        Yii::$app->session->get('oid'),
                        Yii::$app->session->get('aid'),
                        Yii::$app->session->get('dbid')
                    )
                ),
                true
            );
            $businessName = DataHelper::businessDisplayName($businessData['name'], $businessData['database']);

            return $this->render(
                '@app/themes/metronic/views/site/template',
                [
                    'params' => [
                        'content' => 'edit-data',
                        'contentParams' => compact('model', 'businessName')
                    ]
                ]
            );
        }
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function actionDelete()
    {
        if (Yii::$app->getRequest()->isPost) {
            $post = Yii::$app->getRequest()->post();
            if (array_key_exists('oid', $post) && array_key_exists('aid', $post) && array_key_exists('dbid', $post)) {
                $businessId = IdbAccountId::generateBusinessDbUserId(
                    $post['oid'],
                    $post['aid'],
                    $post['dbid'],
                    $post['uid']
                );
                $response = $this->portalBusinessApi->requestDeleteAllBusinessDataForUser($businessId);
                $response = $this->idbClient->deleteRelationBusiness2People($businessId, $this->peopleDbUserId);
                Yii::$app->session->set('emptyData', true);
                Yii::$app->cache->flush();

                return $this->redirect(['business/index']);
            }
        }

        return $this->redirect(['business/edit']);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function actionDeleteAllData()
    {
        if (!Yii::$app->getRequest()->isPost) {
            Yii::$app->session->setFlash(
                'infoMessage',
                Translate::_(
                    'people',
                    'There are no new changes to send.'
                )
            );

            return $this->redirect(['business/edit']);
        }
        $post = Yii::$app->getRequest()->post();
        if (
            !array_key_exists('oid', $post)
            || !array_key_exists('aid', $post)
            || !array_key_exists('dbid', $post)
            || !array_key_exists('uid', $post)
        ) {
            Yii::$app->session->setFlash(
                'infoMessage',
                Translate::_(
                    'people',
                    'There are no new changes to send.'
                )
            );

            return $this->redirect(['business/edit']);
        }
        Yii::$app->session->set('oid', $post['oid']);
        Yii::$app->session->set('aid', $post['aid']);
        Yii::$app->session->set('dbid', $post['dbid']);
        Yii::$app->session->set('uid', $post['uid']);
        $businessId = IdbAccountId::generateBusinessDbUserId($post['oid'], $post['aid'], $post['dbid'], $post['uid']);
        $data = $this->portalBusinessApi->requestBusinessDataForUser($businessId)['QueryData'];
        $dataArray = DataHelper::getMetadataFromBusiness($data, $businessId, $this->portalBusinessApi);

        foreach ($dataArray as $data) {
            if (Yii::$app->session->has($businessId . 'deleteAll')) {
                Yii::$app->cache->delete($businessId . '.display_name.' . $data['display_name']);
                continue;
            }
            $model = new DynamicModel(
                [
                    'display_name' => $data['display_name'],
                    'value' => $data['value'],
                    'column' => $data['column'],
                    'delete' => true,
                    'required' => $data['required']
                ]
            );
            $newId = $businessId . '.display_name.' . $model['display_name'];
            Yii::$app->cache->add($newId, $model, 6000);
        }
        if (!Yii::$app->session->has($businessId . 'deleteAll')) {
            Yii::$app->session->set($businessId . 'deleteAll', true);
        } else {
            Yii::$app->session->remove($businessId . 'deleteAll');
        }

        return $this->redirect(['business/edit']);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionSendToBusiness()
    {
        $businessId = IdbAccountId::generateBusinessDbUserId(
            Yii::$app->session->get('oid'),
            Yii::$app->session->get('aid'),
            Yii::$app->session->get('dbid'),
            Yii::$app->session->get('uid')
        );
        $metadata = $this->portalBusinessApi->requestBusinessMetadataForUser($businessId);
        if (!array_key_exists('Metadata', $metadata)) {
            return $this->redirect(['business/edit']);
        }
        $metadata = json_decode($metadata['Metadata'], true);
        $array = DataHelper::prepareDataForSend($metadata, $businessId);
        $dataArray = $array['data'];
        $isDeleteAction = $array['isDelete'];

        if (Yii::$app->session->has($businessId . 'deleteAll')) {
            $dataArray['deleteAll'] = true;

            DataHelper::removeAllDataFromBusiness(
                $dataArray,
                $this->portalBusinessApi,
                $this->accountId,
                $this->peopleDbUserId,
                $businessId
            );

            Yii::$app->session->setFlash(
                'infoMessage',
                Translate::_(
                    'people',
                    'Connection with business going to be deleted in 30 days.'
                )
            );

            return $this->redirect(['/']);
        }

        if (Yii::$app->session->has($businessId . 'deleteRequired')) {
            $dataArray['deleteAll'] = true;

            DataHelper::planDeleteRelation(
                $dataArray,
                $this->portalBusinessApi,
                $this->accountId,
                $this->peopleDbUserId,
                $businessId
            );

            DataHelper::sendNotification(
                'Deleting required data',
                'Your required data have been deleted from the business you have selected. The business might contact you to discuss the changes.'
            );
            Yii::$app->session->remove($businessId . 'deleteRequired');
            Yii::$app->session->setFlash(
                'infoMessage',
                Translate::_(
                    'people',
                    'Connection with business going to be deleted in 30 days.'
                )
            );

            return $this->redirect(['/']);
        }


        if (empty($dataArray['data'])) {
            Yii::$app->session->setFlash(
                'infoMessage',
                Translate::_(
                    'people',
                    'There are no new changes to send.'
                )
            );

            return $this->redirect(['business/edit']);
        }

        DataHelper::sendUpdateDataToTheBusiness($dataArray, $isDeleteAction, $this->portalBusinessApi);

        return $this->redirect(['business/edit']);
    }

    /**
     * @return Response
     */
    public function actionMapping()
    {
        $post = Yii::$app->request->post();
        $map = [];
        $data = [];

        foreach ($post as $key => $value) {
            if ($key == 'bid' || $key == '_csrf') {
                continue;
            }

            $map[$key] = $value;
        }

        $this->idbClient = IdbBankClientPeople::model($this->accountId);
        $response = $this->idbClient->get($this->peopleDbUserId);

        if (!is_null($response)) {
            foreach ($response[PeopleIdBankClient::DATA_TYPES] as $value) {
                $data[] = [
                    'attribute' => $value['attribute'],
                    'display' => $value['display'],
                    'value' => $value['value']
                ];
            }
        }

        $businessId = $post['bid'];

        $dataArray = [];
        $dataArray['businessId'] = $businessId;
        $dataArray['mapping'] = true;

        foreach ($map as $key => $value) {
            foreach ($data as $item) {
                if ($value == $item['display']) {
                    $dataArray[$key] = [$value, $item['value']];
                }
            }
        }

        try {
            $this->idbClient->addData($this->peopleDbUserId, $dataArray);
        } catch (Exception $e) {
            echo Translate::_('people', 'Error while saving data.');
        }

        $dataArray = [];
        $dataArray['businessId'] = $businessId;

        foreach ($map as $key => $value) {
            foreach ($data as $item) {
                if ($value == $item['display']) {
                    $dataArray[$key] = [$value, $item['value']];
                }
            }
        }

        if (!Yii::$app->session->get('emptyData')) {
            $response = $this->portalBusinessApi->requestUpdateBusinessDataForUser($dataArray);
        } else {
            $response = $this->portalBusinessApi->requestAddBusinessDataForUser($dataArray);
        }

        return $this->redirect(['/business/index']);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionAuditLog()
    {
        $items = [];
        if (
            !empty($this->relatedBusinesses['QueryData'])
            && is_array($this->relatedBusinesses['QueryData'])
        ) {
            foreach ($this->relatedBusinesses['QueryData'] as $business) {
                $auditLogs = IdbAuditLog::findAll(['business_db_id' => $business[0]]);
                $businessData = json_decode(
                    $this->portalBusinessApi->requestBusinessNameForUser(
                        $business[0]
                    ),
                    true
                );
                $businessName = DataHelper::businessDisplayName($businessData['name'], $businessData['database']);
                if (count($auditLogs)) {
                    foreach ($auditLogs as $key => $auditLog) {
                        $items[$key] = [
                            'timestamp' => date('Y-m-d H:i:s', strtotime($auditLog['timestamp'])),
                            'message' => $auditLog['message'],
                            'who' => $businessName
                        ];
                    }
                }
            }
        }

        $searchAttributes = ['who', 'timestamp', 'message'];
        $searchModel = [];
        $searchColumns = [];

        foreach ($searchAttributes as $searchAttribute) {
            $filterName = 'filter' . $searchAttribute;
            $filterValue = Yii::$app->request->getQueryParam($filterName, '');
            $searchModel[$searchAttribute] = $filterValue;
            $searchColumns[] = [
                'attribute' => $searchAttribute,
                'filter' => '<input class="form-control" name="' . $filterName . '" value="' . $filterValue
                    . '" type="text">',
                'value' => $searchAttribute
            ];
            $items = array_filter(
                $items,
                function ($item) use (&$filterValue, &$searchAttribute) {
                    return strlen($filterValue) > 0 ? stripos(
                        '/^' . strtolower($item[$searchAttribute]) . '/',
                        strtolower($filterValue)
                    ) : true;
                }
            );
        }

        $dataProvider = new ArrayDataProvider(
            [
                'allModels' => $items,
                'sort' => [
                    'attributes' => $searchAttributes,
                ],
            ]
        );

        $this->view->title = Translate::_('people', 'What\'s my data being used for?');
        $this->view->params['breadcrumbs'][] = [
            'name' => Translate::_('people', 'What\'s my data being used for?'),
            'action' => Url::toRoute(['business/index']),
        ];

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'audit-log',
                    'contentParams' => [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'searchColumns' => $searchColumns
                    ]
                ]
            ]
        );
    }
}

################################################################################
#                                End of file                                   #
################################################################################
