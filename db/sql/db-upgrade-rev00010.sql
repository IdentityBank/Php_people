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
-- # SR: Create notifications Table
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_people.notifications
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_people"."notifications";

CREATE TABLE "p57b_people"."notifications"
(
  "id" serial PRIMARY KEY,
  "uid" varchar(255) NOT NULL,
  "issued_at" timestamp without time zone DEFAULT now(),
  "expires_at" timestamp without time zone DEFAULT NULL,
  "data" text NOT NULL,
  "type" text NOT NULL,
  "status" smallint DEFAULT 1
);
ALTER TABLE "p57b_people"."notifications"
  OWNER TO p57b_people;

CREATE INDEX notifications_idx_issued_at
  ON "p57b_people"."notifications" ("issued_at");

CREATE INDEX notifications_idx_expires_at
  ON "p57b_people"."notifications" ("expires_at");

CREATE INDEX notifications_idx_status
  ON "p57b_people"."notifications" ("status");

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
