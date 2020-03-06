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
use idbyii2\components\PortalApi;
use idbyii2\helpers\IdbAccountId;
use idbyii2\models\idb\IdbBankClientPeople;
use Yii;
use yii\base\DynamicModel;
use yii\data\ArrayDataProvider;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class DataController
 *
 * @package app\modules\peopleuser\controllers
 */
class DataController extends IdbController
{

    /** @var IdbBankClientPeople */
    private $idbClient = null;
    private $idbClientRelation = null;
    private $peopleDbUserId = null;
    private $relatedBusinesses = null;
    private $portalBusinessApi = null;

    /**
     * @param $action
     *
     * @return bool|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \Exception
     */
    public function beforeAction($action)
    {
        $return = parent::beforeAction($action);
        if (!$return) {
            return $return;
        }

        $accuountid = PeopleConfig::get()->getYii2PeopleAccountId();
        $this->idbClient = IdbBankClientPeople::model($accuountid);
        $this->idbClientRelation = IdbBankClientPeople::model($accuountid);
        $this->portalBusinessApi = PortalApi::getBusinessApi();

        if (!empty(Yii::$app->user->identity->id)) {
            $this->peopleDbUserId = IdbAccountId::generatePeopleUserId(
                $accuountid,
                Yii::$app->user->identity->id
            );
            $relatedBusinesses = $this->idbClientRelation->getRelatedBusinesses($this->peopleDbUserId);

            if (
                !empty($relatedBusinesses['QueryData'])
                && !empty($relatedBusinesses['QueryData'][0])
            ) {
                $this->relatedBusinesses = [];
                foreach($relatedBusinesses['QueryData'] as $business) {
                    $this->relatedBusinesses []= $business[0];
                }
            }
        } else {
            return $this->redirect(['login']);
        }

        return $return;
    }


    /**
     * @return string
     */
    public function actionShowAll()
    {
        $dataArray = [];
        $index = 1;
        if (empty($this->relatedBusinesses)) {
            return $this->redirect('/');
        }

        foreach ($this->relatedBusinesses as $relatedBusiness) {
            $data = $this->portalBusinessApi->requestBusinessDataForUser($relatedBusiness);
            $businessData = json_decode(
                $this->portalBusinessApi->requestBusinessNameForUser(
                    $relatedBusiness
                ),
                true
            );
            $businessName = DataHelper::businessDisplayName($businessData['name'], $businessData['database']);

            $dataIndex = 1;
            if (
                !empty($data)
                && !empty($data['QueryData'])
            ) {
                $data = $data['QueryData'][0];
                Yii::$app->session->set('emptyData', false);
            } else {
                Yii::$app->session->set('emptyData', true);
            }

            $metadata = $this->portalBusinessApi->requestBusinessMetadataForUser($relatedBusiness);
            $metadata = json_decode($metadata['Metadata'], true);
            foreach ($metadata['data'] as $key => $value) {
                if (!array_key_exists('object_type', $value)) {
                    continue;
                }

                if ($value['object_type'] === 'type') {


                    $dataArray[$index] = [
                        'display_name' => $value['display_name'] ?? '',
                        'required' => $value['required'] ?? '',
                        'value' => array_key_exists($dataIndex, $data) ? $data[$dataIndex] : '',
                        'column' => $value['uuid'] ?? '',
                        'relatedBusiness' => $relatedBusiness,
                        'businessName' => $businessName
                    ];
                    $index++;
                    $dataIndex++;
                }

                if (
                    $value['object_type'] === 'set'
                    && !empty($value['data'])
                ) {
                    foreach ($value['data'] as $nr => $type) {
                        $model = Yii::$app->cache->get(
                            $relatedBusiness . '.display_name.' . $value['display_name'] . '-' . $type['display_name']
                        );
                        $dataArray[$index] = [
                            'display_name' => $value['display_name'] . '-' . $type['display_name'] ?? '',
                            'required' => $type['required'] ?? '',
                            'value' => array_key_exists($dataIndex, $data) ? $data[$dataIndex] : '',
                            'column' => $type['uuid'] ?? '',
                            'relatedBusiness' => $relatedBusiness,
                            'businessName' => $businessName
                        ];
                        $index++;
                        $dataIndex++;
                    }
                }
            }
        }

        $dataProvider = new ArrayDataProvider(
            [
                'allModels' => $dataArray,
                'pagination' => [
                    'pageSize' => 25,
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
                    'content' => 'show-all',
                    'contentParams' => compact('dataProvider')
                ]
            ]
        );
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionPermissions()
    {
        $allData = false;
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        if (
            $request->isPost
            && !empty($request->post('relatedBusiness'))
            && !empty($request->post('uuid'))
            && (!empty($request->post('value'))
                || $request->post('value') === '')
            && !empty($request->post('required'))
            && !empty($request->post('display_name'))
            && !empty($request->post('businessName'))
        ) {
            Yii::$app->session->set('relatedBusiness', $request->post('relatedBusiness'));
            Yii::$app->session->set('uuid', $request->post('uuid'));
            Yii::$app->session->set('value', $request->post('value'));
            Yii::$app->session->set('required', $request->post('required'));
            Yii::$app->session->set('display_name', $request->post('display_name'));
            Yii::$app->session->set('businessName', $request->post('businessName'));

            $allData = true;
        } elseif (
            $session->has('relatedBusiness')
            && $session->has('uuid')
            && $session->has('value')
            && $session->has('required')
            && $session->has('display_name')
            && $session->has('businessName')
        ) {
            $allData = true;
        }

        if ($allData) {
            $relatedBusiness = $session->get('relatedBusiness');
            $uuid = $session->get('uuid');
            $value = $session->get('value');
            $required = $session->get('required');
            $displayName = $session->get('display_name');
            $businessName = $session->get('businessName');

            $parsedId = IdbAccountId::parse($relatedBusiness);

            $session->set('uid', $parsedId['uid']);
            $session->set('oid', $parsedId['oid']);
            $session->set('aid', $parsedId['aid']);
            $session->set('dbid', $parsedId['dbid']);

            $model = Yii::$app->cache->get($relatedBusiness . '.display_name.' . $displayName);

            $dataArray[1] = [
                'display_name' => $displayName,
                'required' => $required,
                'value' => $value,
                'new_value' => $model['new_value'] ?? '',
                'column' => $uuid,
                'relatedBusiness' => $relatedBusiness,
                'delete' => $model['delete'] ?? false,
                'businessName' => $businessName
            ];

            $dataProvider = new ArrayDataProvider(
                [
                    'allModels' => $dataArray,
                    'pagination' => [
                        'pageSize' => 25,
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
                        'content' => 'edit-table',
                        'contentParams' => compact('dataProvider')
                    ]
                ]
            );
        }

        return $this->redirect('show-all');
    }

    /**
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionEditData()
    {
        if (Yii::$app->getRequest()->isPost) {
            $post = Yii::$app->getRequest()->post();

            $model = new DynamicModel(
                [
                    'display_name',
                    'value',
                    'new_value',
                    'column',
                    'delete' => false,
                    'required' => false
                ]
            );

            if (array_key_exists('DynamicModel', $post)) {
                DataHelper::addDataToSessionFromDynamic($post, $model);

                return $this->redirect(['data/permissions']);
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
    }

    /**
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionDeleteData()
    {
        if (Yii::$app->getRequest()->isPost) {
            $post = Yii::$app->getRequest()->post();
            DataHelper::addDataToSessionFromPost($post, $this->portalBusinessApi);

            return $this->redirect(['data/permissions']);
        }
    }

    /**
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionSendToBusinesses()
    {
        $isDeleteAction = false;

        $oid = Yii::$app->session->get('oid');
        $aid = Yii::$app->session->get('aid');
        $dbid = Yii::$app->session->get('dbid');
        $uid = Yii::$app->session->get('uid');

        $businessId = IdbAccountId::generateBusinessDbId($oid, $aid, $dbid);
        $businessId .= '.uid.' . $uid;
        $metadata = $this->portalBusinessApi->requestBusinessMetadataForUser($businessId);
        $metadata = json_decode($metadata['Metadata'], true);
        $dataArray = [];
        $dataArray['businessId'] = $businessId;
        $modelToChange = null;
        //TODO: refactor this to be multilevel and move this to some helper
        foreach ($metadata['data'] as $key => $value) {
            if (!array_key_exists('object_type', $value)) {
                continue;
            }

            if ($value['object_type'] === 'type') {
                $modelToChange = $model = Yii::$app->cache->get(
                    $businessId . '.display_name.' . $value['display_name']
                );

                if ($model) {
                    $isDeleteAction = $model['delete'];
                    $dataArray['data'][$value['uuid']] = [
                        'value' => $model['delete'] ? '' : $model['new_value'],
                        'old_value' => $model['value'],
                        'display_name' => $value['display_name']
                    ];
                }
            }

            if (
                $value['object_type'] === 'set'
                && !empty($value['data'])
            ) {
                foreach ($value['data'] as $nr => $type) {
                    $modelToChange = $model = Yii::$app->cache->get(
                        $businessId . '.display_name.' . $value['display_name'] . '-' . $type['display_name']
                    );

                    if ($model) {
                        $dataArray['data'][$type['uuid']] = [
                            'value' => $model['delete'] ? '' : $model['new_value'],
                            'old_value' => $model['value'],
                            'display_name' => $type['display_name']
                        ];
                    }
                }
            }
        }

        if (!empty($dataArray['data'])) {
            DataHelper::sendUpdateDataToTheBusiness($dataArray, $isDeleteAction, $this->portalBusinessApi);
        } else {
            Yii::$app->session->setFlash(
                'infoMessage',
                Translate::_(
                    'people',
                    'There are no new changes to send.'
                )
            );
        }

        Yii::$app->session->set('value', $modelToChange['delete'] ? '' : $modelToChange['new_value']);

        return $this->redirect(['data/permissions']);
    }
}

################################################################################
#                                End of file                                   #
################################################################################
