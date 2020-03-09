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
-- # MZ: Create Log DB
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Schema: p57b_log
-- # ---------------------------------------------------------------------- # --

CREATE SCHEMA IF NOT EXISTS p57b_log AUTHORIZATION p57b;
GRANT ALL ON SCHEMA p57b_log TO p57b_log;

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_log.people_authlog
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_log"."people_authlog";

CREATE TABLE "p57b_log"."people_authlog"
(
    "uid" varchar(255) not null,
    "event" varchar(64) not null,
    "event_data" text,
    "ip" varchar(255) not null,
    "timestamp" timestamp without time zone default CURRENT_TIMESTAMP,
    primary key ("uid","event","ip","timestamp")
);

CREATE INDEX people_authlog_idx_uid ON "p57b_log"."people_authlog" ("uid");

ALTER TABLE "p57b_log"."people_authlog" OWNER TO p57b_log;

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
