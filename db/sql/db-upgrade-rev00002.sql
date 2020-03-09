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
-- # MZ: Initial setup for people user roles (Yii2::RBAC)
-- # Role Based Access Control (RBAC)
-- # Migration file from Yii2
-- #############################################################################

drop table if exists "p57b_people"."auth_assignment";
drop table if exists "p57b_people"."auth_item_child";
drop table if exists "p57b_people"."auth_item";
drop table if exists "p57b_people"."auth_rule";

create table "p57b_people"."auth_rule"
(
    "name"  varchar(64) not null,
    "data"  bytea,
    "created_at"           integer,
    "updated_at"           integer,
    primary key ("name")
);

create table "p57b_people"."auth_item"
(
    "name"                 varchar(64) not null,
    "type"                 smallint not null,
    "description"          text,
    "rule_name"            varchar(64),
    "data"                 bytea,
    "created_at"           integer,
    "updated_at"           integer,
    primary key ("name"),
    foreign key ("rule_name") references "p57b_people"."auth_rule" ("name") on delete set null on update cascade
);

create index auth_item_type_idx on "p57b_people"."auth_item" ("type");

create table "p57b_people"."auth_item_child"
(
    "parent"               varchar(64) not null,
    "child"                varchar(64) not null,
    primary key ("parent","child"),
    foreign key ("parent") references "p57b_people"."auth_item" ("name") on delete cascade on update cascade,
    foreign key ("child") references "p57b_people"."auth_item" ("name") on delete cascade on update cascade
);

create table "p57b_people"."auth_assignment"
(
    "item_name"            varchar(64) not null,
    "user_id"              varchar(255) not null,
    "created_at"           integer,
    primary key ("item_name","user_id"),
    foreign key ("item_name") references "p57b_people"."auth_item" ("name") on delete cascade on update cascade
);

create index auth_assignment_user_id_idx on "p57b_people"."auth_assignment" ("user_id");

ALTER TABLE "p57b_people"."auth_assignment" OWNER TO p57b_people;
ALTER TABLE "p57b_people"."auth_item_child" OWNER TO p57b_people;
ALTER TABLE "p57b_people"."auth_item" OWNER TO p57b_people;
ALTER TABLE "p57b_people"."auth_rule" OWNER TO p57b_people;

-- #############################################################################
-- #                               End of file                                 #
-- #############################################################################
