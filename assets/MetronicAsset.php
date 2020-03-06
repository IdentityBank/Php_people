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
 * Class MetronicAsset
 *
 * @package app\assets
 */
class MetronicAsset extends AssetBundle
{

    public $sourcePath = '@app/themes/metronic/views/assetsMetronic';

    public $jsOptions =
        [
            'position' => View::POS_END
        ];

    /**
     * @return string
     */
    public function getAssetUrl()
    {
        return $this->baseUrl . '/';
    }

    public $depends =
        [
            'yii\web\JqueryAsset',
            'yii\web\YiiAsset',
        ];

    public $js =
        [
            'assets/vendors/general/popper.js/dist/umd/popper.js',
            'assets/vendors/general/bootstrap/dist/js/bootstrap.min.js',
            'assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js',
            'assets/vendors/general/sticky-js/dist/sticky.min.js',
            'assets/vendors/general/owl.carousel/dist/owl.carousel.js',
            'assets/vendors/general/sweetalert2/dist/sweetalert2.js',
            'assets/js/demo4/scripts.bundle.min.js'
        ];

    public $css =
        [
            'assets/vendors/general/bootstrap/dist/css/bootstrap.min.css',
            'assets/vendors/general/animate.css/animate.css',
            'assets/vendors/general/toastr/build/toastr.css',
            'assets/vendors/general/sweetalert2/dist/sweetalert2.css',
            'assets/vendors/custom/vendors/line-awesome/css/line-awesome.css',
            'assets/vendors/custom/vendors/flaticon/flaticon.css',
            'assets/vendors/custom/vendors/flaticon2/flaticon.css',
            'assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css',
            'assets/css/demo4/style.bundle.min.css'
        ];

    public function layoutForms($view)
    {
        // CSS
        $this->css[] = 'plugins/iCheck/all.css';

        // JS
        $this->js[] = 'plugins/iCheck/icheck.min.js';
    }

    public function layoutError($view, $id)
    {
        // CSS
        $this->css[] = "assets/css/demo4/pages/general/error/error-$id.css";
    }
}

################################################################################
#                                End of file                                   #
################################################################################
