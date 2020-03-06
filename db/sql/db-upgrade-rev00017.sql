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
-- # KD: Create Upload File Request
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_people.upload_file_request
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_people"."upload_file_request";

CREATE TABLE "p57b_people"."upload_file_request"
(
    "id" serial PRIMARY KEY,
    "dbid" text NOT NULL,
    "pid" text NOT NULL,
    "upload_limit" int DEFAULT 1,
    "uploads" int DEFAULT 0,
    "timestamp" timestamp without time zone DEFAULT now(),
    "type" text NOT NULL,
    "request_uuid" varchar(255) NOT NULL,
    "name" varchar(255) NOT NULL,
    "message" text NOT NULL
);
ALTER TABLE "p57b_people"."upload_file_request"
    OWNER TO p57b_people;

CREATE INDEX upload_file_request_idx_timestamp
    ON "p57b_people"."upload_file_request" ("timestamp");

DROP TABLE IF EXISTS "p57b_people"."requests_files";

CREATE TABLE "p57b_people"."requests_files"
(
    "request_id" text NOT NULL,
    "oid" INTEGER NOT NULL,
    "timestamp" timestamp without time zone DEFAULT now()
);
ALTER TABLE "p57b_people"."requests_files"
    OWNER TO p57b_people;

CREATE INDEX requests_files_idx_timestamp
    ON "p57b_people"."requests_files" ("timestamp");

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
