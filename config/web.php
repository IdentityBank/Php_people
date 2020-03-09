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
# Use(s)                                                                       #
################################################################################

use idbyii2\helpers\IdbYii2Config;
use idbyii2\helpers\Localization;

################################################################################
# Load params                                                                  #
################################################################################

$params = require(__DIR__ . '/params.php');

################################################################################
# Web Config                                                                   #
################################################################################

$config =
    [
        'id' => 'IDB - People',
        'name' => 'Identity Bank - People',
        'version' => '1.0.0',
        'vendorPath' => $yii . '/vendor',
        'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
        'language' => PeopleConfig::get()->getWebLanguage(),
        'sourceLanguage' => 'en-GB',
        'aliases' => [
            '@idbyii2' => '/usr/local/share/p57b/php/idbyii2',
        ],
        'modules' => [
            'events' => [
                'class' => 'app\modules\events\EventsModule',
                'controllerNamespace' => 'app\modules\events\controllers',
            ],
            'idbuser' => [
                'class' => 'app\modules\idbuser\IdbUserModule',
                'controllerNamespace' => 'app\modules\idbuser\controllers',
                'configUserAccount' => PeopleConfig::get()->getYii2PeopleModulesIdbUserConfigUserAccount(),
                'configUserData' => PeopleConfig::get()->getYii2PeopleModulesIdbUserConfigUserData(),
            ],
            'idb-storage' => [
                'class' => 'app\modules\idbStorage\IdbStorageModule',
                'controllerNamespace' => 'app\modules\idbStorage\controllers',
                'configIdbStorage' => IdbYii2Config::get()->getIdbStorageModuleConfig()
            ],
            'mfarecovery' => [
                'class' => 'app\modules\mfarecovery\MfaRecoveryModule',
            ],
            'registration' => [
                'class' => 'app\modules\registration\Registration',
                'controllerNamespace' => 'app\modules\registration\controllers'
            ],
            'passwordrecovery' => [
                'class' => 'app\modules\passwordrecovery\PasswordRecoveryModule',
                'configPasswordRecovery' => PeopleConfig::get()->getYii2PeopleModulesPasswordRecoveryConfig(),
            ],
            'peopleuser' => [
                'class' => 'app\modules\peopleuser\Peopleuser',
                'controllerNamespace' => 'app\modules\peopleuser\controllers'
            ],
            'business2peoplemessages' => [
                'class' => 'app\modules\business2peoplemessages\Business2PeopleMessagesModule',
                'configB2Pmessages' => PeopleConfig::get()->getYii2PeopleBusinessToPeopleMessagesModulesConfig(),
            ],
            'notifications' => [
                'class' => 'app\modules\notifications\NotificationsModule',
                'configNotifications' => PeopleConfig::get()->getYii2PeopleModulesNotificationsConfig(),
            ],
            'idbverification' => [
                'class' => 'app\modules\idbverification\IdbVerificationModule',
                'controllerNamespace' => 'app\modules\idbverification\controllers',
            ],
        ],
        'components' => [
            'assetManager' => [
                'class' => 'yii\web\AssetManager',
                'forceCopy' => PeopleConfig::get()->isAssetManagerForceCopy(),
                'appendTimestamp' => true,
            ],
            'idbstorageclient' => [
                'class' => 'idbyii2\models\idb\IdbStorageClient',
                'storageName' => IdbYii2Config::get()->getIdbStorageName(),
                'host' => IdbYii2Config::get()->getIdbStorageHost(),
                'port' => IdbYii2Config::get()->getIdbStoragePort(),
                'configuration' => IdbYii2Config::get()->getIdbStorageConfiguration()
            ],
            'audit' => [
                'class' => 'idbyii2\audit\AuditComponent',
                'auditConfig' => [
                    'class' => 'idbyii2\audit\AuditConfig',
                    'enabled' => PeopleConfig::get()->isAuditEnabled(),
                ],
                'auditFile' => [
                    'class' => 'idbyii2\audit\FileAudit',
                    'auditPath' => PeopleConfig::get()->getAuditPath(),
                    'auditFile' => PeopleConfig::get()->getAuditFileName(),
                ],
                'auditMessage' => [
                    'class' => 'idbyii2\audit\AuditMessage',
                    'liveServerLog' => !PeopleConfig::get()->isDebug(),
                    'separator' => PeopleConfig::get()->getAuditMessageSeparator(),
                    'encrypted' => PeopleConfig::get()->isAuditEncrypted(),
                    'password' => PeopleConfig::get()->getAuditMessagePassword(),
                ],
            ],
            'request' => [
                'cookieValidationKey' => PeopleConfig::get()->getYii2PeopleCookieValidationKey(),
                'csrfCookie' => [
                    'httpOnly' => true,
                    'secure' => true,
//                    'sameSite' => (PHP_VERSION_ID >= 70300 ? yii\web\Cookie::SAME_SITE_LAX : null),
                ],
                'enableCookieValidation' => true,
                'enableCsrfCookie' => true,
                'enableCsrfValidation' => true,
            ],
            'idbbusinessportalapi' => [
                'class' => 'idbyii2\components\PortalApi',
                'configuration' => PeopleConfig::get()->getBusinessPortalApiConfiguration(),
            ],
            'idbankclient' => [
                'class' => 'idbyii2\models\idb\IdbBankClient',
                'service' => 'people',
                'host' => PeopleConfig::get()->getIdBankHost(),
                'port' => PeopleConfig::get()->getIdBankPort(),
                'configuration' => PeopleConfig::get()->getIdBankConfiguration()
            ],
            'idbankclientpeople' => [
                'class' => 'idbyii2\models\idb\IdbBankClientPeople',
                'service' => 'people',
                'host' => PeopleConfig::get()->getIdBankHost(),
                'port' => PeopleConfig::get()->getIdBankPort(),
                'configuration' => PeopleConfig::get()->getIdBankConfiguration()
            ],
            'idbankclientbusiness' => [
                'class' => 'idbyii2\models\idb\IdbBankClientBusiness',
                'service' => 'business',
                'host' => PeopleConfig::get()->getIdBankHost(),
                'port' => PeopleConfig::get()->getIdBankPort(),
                'configuration' => PeopleConfig::get()->getIdBankConfiguration()
            ],
            'idbmessenger' => [
                'class' => 'idbyii2\components\Messenger',
                'configuration' => PeopleConfig::get()->getMessengerConfiguration(),
            ],
            'idbrabbitmq' => [
                'class' => 'idbyii2\components\IdbRabbitMq',
                'host' => PeopleConfig::get()->getIdbRabbitMqHost(),
                'port' => PeopleConfig::get()->getIdbRabbitMqPort(),
                'user' => PeopleConfig::get()->getIdbRabbitMqUser(),
                'password' => PeopleConfig::get()->getIdbRabbitMqPassword()
            ],
            'db' => require(__DIR__ . '/db_p57b_people.php'), //RBAC
            'p57b_people' => require(__DIR__ . '/db_p57b_people.php'),
            'p57b_people_search' => require(__DIR__ . '/db_p57b_people.php'),
            'p57b_people_log' => require(__DIR__ . '/db_p57b_people.php'),
            'user' => [
                'identityClass' => 'idbyii2\models\identity\IdbPeopleUser',
                'enableAutoLogin' => PeopleConfig::get()->getYii2PeopleEnableAutoLogin(),
                'identityCookie' => ['name' => '_people_identity-p57b', 'httpOnly' => true],
                'absoluteAuthTimeout' => PeopleConfig::get()->getYii2PeopleAbsoluteAuthTimeout(),
                'authTimeout' => PeopleConfig::get()->getYii2PeopleAuthTimeout(),
                'loginUrl' => PeopleConfig::get()->getLoginUrl(),
            ],
            'authManager' => [
                'class' => 'yii\rbac\DbManager',
                'defaultRoles' => ['idb_people'],
                'cache' => YII_DEBUG ? null : 'cache',
            ],
            'urlManager' => [
                'class' => 'yii\web\UrlManager',
                'showScriptName' => false,
                'enablePrettyUrl' => true,
                'enableStrictParsing' => false,
                'rules' => [
                    'defaultRoute' => '/site/index',
                    'login' => '/site/login',
                    'mfa' => '/site/mfa',
                    'logout' => '/site/logout',
                    'idb-login' => '/site/idb-login',
                    'idb-api' => '/site/idb-api',
                    'profile' => '/site/profile',
                    'events/<action:[\w-]+>' => '/events/default/<action>',
                    'idb-storage' => '/idb-storage/idb-storage',
                    'idb-storage/<action:[\w-]+>' => '/idb-storage/idb-storage/<action>',
                    'passwordrecovery' => '/passwordrecovery/wizard',
                    'passwordrecovery/<action:[\w-]+>' => '/passwordrecovery/wizard/<action>',
                    'signup' => '/registration/signup',
                    'signup/<action:[\w-?=]+>' => '/registration/signup/<action>',
                    'mfarecovery' => '/mfarecovery/wizard',
                    'mfarecovery/<action:[\w-]+>' => '/mfarecovery/wizard/<action>',
                    'data' => '/peopleuser/data',
                    'data/<action:[\w-?=]+>' => '/peopleuser/data/<action>',
                    'dataset' => '/peopleuser/dataset',
                    'dataset/<action:[\w-?=]+>' => '/peopleuser/dataset/<action>',
                    'manage' => '/peopleuser/manage',
                    'manage/<action:[\w-?=]+>' => '/peopleuser/manage/<action>',
                    'business' => '/peopleuser/business',
                    'business/<action:[\w-?=]+>' => '/peopleuser/business/<action>',
                ],
            ],
            'cache' => [
                'class' => 'yii\caching\FileCache',
            ],
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
            'log' => [
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
                    [
                        'class' => 'yii\log\FileTarget',
                        'levels' => ['error'],
                    ],
                    [
                        'class' => 'yii\log\FileTarget',
                        'logVars' => [],
                        'categories' => ['people'],
                        'levels' => ['info'],
                        'logFile' => '@runtime/logs/info.log',
                        'maxFileSize' => 1024 * 2,
                        'maxLogFiles' => 10,
                    ],
                    [
                        'class' => 'yii\log\FileTarget',
                        'logVars' => [],
                        'levels' => ['error', 'warning'],
                        'logFile' => '/var/log/p57b/p57b.people-errors.log',
                    ],
                    [
                        'class' => 'yii\log\FileTarget',
                        'logVars' => [],
                        'levels' => ['trace', 'info'],
                        'logFile' => '/var/log/p57b/p57b.people-debug.log',
                    ],
                ],
            ],
            'i18n' => [
                'translations' => [
                    'people' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'forceTranslation' => true,
                        'sourceLanguage' => 'en-GB',
                        'basePath' => '@app/messages',
                    ],
                    'idbyii2' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'forceTranslation' => true,
                        'sourceLanguage' => 'en-GB',
                        'basePath' => '@idbyii2/messages',
                    ],
                    'idbexternal' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'forceTranslation' => true,
                        'sourceLanguage' => 'en-GB',
                        'basePath' => '@idbyii2/messages',
                    ],
                ],
            ],
            'formatter' => [
                'class' => 'yii\i18n\Formatter',
                'dateFormat' => 'php:' . Localization::getDateFormat(),
                'datetimeFormat' => 'php:' . Localization::getDateTimeFormat(false),
                'timeFormat' => 'php:' . Localization::geTimeFormat(false),
                'decimalSeparator' => ',',
                'thousandSeparator' => ' ',
                'currencyCode' => 'EUR',
            ],
        ],
        'params' => $params,
    ];

if (defined('APP_THEME')) {
    switch (APP_THEME) {
        case 'metronic':
            {
                $config['components']['view'] = [
                    'theme' => [
                        'basePath' => '@app/themes/metronic',
                        'baseUrl' => '@web/themes/metronic',
                        'pathMap' => [
                            '@app/views' => '@app/themes/metronic/views',
                            '@app/modules' => '@app/themes/metronic/modules',
                        ],
                    ]
                ];
            }
            break;
        case 'default':
        default:
        {
        }
    }
}

if (defined('APP_LANGUAGE')) {
    $config['language'] = APP_LANGUAGE;
}

if (YII_ENV_DEV) {
    $allowedIPs = ['127.0.0.1', '::1'];
    if (!empty(PeopleConfig::get()->getYii2SecurityGiiAllowedIP())) {
        $allowedIPs [] = PeopleConfig::get()->getYii2SecurityGiiAllowedIP();
    }

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => $allowedIPs
    ];
}

$config['bootstrap'] = ['log'];
$config['on beforeRequest'] = function ($event) {
    idbyii2\models\db\PeopleModel::initModel();
};

return $config;

################################################################################
#                                End of file                                   #
################################################################################
