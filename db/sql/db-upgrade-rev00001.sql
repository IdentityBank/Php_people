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
-- # MZ: Initial setup for people authentication
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Schema: p57b_people
-- # ---------------------------------------------------------------------- # --

DROP SCHEMA IF EXISTS p57b_people CASCADE;
CREATE SCHEMA p57b_people AUTHORIZATION p57b_people;

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_people.user_data
-- # Table: p57b_people.user_account
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_people"."user_data";
DROP TABLE IF EXISTS "p57b_people"."user_account";

CREATE TABLE "p57b_people"."user_data"
(
    "uid" varchar(255) not null,
    "key_hash" varchar(255) not null,
    "key" text,
    "value" text,
    primary key ("uid","key_hash")
);

CREATE TABLE "p57b_people"."user_account"
(
    "login" varchar(255) not null,
    "access_token" varchar(255) default null,
    "uid" varchar(255) not null,
    primary key ("login")
);

CREATE INDEX user_data_idx_uid ON "p57b_people"."user_data" ("uid");
CREATE UNIQUE INDEX user_account_idx_access_token ON "p57b_people"."user_account" ("access_token");

ALTER TABLE "p57b_people"."user_data" OWNER TO p57b_people;
ALTER TABLE "p57b_people"."user_account" OWNER TO p57b_people;

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_people.password_policy
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_people"."password_policy";

CREATE TABLE p57b_people.password_policy
(
  "name" varchar(255) default null,
  "lowercase" smallint,
  "uppercase" smallint,
  "digit" smallint,
  "special" smallint,
  "special_chars_set" varchar(255) default null,
  "min_types" smallint,
  "reuse_count" smallint,
  "min_recovery_age" smallint,
  "max_age" smallint,
  "min_length" smallint,
  "max_length" smallint,
  "change_initial" smallint,
  "level" smallint,
  primary key ("name")
);

ALTER TABLE p57b_people.password_policy OWNER TO p57b_people;

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
