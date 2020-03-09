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

use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\web\View;

################################################################################
# Class(es)                                                                    #
################################################################################

/**
 * Class WizardAsset
 *
 * @package app\assets
 */
class WizardAsset extends AssetBundle
{

    public $sourcePath = '@app/views/assets';
    public $cssOptions = ['position' => View::POS_END];
    public $jsOptions = ['position' => View::POS_END];
    public $css = [
        'css/bootstrap-directional-buttons.css',
        'css/wizard.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @param null $first
     * @param null $second
     * @param null $third
     * @param null $fourth
     * @param null $fifth
     * @param int  $active
     * @param bool $error
     *
     * @return string
     */
    public function generateWizard(
        $first = null,
        $second = null,
        $third = null,
        $fourth = null,
        $fifth = null,
        $active = 1,
        $error = false
    ) {
        $firstIcon = ((empty($first['Icon'])) ? 'glyphicon-ok' : $first['Icon']);
        $firstTitle = ((empty($first['Title'])) ? '' : $first['Title']);
        $secondIcon = ((empty($second['Icon'])) ? 'glyphicon-ok' : $second['Icon']);
        $secondTitle = ((empty($second['Title'])) ? '' : $second['Title']);
        $thirdIcon = ((empty($third['Icon'])) ? 'glyphicon-ok' : $third['Icon']);
        $thirdTitle = ((empty($third['Title'])) ? '' : $third['Title']);
        $fourthIcon = ((empty($fourth['Icon'])) ? 'glyphicon-ok' : $fourth['Icon']);
        $fourthTitle = ((empty($fourth['Title'])) ? '' : $fourth['Title']);
        $fifthIcon = ((empty($fifth['Icon'])) ? 'glyphicon-ok' : $fifth['Icon']);
        $fifthTitle = ((empty($fifth['Title'])) ? '' : $fifth['Title']);

        return '
            <div class="wizard">
                <div class="wizard-inner">
                    <div class="connecting-line"></div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="' . (($active == 1) ? (($error) ? 'active error' : 'active')
                : (($active > 1) ? 'disabled done' : 'disabled')) . '">
                            <a title="' . $firstTitle . '">
                                <span class="round-tab"><i class="glyphicon ' . $firstIcon . '"></i></span>
                            </a>
                        </li>
                        <li role="presentation" class="' . (($active == 2) ? (($error) ? 'active error' : 'active')
                : (($active > 2) ? 'disabled done' : 'disabled')) . '">
                            <a title="' . $secondTitle . '">
                                <span class="round-tab"><i class="glyphicon ' . $secondIcon . '"></i></span>
                            </a>
                        </li>
                        <li role="presentation" class="' . (($active == 3) ? (($error) ? 'active error' : 'active')
                : (($active > 3) ? 'disabled done' : 'disabled')) . '">
                            <a title="' . $thirdTitle . '">
                                <span class="round-tab"><i class="glyphicon ' . $thirdIcon . '"></i></span>
                            </a>
                        </li>
                        <li role="presentation" class="' . (($active == 4) ? (($error) ? 'active error' : 'active')
                : (($active > 4) ? 'disabled done' : 'disabled')) . '">
                            <a title="' . $fourthTitle . '">
                                <span class="round-tab"><i class="glyphicon ' . $fourthIcon . '"></i></span>
                            </a>
                        </li>
                        <li role="presentation" class="' . (($active == 5) ? (($error) ? 'active error' : 'active')
                : (($active > 5) ? 'disabled done' : 'disabled')) . '">
                            <a title="' . $fifthTitle . '">
                                <span class="round-tab"><i class="glyphicon ' . $fifthIcon . '"></i></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        ';
    }

    /**
     * @param      $next
     * @param null $back
     *
     * @return string
     */
    public function generateWizardActions($next, $back = null)
    {
        $backText = $back['Text'] ?? '';
        $backAction = $back['Action'] ?? '';
        $backTitle = $back['Help'] ?? '';
        $backId = ((empty($back['Id'])) ? 'back' : $back['Id']);
        $backClass = ' ' . ($back['Class'] ?? '');
        $backForceClass = $back['ForceClass'] ?? null;
        $backStyle = ' ' . ($back['Style'] ?? '');

        $nextText = $next['Text'] ?? '';
        $nextAction = $next['Action'] ?? '';
        $nextTitle = $next['Help'] ?? '';
        $nextSubmit = (strtolower(is_string($nextAction) ? $nextAction : null) === 'submit');
        $nextId = ((empty($next['Id'])) ? 'next' : $next['Id']);
        $nextClass = ' ' . ($next['Class'] ?? '');
        $nextForceClass = $next['ForceClass'] ?? null;
        $nextStyle = ' ' . ($next['Style'] ?? '');

        if (empty($backText)) {
            $backButton = '';
        } else {
            $backButton = Html::a(
                $backText,
                $backAction,
                [
                    'class' => $backForceClass ??
                        'btn btn-lg btn-danger btn-arrow-left mid-margin-right wizard-prev pull-left' . $backClass,
                    'style' => $backStyle,
                    'name' => 'back-button',
                    'id' => $backId,
                    'title' => $backTitle
                ]
            );
        }
        if (empty($nextText)) {
            $nextButton = '';
        } elseif ($nextSubmit) {
            $nextButton = Html::submitButton(
                $nextText,
                [
                    'class' => $nextForceClass ??
                        'btn btn-lg btn-success btn-arrow-right mid-margin-right wizard-next pull-right' . $nextClass,
                    'style' => $nextStyle,
                    'name' => 'next-button',
                    'id' => $nextId,
                    'title' => $nextTitle
                ]
            );
        } else {
            $nextButton = Html::a(
                $nextText,
                $nextAction,
                [
                    'class' => 'btn btn-lg btn-success btn-arrow-right mid-margin-right wizard-next pull-right',
                    'name' => 'next-button',
                    'id' => $nextId,
                    'title' => $nextTitle
                ]
            );
        }

        return '
            <div class="wizard">
                <div class="wizard-inner">
                    <div class="connecting-line"></div>
                    <ul class="nav nav-tabs" role="tablist"></ul>
                    <div class="field-block button-height wizard-controls clearfix">
                        <div class="wizard-spacer" style="height:20px"></div>
                        ' . $backButton . '
                        ' . $nextButton . '
                    </div>
                </div>
            </div>
        ';
    }
}

################################################################################
#                                End of file                                   #
################################################################################
