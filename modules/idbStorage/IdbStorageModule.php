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

namespace app\modules\idbStorage;

################################################################################
# Use(s)                                                                       #
################################################################################

use yii\base\Module;

################################################################################
# Class(es)                                                                    #
################################################################################

class IdbStorageModule extends Module
{
    /**
     * Upload limit in mb
     */
    public $configIdbStorageDefault = [
        'uploadLimit' => 50,
    ];

    public $configIdbStorage = [];

    public function init()
    {
        if (is_array($this->configIdbStorage)) {
            $this->configIdbStorage = array_merge($this->configIdbStorageDefault, $this->configIdbStorage);
        } else {
            $this->configIdbStorage = $this->configIdbStorageDefault;
        }

        parent::init();
    }
}

################################################################################
#                                End of file                                   #
################################################################################
