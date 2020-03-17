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

namespace app\modules\idbStorage\controllers;

################################################################################
# Use(s)                                                                       #
################################################################################

use app\controllers\IdbController;
use app\helpers\DataHelper;
use app\helpers\PeopleConfig;
use app\helpers\Translate;
use DateTime;
use Exception;
use idbyii2\components\PortalApi;
use idbyii2\helpers\File as FileAlias;
use idbyii2\helpers\IdbAccountId;
use idbyii2\helpers\IdbStorageExpressions;
use idbyii2\helpers\Localization;
use idbyii2\models\data\IdbStorageItemDataProvider;
use idbyii2\models\db\PeopleRequestsFiles;
use idbyii2\models\db\PeopleUploadFileRequest;
use idbyii2\models\idb\IdbBankClientPeople;
use idbyii2\models\idb\IdbStorageClient;
use idbyii2\models\idb\IdbStorageItem;
use idbyii2\models\idb\IdbStorageObject;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Default controller for the `IdbStorage` module
 */
class IdbStorageController extends IdbController
{
    private $storageClient;
    private $oid = null;
    private $aid = null;
    private $dbid = null;
    private $vaultId = null;
    private $businessId = null;
    private $uid = null;

    /**
     * @param $action
     * @return bool|void
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $this->storageClient = IdbStorageClient::model();

        switch ($action->id) {
            case 'files':
            case 'init-object':
            case 'download':
            case 'delete':
            case 'save-file':
            case 'upload-requests':
            case 'upload-request':
            case 'set-complete':
                $this->oid = $request->post('oid', $session->get('oid', null));
                $this->aid = $request->post('aid', $session->get('aid', null));
                $this->dbid = $request->post('dbid', $session->get('dbid', null));
                $this->uid = $request->post('uid', $session->get('uid', null));

                if (!empty($request->post('oid'))) {
                    $session->set('oid', $request->post('oid', null));
                    $session->set('aid', $request->post('aid', null));
                    $session->set('dbid', $request->post('dbid', null));
                    $session->set('uid', $request->post('uid', null));
                }

                if (
                    empty($this->oid)
                    || empty($this->aid)
                    || empty($this->dbid)
                    || empty($this->uid)
                ) {
                    $this->redirect(['index']);
                }

                $this->vaultId = IdbAccountId::generateBusinessDbId(
                    $this->oid,
                    $this->aid,
                    $this->dbid
                );

                $this->businessId = IdbAccountId::generateBusinessDbUserId(
                    $this->oid,
                    $this->aid,
                    $this->dbid,
                    $this->uid
                );

                break;
        }
        return parent::beforeAction($action);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $this->view->title = Translate::_('people', 'Shared files');
        $this->view->params['breadcrumbs'][] = [
            'name' => Translate::_('people', 'Shared files'),
            'action' => Url::toRoute(['idb-storage/index']),
        ];
        $accountId = PeopleConfig::get()->getYii2PeopleAccountId();
        $idbClientRelation = IdbBankClientPeople::model($accountId);
        $relatedBusinesses = $idbClientRelation->getRelatedBusinesses(
            IdbAccountId::generatePeopleUserId(
                $accountId,
                Yii::$app->user->identity->id
            )
        );
        $data = DataHelper::prepareData($relatedBusinesses['QueryData'], PortalApi::getBusinessApi());

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'storage',
                    'contentParams' => [
                        'data' => $data,
                    ],
                ]
            ]
        );
    }


    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFiles()
    {
        $requestsCount = PeopleUploadFileRequest::find()->where(['pid' => $this->businessId])->andWhere(['<>', 'type', 'complete'])->count();

        $portalBusinessApi = PortalApi::getBusinessApi();
        $uploadLimit = Yii::$app->controller->module->configIdbStorage['uploadLimit'];

        try {
            $metadata = json_decode(
                $portalBusinessApi->requestBusinessMetadataForUser($this->businessId)['Metadata'],
                true
            );
        } catch (Exception $e) {
            throw new NotFoundHttpException();
        }
        $options['peopleUpload'] = false;
        if (ArrayHelper::getValue(ArrayHelper::getValue($metadata, 'options', []), 'peopleUpload', 'off') === 'on') {
            $options['peopleUpload'] = true;
        }

        $dataProvider = new IdbStorageItemDataProvider();
        $dataProvider->init();
        $request = Yii::$app->request;
        $perPage = 20;

        if (!empty($request->get('per-page'))) {
            $perPage = $request->get('per-page');
        }

        $search = $request->post('search', Yii::$app->session->get('search', []));
        if (empty($search['name'])) {
            $dataProvider->setSearch(
                IdbStorageExpressions::getPeopleItemExpression(),
                [
                    '#col1' => 'uid',
                    '#col2' => 'owner',
                    '#col3' => 'uid'
                ],
                [
                    ':val1' => $this->vaultId,
                    ':val2' => 'false',
                    ':val3' => $this->businessId
                ]
            );
        } else {
            $dataProvider->setSearch(
                IdbStorageExpressions::getPeopleItemExpression(true),
                [
                    '#col1' => 'uid',
                    '#col2' => 'owner',
                    '#col3' => 'uid',
                    '#col4' => 'name'
                ],
                [
                    ':val1' => $this->vaultId,
                    ':val2' => 'false',
                    ':val3' => $this->businessId,
                    ':val4' => str_replace('*', '%', $search['name'])
                ]
            );
        }

        $dataProvider->setPagination(
            [
                'pageSize' => $perPage,
                'page' => $request->get('page') - 1
            ]
        );

        $this->view->params['breadcrumbs'][] = [
            'name' => Translate::_('people', 'Shared files'),
            'action' => Url::toRoute(['idb-storage/index']),
        ];

        $params = [
            'oid' => $this->oid,
            'aid' => $this->aid,
            'dbid' => $this->dbid,
            'uid' => $this->uid
        ];
        $vaultId = $this->vaultId;

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'files',
                    'contentParams' => compact(
                        'dataProvider',
                        'options',
                        'uploadLimit',
                        'requestsCount',
                        'search',
                        'params',
                        'vaultId'
                    )
                ]
            ]
        );
    }

    /**
     * @return mixed
     */
    public function actionInitObject()
    {
        return json_encode($this->storageClient->initObject($this->vaultId));
    }

    /**
     * @return Response
     */
    public function actionSaveFile()
    {
        try {
            $request = Yii::$app->request;
            $info = $this->storageClient->infoStorageObject($request->post('shareOid'))['info']['HTTPHeaders']['etag'];
            $info = str_replace('"', '', $info);
            $user = Yii::$app->user->identity->name . ' ' . Yii::$app->user->identity->surname;
            $filename = $request->post('shareName');

            if ($info === $request->post('shareChecksum')) {
                if (!empty($request->post('requestId'))) {
                    $requestId = $request->post('requestId');
                    $uploadRequest = PeopleUploadFileRequest::findOne($requestId);
                    ++$uploadRequest->uploads;
                    $uploadRequest->save();

                    $requestFile = new PeopleRequestsFiles();
                    $requestFile->oid = $request->post('shareOid');
                    $requestFile->request_id = $requestId;
                    $requestFile->save();

                }

                $businessApi = PortalApi::getBusinessApi();

                $this->storageClient->addStorageItem([
                    'owner' => "true",
                    'oid' => $request->post('shareOid'),
                    'uid' => $this->vaultId,
                    'type' => 'FILE',
                    'name' => $filename
                ]);
                $businessApi->requestNotifyBusinessUser(
                    [
                        'businessId' => $this->businessId,
                        'title' => Translate::_('people', '{user} upload new file: {filename}', compact('user', 'filename')),
                        'body' => Translate::_('people', '{user} upload new file: {filename}', compact('user', 'filename'))
                    ]
                );

                $this->storageClient->addStorageItem([
                    'owner' => "false",
                    'oid' => $request->post('shareOid'),
                    'uid' => $this->businessId,
                    'type' => 'FILE',
                    'name' => $filename
                ]);

                $this->storageClient->editStorageObject(
                    $request->post('shareOid'),
                    [
                        'metadata' => json_encode([
                            'checkSum' => $request->post('shareChecksum'),
                            'origName' => $filename,
                            'size' => $request->post('shareSize'),
                        ]),
                        'attributes' => json_encode(['downloads' => 0])
                    ]
                );
            } else {
                throw new Exception('Wrong checksum');
            }
            Yii::$app->session->setFlash('successMessage', Translate::_('people', 'Upload was successfully'));
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            Yii::$app->session->setFlash('dangerMessage', Translate::_('people', 'There was a problem please try again'));
        }

        return $this->redirect(Yii::$app->request->referrer ? Yii::$app->request->referrer : ['files']);
    }

    /**
     * @param $itemId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDownload($oid)
    {
        try {
            $this->view->params['breadcrumbs'][] = [
                'name' => Translate::_('people', 'Download'),
                'action' => Url::toRoute(['idb-storage/index']),
            ];
            $item = new IdbStorageItem($this->storageClient->findItemByOid($oid)['QueryData'][0]);
            if ($item->uid === $this->vaultId || $item->uid === $this->businessId) {
                $object = new IdbStorageObject($this->storageClient->findStorageObjectById($item->oid)['QueryData'][0]);
                $object->attributes['downloads']++;

                $this->storageClient->editStorageObject($object->oid, ['attributes' => json_encode($object->attributes)]);
                $download = $this->storageClient->downloadStorageObject($item->oid, urlencode($item->name))['downloadUrl'];
                $name = $item->name;
                $preventLoading = true;

                return $this->render(
                    '@app/themes/metronic/views/site/template',
                    [
                        'params' => [
                            'content' => 'download',
                            'contentParams' => compact(
                                'name',
                                'download',
                                'preventLoading'
                            )
                        ]
                    ]
                );
            } else {
                Throw new NotFoundHttpException();
            }
        } catch (Exception $e) {
            Throw new NotFoundHttpException();
        }
    }

    /**
     * @return string
     */
    public function actionUploadRequests()
    {
        $uploadLimit = Yii::$app->controller->module->configIdbStorage['uploadLimit'];
        $requests = PeopleUploadFileRequest::find()->where(['pid' => $this->businessId])->andWhere(['<>', 'type', 'complete'])->all();

        if (empty($requests)) {
            return $this->redirect(['files']);
        } elseif (count($requests) === 1) {
            return $this->redirect(['upload-request', 'id' => $requests[0]->id]);
        }

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'upload-requests',
                    'contentParams' => compact(
                        'uploadLimit',
                        'requests'
                    )
                ]
            ]
        );
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUploadRequest($id)
    {
        $uploadLimit = Yii::$app->controller->module->configIdbStorage['uploadLimit'];

        $uploadRequest = PeopleUploadFileRequest::findOne($id);

        if (empty($uploadRequest) || $uploadRequest->pid !== $this->businessId) {
            Throw new NotFoundHttpException();
        }

        if ($uploadRequest->type === 'complete') {
            return $this->redirect(['upload-requests']);
        }

        $dataProvider = new IdbStorageItemDataProvider();

        $files = PeopleRequestsFiles::findAll(['request_id' => $uploadRequest->id]);

        $search = [];
        foreach ($files as $file) {
            $search [] = [
                'column' => 'oid',
                'value' => $file->oid
            ];
        }

        if (empty($search)) {
            $search [] = [
                'column' => 'oid',
                'value' => '1'
            ];
        }

        $dataProvider->prepareSearch($search, 'OR', 'false');

        return $this->render(
            '@app/themes/metronic/views/site/template',
            [
                'params' => [
                    'content' => 'request',
                    'contentParams' => compact(
                        'uploadLimit',
                        'uploadRequest',
                        'dataProvider',
                        'id'
                    )
                ]
            ]
        );
    }

    public function actionSetComplete($id)
    {
        try {
            $uploadRequest = PeopleUploadFileRequest::findOne($id);
            if ($uploadRequest->pid !== $this->businessId) {
                Throw new \yii\db\Exception('Bad user try to set completed');
            }
            $uploadRequest->type = 'complete';
            $uploadRequest->save();
            Yii::$app->session->setFlash('successMessage', Translate::_('people', 'Completed successfully'));
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            Yii::$app->session->setFlash('dangerMessage', Translate::_('people', 'There was a problem please try again'));
        }

        return $this->redirect(['files']);
    }

    /**
     * @param $itemId
     * @return Response
     * @throws Throwable
     */
    public function actionDelete($itemId)
    {
        try {
            $item = new IdbStorageItem($this->storageClient->findStorageItemById($itemId)['QueryData'][0]);
            if ($item->uid === $this->vaultId) {
                Yii::$app->session->setFlash('dangerMessage', Translate::_('people', 'You don\'d have permission to do that'));
            } elseif ($item->uid === $this->businessId) {
                $peopleFilesRequest = PeopleRequestsFiles::findAll(['oid' => $item->oid]);
                foreach ($peopleFilesRequest as $request) {
                    $uploadRequest = PeopleUploadFileRequest::findOne(['id' => $request->request_id]);
                    $uploadRequest->uploads--;
                    $uploadRequest->save();
                    $request->delete();
                }

                $this->storageClient->deleteStorageItem($itemId);
                Yii::$app->session->setFlash('successMessage', Translate::_('people', 'Deleting was successfully'));
            }
        } catch (Exception $e) {
            Yii::$app->session->setFlash('dangerMessage', Translate::_('people', 'There was a problem please try again'));
        }

        return $this->redirect(Yii::$app->request->referrer ? Yii::$app->request->referrer : ['files']);
    }

    /**
     * @param $oid
     * @return false|string
     */
    public function actionSummary($oid)
    {
        try {
            $item = new IdbStorageItem($this->storageClient->findItemOwnerByOid($oid)['QueryData'][0]);
            $object = new IdbStorageObject($this->storageClient->findStorageObjectById($oid)['QueryData'][0]);

            $data = [
                'name' => $item->name,
                'size' => FileAlias::formatSize($object->metadata['size']),
                'createTime' => Localization::getDateTimePortalFormat(new DateTime($item->createtime)),
                'metadata' => $object->metadata,
                'attributes' => $object->attributes,
                'download' => Url::toRoute(['download', 'oid' => $item->oid]),
            ];

            return json_encode($data);
        } catch (Exception $e) {
            Yii::error("Error get summary");
            Yii::error($e->getMessage());

            return json_encode(['error' => $e->getMessage()]);
        }
    }

}

################################################################################
#                                End of file                                   #
################################################################################
