################################################################################
# Set up tables for Project Manager XOOPS module
################################################################################
#PHP based project tracking tool 
#Copyright (c) 2004 by Herman Sheremetyev (herman@swebpage.com)
#IPM (Incyte Project Manager)
#Copyright (c) 2001 by phlux (phlux@udpviper.com)
################################################################################
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
################################################################################


# --------------------------------------------------------
#
# Table structure for table 'project_manager_comments'
#

CREATE TABLE project_manager_comments (
    comment_id INT(10)             NOT NULL AUTO_INCREMENT,
    task_id    INT(10) DEFAULT '0' NOT NULL,
    user       INT(10),
    date       DATETIME,
    comment    TEXT,
    PRIMARY KEY (comment_id)
);


# --------------------------------------------------------
#
# Table structure for table 'project_manager_projects'
#

CREATE TABLE project_manager_projects (
    id             INT(10) NOT NULL AUTO_INCREMENT,
    name           TEXT,
    startdate      DATE,
    enddate        DATE,
    comments       TEXT,
    completed      TINYINT(1) DEFAULT '0',
    completed_date DATE,
    PRIMARY KEY (id)
);


# --------------------------------------------------------
#
# Table structure for table 'project_manager_tasks'
#

CREATE TABLE project_manager_tasks (
    task_id     INT(10)                      NOT NULL AUTO_INCREMENT,
    id          INT(10)       DEFAULT '1'    NOT NULL,
    title       TEXT,
    hours       DECIMAL(4, 2) DEFAULT '1.00' NOT NULL,
    enddate     DATETIME,
    description TEXT,
    status      INT(1),
    person      INT(10)       DEFAULT '0'    NOT NULL,
    billable    TINYINT(1)    DEFAULT '0'    NOT NULL,
    comments    TINYINT(1)    DEFAULT '0'    NOT NULL,
    parent_id   INT(10)       DEFAULT '0'    NOT NULL,
    priority    TINYINT(1)    DEFAULT '0'    NOT NULL,
    PRIMARY KEY (task_id)
);


# --------------------------------------------------------
#
# Table structure for table 'project_manager_users'
#

CREATE TABLE project_manager_users (
    uid   INT(10)     NOT NULL,
    uname VARCHAR(30) NOT NULL,
    name  TEXT        NOT NULL,
    PRIMARY KEY (uid)
);


# --------------------------------------------------------
#
# Table structure for table 'project_manager_user_tasks'
#

CREATE TABLE project_manager_user_tasks (
    id      INT(10) NOT NULL AUTO_INCREMENT,
    uid     INT(10) NOT NULL,
    task_id INT(10) NOT NULL,
    PRIMARY KEY (id)
);

