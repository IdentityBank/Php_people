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
# DB Config                                                                    #
################################################################################

$dbHost = PeopleConfig::get()->getYii2PeopleDbHost();
$dbPort = PeopleConfig::get()->getYii2PeopleDbPort();
$dbSchema = PeopleConfig::get()->getYii2PeopleDbSchema();
$dbName = PeopleConfig::get()->getYii2PeopleDbName();
$dbUser = PeopleConfig::get()->getYii2PeopleDbUser();
$dbPassword = PeopleConfig::get()->getYii2PeopleDbPassword();

$dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName";

return
    [
        'class' => 'yii\db\Connection',
        'dsn' => "$dsn",
        'username' => "$dbUser",
        'password' => "$dbPassword",
        'charset' => 'utf8',
        'schemaMap' =>
            [
                'pgsql' =>
                    [
                        'class' => 'yii\db\pgsql\Schema',
                        'defaultSchema' => "$dbSchema"
                    ]
            ],
        'on afterOpen' => function ($event) {
            $dbSchema = PeopleConfig::get()->getYii2PeopleDbSchema();
            $event->sender->createCommand("SET search_path TO $dbSchema;")->execute();
        },
    ];

################################################################################
#                                End of file                                   #
################################################################################
