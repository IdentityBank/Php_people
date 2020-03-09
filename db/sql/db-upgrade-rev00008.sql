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
-- # AT: Create password_history, password_policy Tables
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_people.password_policy
-- # Table: p57b_people.password_history
-- # ---------------------------------------------------------------------- # --

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

-- # password_policy
DELETE FROM p57b_people.password_policy WHERE "name" = 'default';
INSERT INTO  p57b_people.password_policy ("name","lowercase","uppercase","digit","special","special_chars_set","min_types","reuse_count","min_recovery_age","max_age","min_length","max_length","change_initial","level") VALUES ('default',4,3,3,2,'!@#$%^&*()',3,10,60,60,12,128,1,300);

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
