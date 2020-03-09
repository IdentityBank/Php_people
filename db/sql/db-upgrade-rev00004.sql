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
-- # MZ: Create Search DB
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Schema: p57b_search
-- # ---------------------------------------------------------------------- # --

CREATE SCHEMA IF NOT EXISTS p57b_search AUTHORIZATION p57b;
GRANT ALL ON SCHEMA p57b_search TO p57b_search;

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_search.people_user_data
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_search"."people_user_data";

CREATE TABLE "p57b_search"."people_user_data"
(
    "key_hash" varchar(255) not null,
    "value_hash" varchar(255) not null,
    "uid_hash" varchar(255) not null,
    "uid" varchar(255) not null,
    primary key ("key_hash","value_hash","uid_hash")
);

CREATE INDEX people_user_data_idx_key_hash ON "p57b_search"."people_user_data" ("key_hash");
CREATE INDEX people_user_data_idx_value_hash ON "p57b_search"."people_user_data" ("value_hash");
CREATE INDEX people_user_data_idx_uid_hash ON "p57b_search"."people_user_data" ("uid_hash");

ALTER TABLE "p57b_search"."people_user_data" OWNER TO p57b_search;

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_search.people_user_account
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_search"."people_user_account";

CREATE TABLE "p57b_search"."people_user_account"
(
    "uid_hash" varchar(255) not null,
    "login_hash" varchar(255) not null,
    primary key ("uid_hash","login_hash")
);

CREATE INDEX people_user_account_idx_uid_hash ON "p57b_search"."people_user_account" ("uid_hash");

ALTER TABLE "p57b_search"."people_user_account" OWNER TO p57b_search;

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
