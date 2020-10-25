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

    include '../../mainfile.php';
    require XOOPS_ROOT_PATH . '/header.php';

    include 'functions.php';
    include 'common.php';

    extract($_GET);
    extract($_POST);

    // Set error logging
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    set_error_handler('errorHandle');
    error_reporting(0);
    //$xoopsConfig['debug_mode'] = 2;

    //$_XOOPS_project_manager_is_admin = $xoopsUser->isAdmin($xoopsModule->mid());
    //$_XOOPS_project_manager_company = $xoopsModuleConfig['company'];
    $xoopsTpl->assign('is_admin', $xoopsUser->isAdmin());
    $xoopsTpl->assign('image_dir', XOOPS_URL . '/modules/project-manager/images');

    $xoopsTpl->assign('xoops_module_header', '');
    $xoopsTpl->assign('xoops_user', $xoopsUser->uname());

    $message = '';
    // get the general user info
    $result_query = "SELECT uname
                       FROM $_XOOPS_project_manager_users_table
                       WHERE uid='" . $xoopsUser->uid() . "'";
    $result = $xoopsDB->query($result_query);

    if ($userinfo = $xoopsDB->fetchArray($result)) {
        $xoopsTpl->assign('user_info', $userinfo['uname']);

        if (!isset($op)) {
            $op = 'mytasks';
        }

        $task_status = $status ?? 1;

        switch ($op) {
            case 'mytasks':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_mytasks.html';
            myTasks($status);
            break;
            case 'addtask':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_add_task.html';
            addTask($id, $return);
            break;
            case 'edittask':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_edit_task.html';
            editTask($task_id);
            break;
            case 'viewtask':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_view_task.html';
            viewTask($task_id, '');
            break;
            case 'confirmdeletetask':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_delete_task.html';
            confirmDeleteTask($task_id, $project_id);
            break;
            case 'confirmremoveuser':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_remove_user.html';
            confirmRemoveUser($task_id);
            break;
            case 'listprojects':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_list_projects.html';
            listProjects($type);
            break;
            case 'projectdetail':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_project_detail.html';
            projectDetail($id);
            break;
            case 'addcomment':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_add_comment.html';
            addComment($task_id);
            break;
            case 'editcomment':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_edit_comment.html';
            editComment($comment_id, $task_id);
            break;
            case 'deletecomment':
            $GLOBALS['xoopsOption']['template_main'] = 'project_manager_delete_comment.html';
            deleteComment($comment_id, $task_id);
            break;
        }
    } else {
        echo "
        <table class=outer width='100%'>
            <tr>
                <th align=center>
                    This user is not in Project Manager
                </th>
            </tr>
            </tr>
                <td align=center class=head>
                    Site administrator must add this user to Project Manager before you can begin to
                    use this module
                </td>
            </tr>
        </table>";
    }

    require XOOPS_ROOT_PATH . '/footer.php';
