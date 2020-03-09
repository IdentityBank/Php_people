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
# Localization                                                                 #
################################################################################

return
    [
        'sourcePath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR,
        'languages' => ['en-GB', 'nl-NL', 'pl-PL', 'de-DE', 'fr-FR'],
        'translator' => 'Translate::_',
        'sort' => true,
        'removeUnused' => true,
        'only' => ['*.php', '*.inc'],
        'except' => [
            '.svn',
            '.git',
            '.gitignore',
            '.gitkeep',
            '.hgignore',
            '.hgkeep',
            '/messages',
            '/BaseYii.php',
        ],

        'format' => 'php',
        'messagePath' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'messages',
        'overwrite' => true,

        /*
        // 'po' output format is for saving messages to gettext po files.
        'format' => 'po',
        // Name of the file that will be used for translations.
        'catalog' => 'messages',
        */

        /*
        // 'db' output format is for saving messages to database.
        'format' => 'db',
        // Connection component to use. Optional.
        'db' => 'db',
        // Custom source message table. Optional.
        // 'sourceMessageTable' => '{{%source_message}}',
        // Custom name for translation message table. Optional.
        // 'messageTable' => '{{%message}}',
        */
    ];

################################################################################
#                                End of file                                   #
################################################################################
