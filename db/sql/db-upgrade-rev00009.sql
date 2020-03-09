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
-- # SR: Create Message People Business Table
-- #############################################################################

-- # ---------------------------------------------------------------------- # --
-- # Table: p57b_people.messages_business_people
-- # ---------------------------------------------------------------------- # --

DROP TABLE IF EXISTS "p57b_people"."messages_business_people";


CREATE TABLE p57b_people.messages_business_people
(
  id serial PRIMARY KEY,
  issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP,
  business_user VARCHAR (255) NOT NULL,
  people_user VARCHAR (255) NOT NULL,
  messageContent TEXT NOT NULL
);

ALTER TABLE "p57b_people"."messages_business_people" OWNER TO p57b_people;

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
