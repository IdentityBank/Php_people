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
# Include(s)                                                                   #
################################################################################

include_once 'simplelog.inc';
include_once 'jsonsimpleconfig.inc';
include_once 'idbyii2/helpers/IdbYii2Config.php';

################################################################################
# Use(s)                                                                       #
################################################################################

use idbyii2\helpers\IdbAccountNumberDestination;
use idbyii2\helpers\IdbYii2Config;
use function xmz\simplelog\logLevel;
use function xmz\simplelog\registerLogger;

################################################################################
# Local Config                                                                 #
################################################################################

const peopleConfigFile = '/etc/p57b/people.jsc';
const peopleConfigFileDefault = 'config.jsc';

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class PeopleConfig
 */
class PeopleConfig extends IdbYii2Config
{

    private static $instance;

    /**
     * PeopleConfig constructor.
     */
    final protected function __construct()
    {
        parent::__construct();
        $this->mergeJscFile(dirname(__FILE__) . '/' . peopleConfigFileDefault);
        $this->mergeJscFile(peopleConfigFile);
        registerLogger($this->getLogName(), $this->getLogPath());
        logLevel($this->getLogLevel());
    }

    public static function get()
    {
        if (!isset(self::$instance) || !self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function isDebug()
    {
        return $this->getValue(null, 'debug', false);
    }

    function isAssetManagerForceCopy()
    {
        return $this->getValue(null, 'assetManagerForceCopy', false);
    }

    function isYiiErrorHandlerEnabled()
    {
        return $this->getValue(null, 'yiiEnableErrorHandler', true);
    }

    function getLogLevel()
    {
        return $this->getValue("Log", "logLevel");
    }

    function getYii2PeopleErrorsToSendRecovery()
    {
        return $this->getValue('"Yii2"."people"', 'errorsToSendRecovery', 3);
    }

    function getLogName()
    {
        return $this->getValue("Log", "logName");
    }

    function getLogPath()
    {
        return $this->getValue("Log", "logPath");
    }

    function getTheme()
    {
        return $this->getValue(null, 'theme', "default");
    }

    function getThemeErrorPage()
    {
        return $this->getValue(null, 'themeErrorPage', 6);
    }

    function getLoginUrl()
    {
        return $this->getValue(null, 'loginUrl', ['/login']);
    }

    function useBrowserLocalization()
    {
        return $this->getValue('Localization', 'browser');
    }

    function getDefaultLocalizationLanguage()
    {
        return $this->getValue('Localization', 'defaultLanguage');
    }

    function getLocalizationLanguage()
    {
        return $this->getValue('Localization', 'language', $this->getDefaultLocalizationLanguage());
    }

    function getWebLanguage()
    {
        return $this->getValue('Localization', 'webLanguage', $this->getDefaultLocalizationLanguage());
    }

    function isAuditEnabled()
    {
        return $this->getValue('Audit', 'enabled', false);
    }

    function isAuditEncrypted()
    {
        return $this->getValue('Audit', 'messageEncrypted', false);
    }

    function getAuditMessagePassword()
    {
        return $this->getValue('Audit', 'messagePassword', 'password');
    }

    function getAuditMessageSeparator()
    {
        return $this->getValue('Audit', 'messageSeparator', ']|[');
    }

    function getAuditPath()
    {
        return $this->getValue('Audit', 'auditPath');
    }

    function getAuditFileName()
    {
        return $this->getValue('Audit', 'auditFile', 'yii2_people_audit.log');
    }

    function getIdBankHost()
    {
        return $this->getValue('"IDBank"."IdBankClient"', 'host', '127.0.0.1');
    }

    function getIdBankPort()
    {
        return $this->getValue('"IDBank"."IdBankClient"', 'port', 57);
    }

    function getYii2PeopleCookieValidationKey()
    {
        return $this->getValue('"Yii2"."people"', 'cookieValidationKey');
    }

    function isIdentityByAccessTokenEnabled()
    {
        return $this->getValue('"Yii2"."people"', 'identityByAccessTokenEnabled');
    }

    function getYii2PeopleEnableAutoLogin()
    {
        return $this->getValue('"Yii2"."people"', 'enableAutoLogin', false);
    }

    function getYii2PeopleAbsoluteAuthTimeout()
    {
        return $this->getValue('"Yii2"."people"', 'absoluteAuthTimeout', 43200);
    }

    function getYii2PeopleAuthTimeout()
    {
        return $this->getValue('"Yii2"."people"', 'authTimeout', 3600);
    }

    function getYii2PeopleAccountId()
    {
        return $this->getValue('"Yii2"."people"', 'accountId');
    }

    function isYii2PeopleHelpEnabled()
    {
        return $this->getValue('"Yii2"."people"', 'help', true);
    }

    function passwordPolicy()
    {
        return $this->getValue('"Yii2"."people"', 'passwordPolicy');
    }

    function getYii2IdbSecurityMagicShift()
    {
        return $this->getValue('"Yii2"."security"."idb"', 'magic_shift', 5);
    }

    function getYii2PeopleSearchPassword()
    {
        return $this->getValue('"Yii2"."security"."idb"', 'search_password', 'password');
    }

    function getYii2PeopleSearchBlowfishCost()
    {
        return $this->getValue('"Yii2"."security"."idb"', 'search_blowfish', 1);
    }

    function getYii2PeopleLogPassword()
    {
        return $this->getValue('"Yii2"."security"."idb"', 'log_password', 'password');
    }

    function getYii2PeopleLogBlowfishCost()
    {
        return $this->getValue('"Yii2"."security"."idb"', 'log_blowfish', 1);
    }

    function getYii2PeopleModulesIdbUserConfigUserAccount()
    {
        $configUserAccount = $this->getValue('"Yii2"."people"."modules"."idbuser"', 'configUserAccount');
        if ((!empty($configUserAccount)) && (is_array($configUserAccount))) {
            return $configUserAccount;
        }
        return null;
    }

    function getYii2PeopleModulesSignUpConfig()
    {
        $configSignUp = $this->getValue(
            '"Yii2"."people"."modules"."signup"',
            'configSignUp'
        );
        if ((!empty($configSignUp)) && (is_array($configSignUp))) {
            return $configSignUp;
        }

        return null;
    }

    function getYii2PeopleModulesIdbUserConfigUserData()
    {
        $configUserData = $this->getValue('"Yii2"."people"."modules"."idbuser"', 'configUserData');
        if ((!empty($configUserData)) && (is_array($configUserData))) {
            return $configUserData;
        }
        return null;
    }

    function getYii2PeopleBusinessToPeopleMessagesModulesConfig()
    {
        $configB2Pmessages = $this->getValue(
            '"Yii2"."people"."modules"."business2peoplemessages"',
            'configB2Pmessages'
        );
        if ((!empty($configB2Pmessages)) && (is_array($configB2Pmessages))) {
            return $configB2Pmessages;
        }

        return null;
    }

    function getYii2PeopleModulesNotificationsConfig()
    {
        $configNotification = $this->getValue(
            '"Yii2"."people"."notification"',
            'configNotification'
        );

        if ((!empty($configNotification)) && (is_array($configNotification))) {
            return $configNotification;
        }

        return null;
    }

    function getYii2MfaIssuer($portal = IdbAccountNumberDestination::people)
    {
        return parent::getYii2MfaIssuer(IdbAccountNumberDestination::people);
    }

    function getYii2PeopleModulesPasswordRecoveryConfig()
    {
        $configSignUp = $this->getValue(
            '"Yii2"."people"."modules"."passwordrecovery"',
            'configPasswordrecovery'
        );
        if ((!empty($configSignUp)) && (is_array($configSignUp))) {
            return $configSignUp;
        }

        return null;
    }

}

################################################################################
#                                End of file                                   #
################################################################################
