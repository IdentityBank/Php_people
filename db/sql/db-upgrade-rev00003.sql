-- # * ********************************************************************* *
-- # *                                                                       *
-- # *   People Portal                                                       *
-- # *   This file is part of people. This project may be found at:          *
-- # *   https://github.com/IdentityBank/Php_people.                         *
-- # *                                                                       *
-- # *   Copyright (C) 2020 by Identity Bank. All Rights Reserved.           *
-- # *   https://www.identitybank.eu - You belong to you                     *
-- # *                                                                       *
-- # *   This program is free software: you can redistribute it and/or       *
-- # *   modify it under the terms of the GNU Affero General Public          *
-- # *   License as published by the Free Software Foundation, either        *
-- # *   version 3 of the License, or (at your option) any later version.    *
-- # *                                                                       *
-- # *   This program is distributed in the hope that it will be useful,     *
-- # *   but WITHOUT ANY WARRANTY; without even the implied warranty of      *
-- # *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the        *
-- # *   GNU Affero General Public License for more details.                 *
-- # *                                                                       *
-- # *   You should have received a copy of the GNU Affero General Public    *
-- # *   License along with this program. If not, see                        *
-- # *   https://www.gnu.org/licenses/.                                      *
-- # *                                                                       *
-- # * ********************************************************************* *

-- #############################################################################
-- # DB migration file
-- #############################################################################

-- #############################################################################
-- # MZ: Initial setup for RBAC
-- #############################################################################

-- # auth_item
INSERT INTO p57b_people.auth_item (name, type, description, rule_name, data, created_at, updated_at) VALUES ('idb_people', 1, 'The idb people users.', null, null, (select extract(epoch from now())), (select extract(epoch from now())));

-- # password_policy
DELETE FROM p57b_people.password_policy WHERE "name" = 'default';
INSERT INTO  p57b_people.password_policy ("name","lowercase","uppercase","digit","special","special_chars_set","min_types","reuse_count","min_recovery_age","max_age","min_length","max_length","change_initial","level") VALUES ('default',4,3,3,2,'!@#$%^&*()',3,10,60,60,12,128,1,300);

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
