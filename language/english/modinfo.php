<?php

/*-----------------------------------------------------------------------**
    XOOPS Project Manager
    PHP based project tracking tool
    Copyright (c) 2004 by Herman Sheremetyev

    (based on IPM - Incyte Project Manager)
    Copyright (c) 2001 by phlux

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
    **-----------------------------------------------------------------------*/

    // The name of this module
    define('_MI_PROJECT_MANAGER_NAME', 'Project Manager');

    // A brief description of this module
    define('_MI_PROJECT_MANAGER_DESC', 'Port of IPM to XOOPS');
    //define("_MI_PROJECT_MANAGER_SMNAME1", "Foo");

    // Names of admin menu items
    define('_MI_PROJECT_MANAGER_ADMENU1', 'User Admin');
    define('_MI_PROJECT_MANAGER_ADMENU2', 'Project Admin');

    define('_MI_PROJECT_MANAGER_COMPANY', 'Customize your company name');
    //define('_MI_PROJECT_MANAGER_COMPANY_DESC', '');

    // Notification categories
    define('_MI_PROJECT_MANAGER_TASKS_NOTIFY', 'Task Notifications');
    define('_MI_PROJECT_MANAGER_TASKS_NOTIFY_DESC', 'Task Notifications for the Project Manager Module');

    define('_MI_PROJECT_MANAGER_COMMENTS_NOTIFY', 'Comment Notifications');
    define('_MI_PROJECT_MANAGER_COMMENTS_NOTIFY_DESC', 'Comment Notifications for the Project Manager Module');

    // Task assignment events
    define('_MI_PROJECT_MANAGER_NEW_TASK_NOTIFY', 'New Task Notification');
    define('_MI_PROJECT_MANAGER_NEW_TASK_NOTIFYCAP', 'Notify me when a new task is assigned to me');
    define('_MI_PROJECT_MANAGER_NEW_TASK_NOTIFY_DESC', 'A new task has been assigned to you');
    define('_MI_PROJECT_MANAGER_NEW_TASK_NOTIFY_SUBJECT', 'You have been assigned a new task in Project Manager');

    // Comment events
    define('_MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFY', 'New Comment Notification');
    define('_MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFYCAP', 'Notify me when a new comment is added to a task I am assigned to');
    define('_MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFY_DESC', 'A new comment has been added to a task I am assigned to');
    define('_MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFY_SUBJECT', 'A comment has been added to a task you are assigned to in Project Manager');
