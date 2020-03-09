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
-- # MZ: Create Tables for Password History and for IDB Accounts
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_people.password_history
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_people"."password_history";

CREATE TABLE "p57b_people"."password_history"
(
    "id" serial PRIMARY KEY,
    "uid" varchar(255) not null,
    "passwd" varchar(255) not null,
    "createtime" timestamp without time zone default CURRENT_TIMESTAMP
);

CREATE INDEX password_history_idx_uid ON "p57b_people"."password_history" ("uid");

ALTER TABLE "p57b_people"."password_history" OWNER TO p57b_people;

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
