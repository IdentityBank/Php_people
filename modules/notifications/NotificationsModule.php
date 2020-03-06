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

namespace app\modules\notifications;

################################################################################
# Use(s)                                                                       #
################################################################################

use yii\base\Module;


################################################################################
# Class(es)                                                                    #
################################################################################

class NotificationsModule extends Module
{

    public $configNotificationsDefault = [
        'blowfishCost' => 1,
        'dataPassword' => 'password',
    ];

    public $configNotifications = [];

    public function init()
    {
        if (is_array($this->configNotifications)) {
            $this->configNotifications = array_merge($this->configNotificationsDefault, $this->configNotifications);
        } else {
            $this->configNotifications = $this->configNotificationsDefault;
        }

        parent::init();
    }
}

################################################################################
#                                End of file                                   #
################################################################################
