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

namespace app\helpers;

################################################################################
# Use(s)                                                                       #
################################################################################

use idbyii2\helpers\IdbAccountId;
use idbyii2\models\db\PeopleDisconnectRequest;
use idbyii2\models\db\PeopleNotification;
use Yii;
use yii\base\DynamicModel;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class DataHelper
 *
 * @package app\helpers
 */
class DataHelper
{

    const FLAG_SHOW_DATABASE_NAME = false;

    /**
     * @param $data
     * @param $businessId
     * @param $portalBusinessApi
     *
     * @return array
     */
    public static function getMetadataFromBusiness($data, $businessId, $portalBusinessApi)
    {
        if (!empty($data)) {
            $data = $data[0];
            Yii::$app->session->set('emptyData', false);
        } else {
            Yii::$app->session->set('emptyData', true);
        }

        $metadata = $portalBusinessApi->requestBusinessMetadataForUser($businessId);
        $metadata = json_decode($metadata['Metadata'], true);
        $dataArray = [];

        $index = 1;

        foreach ($metadata['data'] as $key => $value) {
            if (!array_key_exists('object_type', $value)) {
                continue;
            }

            if ($value['object_type'] === 'type') {
                $model = Yii::$app->cache->get($businessId . '.display_name.' . $value['display_name']);

                $dataArray[$index] = [
                    'display_name' => $value['display_name'] ?? '',
                    'used_for' => $value['used_for'] ?? '',
                    'required' => $value['required'] ?? '',
                    'value' => array_key_exists($index, $data) ? $data[$index] : '',
                    'new_value' => $model['new_value'] ?? '',
                    'column' => $value['uuid'] ?? '',
                    'delete' => $model['delete'] ?? false,
                ];
                $index++;
            }

            if (
                $value['object_type'] === 'set'
                && !empty($value['data'])
            ) {
                foreach ($value['data'] as $nr => $type) {
                    $model = Yii::$app->cache->get(
                        $businessId . '.display_name.' . $value['display_name'] . '-' . $type['display_name']
                    );
                    $dataArray[$index] = [
                        'display_name' => $value['display_name'] . '-' . $type['display_name'] ?? '',
                        'used_for' => $type['used_for'] ?? '',
                        'required' => $type['required'] ?? '',
                        'value' => array_key_exists($index, $data) ? $data[$index] : '',
                        'new_value' => $model['new_value'] ?? '',
                        'column' => $type['uuid'] ?? '',
                        'delete' => $model['delete'] ?? false,
                    ];
                    $index++;
                }
            }
        }

        return $dataArray;
    }

    /**
     * @param $queryData
     * @param $portalBusinessApi
     *
     * @return array
     */
    public static function prepareData($queryData, $portalBusinessApi)
    {
        $data = [];
        $i = 0;
        if (
            !empty($queryData)
            && is_array($queryData)
        ) {
            foreach ($queryData as $business) {
                $data[$i] = IdbAccountId::parse($business[0]);
                $businessData = json_decode(
                    $portalBusinessApi->requestBusinessNameForUser(
                        $business[0]
                    ),
                    true
                );

                $data[$i]['name'] = self::businessDisplayName(
                    $businessData['name'],
                    $businessData['database'],
                    '{businessName} <hr> {databaseName}'
                );

                $i++;
            }
        }

        return $data;
    }

    /**
     * @param $post
     */
    public static function prepareSessionFromPost($post)
    {
        Yii::$app->session->set('oid', $post['oid']);
        Yii::$app->session->set('aid', $post['aid']);
        Yii::$app->session->set('dbid', $post['dbid']);
        Yii::$app->session->set('uid', $post['uid']);
    }

    /**
     * @param $dataArray
     * @param $isDeleteAction
     *
     * @return mixed
     */
    public static function prepareNotification($dataArray, $isDeleteAction)
    {
        if ($isDeleteAction) {
            $notificationTitle = Translate::_(
                'people',
                'Your information has been deleted from the business you have selected. The business might contact you to discuss the changes.'
            );
        } else {
            $notificationTitle = Translate::_('people', 'Your amended data has been sent to the business.');
        }
        $notificationData = '';
        foreach ($dataArray['data'] as $data) {
            $notificationData .= ' ' . Translate::_('people', 'New value for') . ': ' . $data['display_name'];
            $notificationData .= ' ' . Translate::_('people', 'is') . ': ' . $data['value'];
        }

        $model = PeopleNotification::instantiate(
            [
                'type' => 'green',
                'status' => 1,
                'expires_at' => date('Y/m/d h:i', strtotime('+7 days')),
                'uid' => Yii::$app->user->id,
                'data' => json_encode(
                    [
                        'title' => $notificationTitle,
                        'body' => $notificationData
                    ]
                )
            ]
        );

        $model->save();

        $dataArray['userId'] = Yii::$app->user->identity->userId;
        $dataArray['peopleId'] = Yii::$app->user->identity->peopleDbUserId;

        return $dataArray;
    }

    /**
     * @param $post
     * @param $model
     *
     * @throws \Exception
     */
    public static function addDataToSessionFromDynamic($post, $model)
    {
        foreach ($post['DynamicModel'] as $key => $value) {
            $model[$key] = $value;
        }

        $businessId = IdbAccountId::generateBusinessDbUserId(
            Yii::$app->session->get('oid'),
            Yii::$app->session->get('aid'),
            Yii::$app->session->get('dbid'),
            Yii::$app->session->get('uid')
        );

        $businessId .= '.display_name.' . $model['display_name'];

        if (Yii::$app->cache->get($businessId)) {
            Yii::$app->cache->delete($businessId);
        }

        Yii::$app->cache->add($businessId, $model, 6000);
    }

    /**
     * @param $post
     * @param $businessApi
     *
     * @throws \Exception
     */
    public static function addDataToSessionFromPost($post, $businessApi)
    {
        $model = new DynamicModel(
            [
                'display_name' => $post['display_name'],
                'value' => $post['value'],
                'column' => $post['column'],
                'delete' => true,
                'required' => $post['required']
            ]
        );

        $businessId = IdbAccountId::generateBusinessDbUserId(
            Yii::$app->session->get('oid'),
            Yii::$app->session->get('aid'),
            Yii::$app->session->get('dbid'),
            Yii::$app->session->get('uid')
        );

        $typeId = $businessId . '.display_name.' . $model['display_name'];

        if (empty(Yii::$app->cache->get($typeId))) {
            Yii::$app->cache->add($typeId, $model, 6000);
        } else {
            Yii::$app->cache->delete($typeId);
        }

        if (Yii::$app->session->has($businessId . 'deleteAll')) {
            Yii::$app->session->remove($businessId . 'deleteAll');
        } else {
            $data = $businessApi->requestBusinessDataForUser($businessId)['QueryData'];
            $dataArray = DataHelper::getMetadataFromBusiness($data, $businessId, $businessApi);

            $all = true;
            foreach ($dataArray as $data) {
                if (empty(Yii::$app->cache->get($businessId . '.display_name.' . $data['display_name']))) {
                    $all = false;
                }
            }

            if ($all) {
                Yii::$app->session->set($businessId . 'deleteAll', true);
            }
        }
    }

    /**
     * @param $request
     * @param $relatedBusinesses
     *
     * @return array
     */
    public static function checkPost($request, $relatedBusinesses)
    {
        if ($request->isPost) {
            $post = Yii::$app->getRequest()->post();
            if (
                array_key_exists('oid', $post) && array_key_exists('aid', $post) && array_key_exists('dbid', $post)
                && array_key_exists('uid', $post)
            ) {
                DataHelper::prepareSessionFromPost($post);
                $oid = $post['oid'];
                $aid = $post['aid'];
                $dbid = $post['dbid'];
                $uid = $post['uid'];
            }
        } else {
            if (
                !Yii::$app->session->has('oid')
                || !Yii::$app->session->has('aid')
                || !Yii::$app->session->has('dbid')
                || !Yii::$app->session->has('uid')
            ) {
                $parsedIds = IdbAccountId::parse($relatedBusinesses['QueryData'][0][0]);

                $oid = $parsedIds['oid'];
                $aid = $parsedIds['aid'];
                $dbid = $parsedIds['dbid'];
                $uid = $parsedIds['uid'];

            } else {
                $oid = Yii::$app->session->get('oid');
                $aid = Yii::$app->session->get('aid');
                $dbid = Yii::$app->session->get('dbid');
                $uid = Yii::$app->session->get('uid');
            }

        }

        return ['oid' => $oid, 'aid' => $aid, 'dbid' => $dbid, 'uid' => $uid];
    }

    /**
     * @param $metadata
     * @param $businessId
     *
     * @return array
     */
    public static function prepareDataForSend($metadata, $businessId)
    {
        $dataArray = [];
        $dataArray['businessId'] = $businessId;
        $isDeleteAction = false;

        //TODO: refactor this to be multilevel and move this to some helper
        foreach ($metadata['data'] as $key => $value) {
            if (!array_key_exists('object_type', $value)) {
                continue;
            }

            if ($value['object_type'] === 'type') {
                $model = Yii::$app->cache->get($businessId . '.display_name.' . $value['display_name']);
                Yii::$app->cache->delete($businessId . '.display_name.' . $value['display_name']);

                if ($model) {
                    $isDeleteAction = $model['delete'];
                    $dataArray['data'][$value['uuid']] = [
                        'value' => $model['delete'] ? '' : $model['new_value'],
                        'old_value' => $model['value'],
                        'display_name' => $value['display_name'],
                    ];
                    if ($model['required'] && $model['delete']) {
                        Yii::$app->session->set($businessId . 'deleteRequired', true);
                    }
                }
            }

            if (
                $value['object_type'] === 'set'
                && !empty($value['data'])
            ) {
                foreach ($value['data'] as $nr => $type) {
                    $model = Yii::$app->cache->get(
                        $businessId . '.display_name.' . $value['display_name'] . '-' . $type['display_name']
                    );
                    Yii::$app->cache->delete(
                        $businessId . '.display_name.' . $value['display_name'] . '-' . $type['display_name']
                    );

                    if ($model) {
                        $dataArray['data'][$type['uuid']] = [
                            'value' => $model['delete'] ? '' : $model['new_value'],
                            'old_value' => $model['value'],
                            'display_name' => $type['display_name'],
                        ];
                        if ($model['required'] && $model['delete']) {
                            Yii::$app->session->set($businessId . 'deleteRequired', true);
                        }
                    }
                }
            }
        }

        return ['data' => $dataArray, 'isDelete' => $isDeleteAction];
    }

    /**
     * @param $dataArray
     * @param $portalBusinessApi
     * @param $accountId
     * @param $peopleDbUserId
     * @param $businessId
     * @param bool $delete
     */
    public static function removeAllDataFromBusiness(
        $dataArray,
        $portalBusinessApi,
        $accountId,
        $peopleDbUserId,
        $businessId,
        $delete = false
    ) {
        self::planDeleteRelation($dataArray, $portalBusinessApi, $accountId, $peopleDbUserId, $businessId);
        self::sendNotification(
            'Delete all your data',
            'All Your data have been deleted from the business you have selected. The business might contact you to discuss the changes.'
        );
        if(!$delete) {
            Yii::$app->session->remove($businessId . 'deleteAll');
        }
    }

    /**
     * @param $title
     * @param $content
     */
    public static function sendNotification($title, $content)
    {
        $model = PeopleNotification::instantiate(
            [
                'type' => 'green',
                'status' => 1,
                'expires_at' => date('Y/m/d h:i', strtotime('+7 days')),
                'uid' => Yii::$app->user->id,
                'data' => json_encode(
                    [
                        'title' => Translate::_('people', $title),
                        'body' => Translate::_('people', $content)
                    ]
                )
            ]
        );

        $model->save();
    }

    /**
     * @param $dataArray
     * @param $portalBusinessApi
     * @param $idbClient
     * @param $peopleDbUserId
     * @param $businessId
     */
    public static function deleteRelation($dataArray, $portalBusinessApi, $idbClient, $peopleDbUserId, $businessId)
    {
        $dataArray = DataHelper::prepareNotification($dataArray, true);
        if ($portalBusinessApi->requestUpdateBusinessDataForUser($dataArray)) {
            $response = $idbClient->deleteRelationBusiness2People($businessId, $peopleDbUserId);
        }
    }

    /**
     * @param $dataArray
     * @param $portalBusinessApi
     * @param $accountId
     * @param $peopleDbUserId
     * @param $businessId
     */
    public static function planDeleteRelation($dataArray, $portalBusinessApi, $accountId, $peopleDbUserId, $businessId)
    {
        $dataArray = DataHelper::prepareNotification($dataArray, true);
        $response = $portalBusinessApi->requestUpdateBusinessDataForUser($dataArray);
        if ($response !== false) {
            $disconnectRequest = new PeopleDisconnectRequest();
            $dataArray['crId'] = $response;
            $dataArray['forDelete']['accountId'] = $accountId;
            $dataArray['forDelete']['peopleDbUserId'] = $peopleDbUserId;
            $dataArray['forDelete']['businessId'] = $businessId;
            $disconnectRequest->data = json_encode($dataArray);
            $disconnectRequest->save();
        }
    }

    /**
     * @param $dataArray
     * @param $isDeleteAction
     * @param $portalBusinessApi
     */
    public static function sendUpdateDataToTheBusiness($dataArray, $isDeleteAction, $portalBusinessApi)
    {
        $dataArray = DataHelper::prepareNotification($dataArray, $isDeleteAction);
        if ($portalBusinessApi->requestUpdateBusinessDataForUser($dataArray)) {
            if ($isDeleteAction) {
                Yii::$app->session->setFlash(
                    'successMessage',
                    Translate::_(
                        'people',
                        'Your information has been deleted from the business you have selected. The business might contact you to discuss the changes'
                    )
                );
            } else {
                Yii::$app->session->setFlash(
                    'successMessage',
                    Translate::_(
                        'people',
                        'Your amended data has been sent to the business.'
                    )
                );
            }
        } else {
            Yii::$app->session->setFlash(
                'dangerMessage',
                Translate::_(
                    'people',
                    'An error has occured. Please contact your system administrator.'
                )
            );
        }

        Yii::$app->session->set('emptyData', false);
        Yii::$app->cache->flush();
    }

    /**
     * @param        $businessName
     * @param        $databaseName
     * @param string $templateFull
     * @param string $templateBusinessOnly
     *
     * @return mixed
     */
    public static function businessDisplayName(
        $businessName,
        $databaseName,
        $templateFull = "{businessName} ({databaseName})",
        $templateBusinessOnly = "{businessName}"
    ) {
        $returnValue = $businessName;
        $template = (self::FLAG_SHOW_DATABASE_NAME ? $templateFull : $templateBusinessOnly);
        if (!empty($template)) {
            $returnValue = str_replace('{businessName}', $businessName, $template);
            $returnValue = str_replace('{databaseName}', $databaseName, $returnValue);
        }

        return $returnValue;
    }
}

################################################################################
#                                End of file                                   #
################################################################################
