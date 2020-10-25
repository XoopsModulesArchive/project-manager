<?php

/*-----------------------------------------------------------------------**
    XOOPS Project Manager
    PHP based project tracking tool
    Copyright (c) 2004 by Herman Sheremetyev (herman@swebpage.com)

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

    $modversion['name'] = _MI_PROJECT_MANAGER_NAME;
    $modversion['version'] = 0.01;
    $modversion['description'] = _MI_PROJECT_MANAGER_DESC;
    $modversion['credits'] = 'Big thanks IPM developers';
    $modversion['author'] = 'Herman Sheremetyev';
    $modversion['license'] = 'GPL';
    $modversion['official'] = 0;
    $modversion['image'] = 'project_manager_slogo.png';
    $modversion['dirname'] = 'project-manager';

    // SQL stuff
    $modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
    $modversion['tables'][0] = 'project_manager_comments';
    $modversion['tables'][1] = 'project_manager_projects';
    $modversion['tables'][2] = 'project_manager_tasks';
    $modversion['tables'][3] = 'project_manager_users';
    $modversion['tables'][4] = 'project_manager_user_tasks';

    //Admin things
    $modversion['hasAdmin'] = 1;
    $modversion['adminindex'] = 'admin/index.php';
    $modversion['adminmenu'] = 'admin/menu.php';

    // Menu
    $modversion['hasMain'] = 1;
    /*
    $modversion['sub'][1]['name'] = _MI_PROJECT_MANAGER_SMNAME1;
    $modversion['sub'][1]['url'] = "index.php?op=foo";
    */

    // Templates
    $modversion['templates'][1]['file'] = 'project_manager_header.html';
    $modversion['templates'][1]['description'] = '';
    $modversion['templates'][2]['file'] = 'project_manager_mytasks.html';
    $modversion['templates'][2]['description'] = '';
    $modversion['templates'][3]['file'] = 'project_manager_list_projects.html';
    $modversion['templates'][3]['description'] = '';
    $modversion['templates'][4]['file'] = 'project_manager_project_detail.html';
    $modversion['templates'][4]['description'] = '';
    $modversion['templates'][5]['file'] = 'project_manager_view_task.html';
    $modversion['templates'][5]['description'] = '';
    $modversion['templates'][6]['file'] = 'project_manager_edit_task.html';
    $modversion['templates'][6]['description'] = '';
    $modversion['templates'][7]['file'] = 'project_manager_add_task.html';
    $modversion['templates'][7]['description'] = '';
    $modversion['templates'][8]['file'] = 'project_manager_delete_task.html';
    $modversion['templates'][8]['description'] = '';
    $modversion['templates'][9]['file'] = 'project_manager_add_comment.html';
    $modversion['templates'][9]['description'] = '';
    $modversion['templates'][10]['file'] = 'project_manager_edit_comment.html';
    $modversion['templates'][10]['description'] = '';
    $modversion['templates'][11]['file'] = 'project_manager_delete_comment.html';
    $modversion['templates'][11]['description'] = '';
    $modversion['templates'][12]['file'] = 'project_manager_remove_user.html';
    $modversion['templates'][12]['description'] = '';
    $modversion['templates'][13]['file'] = 'project_manager_notauthorized.html';
    $modversion['templates'][13]['description'] = '';

    // Configurable options
    $modversion['config'][1]['name'] = 'company';
    $modversion['config'][1]['title'] = '_MI_PROJECT_MANAGER_COMPANY';
    $modversion['config'][1]['description'] = '_MI_PROJECT_MANAGER_COMPANY_DESC';
    $modversion['config'][1]['formtype'] = 'textbox';
    $modversion['config'][1]['valuetype'] = 'text';
    $modversion['config'][1]['default'] = 'Your Company';

    // Nofitfications
    $modversion['hasNotification'] = 1;

    $modversion['notification']['category'][1]['name'] = 'tasks';
    $modversion['notification']['category'][1]['title'] = _MI_PROJECT_MANAGER_TASKS_NOTIFY;
    $modversion['notification']['category'][1]['description'] = _MI_PROJECT_MANAGER_TASKS_NOTIFY_DESC;
    $modversion['notification']['category'][1]['subscribe_from'] = '*';

    $modversion['notification']['category'][2]['name'] = 'comments';
    $modversion['notification']['category'][2]['title'] = _MI_PROJECT_MANAGER_COMMENTS_NOTIFY;
    $modversion['notification']['category'][2]['description'] = _MI_PROJECT_MANAGER_COMMENTS_NOTIFY_DESC;
    $modversion['notification']['category'][2]['subscribe_from'] = '*';

    $modversion['notification']['event'][1]['name'] = 'new_task';
    $modversion['notification']['event'][1]['category'] = 'tasks';
    $modversion['notification']['event'][1]['title'] = _MI_PROJECT_MANAGER_NEW_TASK_NOTIFY;
    $modversion['notification']['event'][1]['caption'] = _MI_PROJECT_MANAGER_NEW_TASK_NOTIFYCAP;
    $modversion['notification']['event'][1]['description'] = _MI_PROJECT_MANAGER_NEW_TASK_NOTIFY_DESC;
    $modversion['notification']['event'][1]['mail_template'] = 'new_task_notify';
    $modversion['notification']['event'][1]['mail_subject'] = _MI_PROJECT_MANAGER_NEW_TASK_NOTIFY_SUBJECT;

    $modversion['notification']['event'][2]['name'] = 'new_comment';
    $modversion['notification']['event'][2]['category'] = 'comments';
    $modversion['notification']['event'][2]['title'] = _MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFY;
    $modversion['notification']['event'][2]['caption'] = _MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFYCAP;
    $modversion['notification']['event'][2]['description'] = _MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFY_DESC;
    $modversion['notification']['event'][2]['mail_template'] = 'new_comment_notify';
    $modversion['notification']['event'][2]['mail_subject'] = _MI_PROJECT_MANAGER_NEW_COMMENT_NOTIFY_SUBJECT;
