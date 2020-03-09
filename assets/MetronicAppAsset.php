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

namespace app\assets;

################################################################################
# Use(s)                                                                       #
################################################################################

use yii\web\AssetBundle;
use yii\web\View;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class MetronicAppAsset
 *
 * @package app\assets
 */
class MetronicAppAsset extends AssetBundle
{

    public $sourcePath = '@app/themes/metronic/views/assets';

    public $css = [
        'css/main.css'
    ];

    public $cssOptions = [
        'position' => View::POS_END
    ];

    public $depends = [
        'app\assets\MetronicAsset',
    ];

    public $js = [];

    /**
     * @return string
     */
    public function getAssetUrl()
    {
        return $this->baseUrl . '/';
    }

    public function businessIndexAssets()
    {
        $this->js [] = 'js/businessTable.js';
    }

    public function businessMapAssets()
    {
        $this->js [] = 'js/mapDnD.js';
    }

    public function tooltipAssets()
    {
        $this->js [] = 'js/tooltip.js';
    }
}

################################################################################
#                                End of file                                   #
################################################################################
