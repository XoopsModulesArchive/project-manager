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

    // Function definitions

    function myTasks($status)
    {
        // This function gives a comprehensive summary for all of a user's

        // projects and tasks. All tasks assigned to the logged in user are

        // displayed along with project details. This is the default function

        global $finish, $reset;

        global $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_user_tasks_table;

        global $_XOOPS_project_manager_users_table, $_XOOPS_project_manager_projects_table;

        global $xoopsDB, $xoopsUser, $xoopsTpl;

        if (isset($finish)) {
            finishTask($finish);
        }

        if (isset($reset)) {
            resetTask($reset);
        }

        $task_status = 1;

        if (isset($status) && 0 == $status) {
            $task_status = $status;
        }

        $xoopsTpl->assign('task_status', $task_status);

        // get ammount of projects the user is assigned to

        $p_result_query = "SELECT $_XOOPS_project_manager_tasks_table.id 
                            FROM $_XOOPS_project_manager_tasks_table 
                            LEFT JOIN $_XOOPS_project_manager_user_tasks_table 
                            ON $_XOOPS_project_manager_tasks_table.task_id = $_XOOPS_project_manager_user_tasks_table.task_id 
                            WHERE uid='" . $xoopsUser->uid() . "' AND status='$task_status' 
                            GROUP BY $_XOOPS_project_manager_tasks_table.id";

        $p_result = $xoopsDB->query($p_result_query);

        $project_total = $xoopsDB->getRowsNum($p_result);

        $xoopsTpl->assign('project_total', $project_total);

        // get ammount of open tasks the user is assigned to

        $query = "SELECT $_XOOPS_project_manager_tasks_table.id 
                    FROM $_XOOPS_project_manager_tasks_table 
                    LEFT JOIN $_XOOPS_project_manager_user_tasks_table 
                    ON $_XOOPS_project_manager_tasks_table.task_id = $_XOOPS_project_manager_user_tasks_table.task_id 
                    WHERE uid='" . $xoopsUser->uid() . "' AND status='1'";

        $t_result = $xoopsDB->query($query);

        $task_total = $xoopsDB->getRowsNum($t_result);

        $xoopsTpl->assign('task_total', $task_total);

        $result2 = $xoopsDB->query($p_result_query);

        while (false !== ($open = $xoopsDB->fetchArray($result2))) {
            // grab all info for the open projects

            $result3_query = "SELECT id,name 
                                FROM $_XOOPS_project_manager_projects_table
                                WHERE id='$open[id]'";

            $result3 = $xoopsDB->query($result3_query);

            while (false !== ($project = $xoopsDB->fetchArray($result3))) {
                $xoopsTpl->append('projects', $project);
            }
        }
    } //end mytasks

    function insert_userTaskList($args)
    {
        global $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_user_tasks_table;

        global $_XOOPS_project_manager_image_dir, $_XOOPS_project_manager_is_admin;

        global $xoopsDB, $xoopsUser;

        $project_id = $args['project_id'];

        $status = 1;

        if (isset($args['status'])) {
            if (0 == $args['status']) {
                $status = $args['status'];
            }
        }

        $image_dir = $_XOOPS_project_manager_image_dir;

        $is_admin = $_XOOPS_project_manager_is_admin;

        $query = "SELECT $_XOOPS_project_manager_tasks_table.*
                    FROM $_XOOPS_project_manager_tasks_table
                    LEFT JOIN $_XOOPS_project_manager_user_tasks_table
                    ON $_XOOPS_project_manager_tasks_table.task_id = $_XOOPS_project_manager_user_tasks_table.task_id
                    WHERE uid='" . $xoopsUser->uid() . "' AND status='$status'
                        AND $_XOOPS_project_manager_tasks_table.id='$project_id'
                    ORDER BY 'priority' DESC";

        $result = $xoopsDB->query($query);

        while (false !== ($task = $xoopsDB->fetchArray($result))) {
            echo '<tr class=odd valign=top>
                <td align=center>';

            if (0 == $task['billable']) {
                echo (string)$task[hours];
            } else {
                echo "<font class=billable>$task[hours]</font>";
            }

            echo '</td>
                <td align=center>';

            echo "<a href='index.php?op=addcomment&task_id=$task[task_id]'>";

            if ($task['comments'] > 0) {
                echo "<img src='$image_dir/comment-on.gif' border=0 title=\"Task has comments\" alt=\"Task has comments\">";
            } else {
                echo "<img src='$image_dir/comment-off.gif' border=0 title=\"Task has no comments\" alt=\"Task has no comments\">";
            }

            echo '</a>';

            echo '</td>
                <td>';

            if ($task['priority'] > 6) {
                $class = 'listlink-high';
            }

            if ($task['priority'] <= 6) {
                $class = 'listlink-med';
            }

            if ($task['priority'] <= 3) {
                $class = 'listlink-low';
            }

            echo "<a href='index.php?op=viewtask&task_id=$task[task_id]' class='$class'>$task[title]</a>
                </td>
                <td>
                    $task[description] &nbsp;
                </td>
                <td align=center nowrap>";

            if ($status) {
                echo "<a href='index.php?op=edittask&task_id=$task[task_id]'>
                            <img src='$image_dir/e-on.gif' border=0 title=\"Edit Task\" alt=\"Edit Task\">
                        </a>
                        <a href='index.php?finish=$task[task_id]'>
                            <img src='$image_dir/f-on.gif' border=0 title=\"Finish Task\" alt=\"Finish Task\">
                        </a>
                        <a href='index.php?op=confirmremoveuser&task_id=$task[task_id]'>
                            <img src='$image_dir/r-on.gif' border=0 title=\"Remove Yourself from Task\" alt=\"Remove Yourself from Task\">
                        </a>";
            } else {
                echo "<a href='index.php?util_op=mytasks&reset=$task[task_id]'>[REACTIVATE]</a>";
            }

            echo '</td>
            </tr>';
        }
    }

    function insert_taskList($args)
    {
        // print the nice indented list

        global $xoopsDB;

        global $_XOOPS_project_manager_image_dir;

        global $_XOOPS_project_manager_is_admin;

        global $_XOOPS_project_manager_users_table;

        global $_XOOPS_project_manager_user_tasks_table;

        global $_XOOPS_project_manager_tasks_table;

        $id = $args['id'];

        $status = $args['status'];

        $order = $args['order'];

        $table1 = $_XOOPS_project_manager_users_table;

        $table2 = $_XOOPS_project_manager_user_tasks_table;

        $query = "SELECT * FROM $_XOOPS_project_manager_tasks_table WHERE id='$id' ORDER BY $order";

        $result = $xoopsDB->query($query);

        while (false !== ($task = $xoopsDB->fetchArray($result))) {
            if (isTopLevel($task['task_id'])) {
                if (getDescendents($task['task_id'], $status) || $task['status'] == $status) {
                    echo '<tr valign=top class=todolist>
                            <td>';

                    if ($task['status'] == (string)$status) {
                        if ($task['priority'] >= 7) {
                            $class = 'listlink-high';
                        }

                        if ($task['priority'] <= 6) {
                            $class = 'listlink-med';
                        }

                        if ($task['priority'] <= 3) {
                            $class = 'listlink-low';
                        }
                    } else {
                        $class = 'strike-through';
                    }

                    insert_popupLoader(
                        [
                            'popup_id' => (string)$task[task_id],
                            'description' => (string)$task[description],
                            'linkname' => (string)$task[title],
                            'link' => "index.php?op=viewtask&task_id=$task[task_id]",
                            'class' => (string)$class,
                                    ]
                    );

                    echo '</td>
                            <td>';

                    $query2 = "SELECT uname FROM $table1 LEFT JOIN $table2 ON $table1.uid = $table2.uid WHERE task_id='$task[task_id]'";

                    $result2 = $xoopsDB->query($query2);

                    while (false !== ($user = $xoopsDB->fetchArray($result2))) {
                        echo "$user[uname]<br>";
                    }

                    echo '</td>
                            <td>';

                    if ($task['priority'] <= 3) {
                        echo 'Low';
                    }

                    if ($task['priority'] > 3 && $task['priority'] <= 6) {
                        echo 'Med';
                    }

                    if ($task['priority'] > 6) {
                        echo 'High';
                    }

                    echo '</td>
                            <td align=center>';

                    echo "<a href='index.php?op=addcomment&task_id=$task[task_id]'>";

                    if ($task['comments']) {
                        echo "<img src=$_XOOPS_project_manager_image_dir/comment-on.gif border=0 "
                                        . 'title="Task has comments" alt="Task has comments">';
                    } else {
                        echo "<img src=$_XOOPS_project_manager_image_dir/comment-off.gif border=0 "
                                        . 'title="Task has no comments" alt="Task has no comments">';
                    }

                    echo '</a>';

                    echo '</td>
                            <td align=center>';

                    if (!$task['billable']) {
                        echo $task['hours'];
                    } else {
                        echo "<font class=billable>$task[hours]</font>";
                    }

                    echo '</td>
                            <td align=center>';

                    if ($task['status'] == (string)$status) {
                        if (1 == $status) {
                            if (getChildren($task['task_id'], 1)) {
                                if ($_XOOPS_project_manager_is_admin) {
                                    echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/d-off.gif border=0></a> ";
                                }

                                echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/f-off.gif border=0></a>";
                            } else {
                                if ($_XOOPS_project_manager_is_admin) {
                                    echo "<a href=index.php?op=confirmdeletetask&task_id=$task[task_id]&project_id=$id>
                                                    <img src=$_XOOPS_project_manager_image_dir/d-on.gif 
                                                        title=\"Delete Task\" alt=\"Delete Task\" border=0></a> ";
                                }

                                echo "<a href=index.php?op=projectdetail&finish=$task[task_id]&id=$id>
                                                <img src=$_XOOPS_project_manager_image_dir/f-on.gif 
                                                        title=\"Finish Task\" alt=\"Finish Task\" border=0></a>";
                            }
                        } else {
                            if (getChildren($task['task_id'], 0)) {
                                if ($_XOOPS_project_manager_is_admin) {
                                    echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/d-off.gif border=0></a> ";
                                }

                                echo "<a href=index.php?op=projectdetail&reset=$task[task_id]&id=$id>
                                                <img src=$_XOOPS_project_manager_image_dir/r-on.gif 
                                                    title=\"Reactivate Task\" alt=\"Reactivate Task\" border=0></a>";
                            } else {
                                if ($_XOOPS_project_manager_is_admin) {
                                    echo "<a href=index.php?op=confirmdeletetask&task_id=$task[task_id]&project_id=$id>
                                                    <img src=$_XOOPS_project_manager_image_dir/d-on.gif 
                                                        title=\"Delete Task\" alt=\"Delete Task\" border=0></a> ";
                                }

                                echo "<a href=index.php?op=projectdetail&reset=$task[task_id]&id=$id>
                                                <img src=$_XOOPS_project_manager_image_dir/r-on.gif 
                                                        title=\"Reactive Task\" alt=\"Reactivate Task\" border=0></a>";
                            }
                        }
                    } else {
                        if ($_XOOPS_project_manager_is_admin) {
                            echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/d-off.gif border=0></a> ";
                        }

                        echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/f-off.gif border=0></a>";
                    }

                    echo '</td>
                    </tr>';
                } //if

                printTree($task['task_id'], 0, $status);
            } // if top level
        } // while
    } //end taskList

    function printTree($task_id, $count, $status)
    {
        global $id, $_XOOPS_project_manager_image_dir, $_XOOPS_project_manager_is_admin;

        global $xoopsDB, $xoopsUser;

        global $_XOOPS_project_manager_users_table;

        global $_XOOPS_project_manager_user_tasks_table;

        $count++;

        $indent = $count * 10;

        $result = getChildren($task_id, 2);

        for ($x = 0, $xMax = count($result); $x < $xMax; $x++) {
            $task = getTaskInfo($result[$x][0]);

            $table1 = $_XOOPS_project_manager_users_table;

            $table2 = $_XOOPS_project_manager_user_tasks_table;

            if ($task['status'] == $status || getDescendents($task['task_id'], $status)) {
                echo "<tr valign=top class=todolist>
                        <td>
                            <img src=$_XOOPS_project_manager_image_dir/spacer.gif border=0 height=10 width=$indent alt=\"\">";

                if ($task['status'] == (string)$status) {
                    if ($task['priority'] >= 7) {
                        $class = 'listlink-high';
                    }

                    if ($task['priority'] <= 6) {
                        $class = 'listlink-med';
                    }

                    if ($task['priority'] <= 3) {
                        $class = 'listlink-low';
                    }
                } else {
                    $class = 'strike-through';
                }

                insert_popupLoader(
                    [
                        'popup_id' => (string)$task[task_id],
                        'description' => (string)$task[description],
                        'linkname' => (string)$task[title],
                        'link' => "index.php?op=viewtask&task_id=$task[task_id]",
                        'class' => (string)$class,
                                ]
                );

                echo '</td>
                        <td>';

                $query2 = "SELECT uname FROM $table1 LEFT JOIN $table2 ON $table1.uid = $table2.uid WHERE task_id='$task[task_id]'";

                $result2 = $xoopsDB->query($query2);

                while (false !== ($todouser = $xoopsDB->fetchRow($result2))) {
                    echo "$todouser[0]<br>";
                }

                echo '</td>
                        <td>';

                if ($task['priority'] <= 3) {
                    echo 'Low';
                }

                if ($task['priority'] > 3 && $task['priority'] <= 6) {
                    echo 'Med';
                }

                if ($task['priority'] > 6) {
                    echo 'High';
                }

                echo '</td>
                        <td align=center>';

                echo "<a href='index.php?op=addcomment&task_id=$task[task_id]'>";

                if ($task['comments']) {
                    echo "<img src=$_XOOPS_project_manager_image_dir/comment-on.gif border=0 title=\"Task has comments\" alt=\"Task has comments\">";
                } else {
                    echo "<img src=$_XOOPS_project_manager_image_dir/comment-off.gif border=0 title=\"Task has no comments\" alt=\"Task has no comments\">";
                }

                echo '</a>';

                echo '</td>
                        <td align=center>';

                if (!$task['billable']) {
                    echo $task['hours'];
                } else {
                    echo "<font class=billable>$task[hours]</font>";
                }

                echo '</td>
                        <td align=center>';

                if ($task['status'] == (string)$status) {
                    if (1 == $status) {
                        if (getChildren($task['task_id'], 1)) {
                            if ($_XOOPS_project_manager_is_admin) {
                                echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/d-off.gif border=0 alt=\"\"></a> ";
                            }

                            echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/f-off.gif border=0 alt=\"\"></a>";
                        } else {
                            if ($_XOOPS_project_manager_is_admin) {
                                echo "<a href=index.php?op=confirmdeletetask&task_id=$task[task_id]&project_id=$id>
                                                <img src=$_XOOPS_project_manager_image_dir/d-on.gif border=0 title=\"Delete Task\" alt=\"Delete Task\">
                                                </a>";
                            }

                            echo "<a href=index.php?op=projectdetail&finish=$task[task_id]&id=$id>
                                            <img src=$_XOOPS_project_manager_image_dir/f-on.gif border=0 title=\"Finish Task\" alt=\"Finish Task\">
                                            </a>";
                        }
                    } else {
                        if (getChildren($task['task_id'], 0)) {
                            if ($_XOOPS_project_manager_is_admin) {
                                echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/d-off.gif border=0 alt=\"\"></a> ";
                            }

                            echo "<a href=index.php?op=projectdetail&reset=$task[task_id]&id=$id>
                                            <img src=$_XOOPS_project_manager_image_dir/r-on.gif border=0 alt=\"\"></a>";
                        } else {
                            if ($_XOOPS_project_manager_is_admin) {
                                echo "<a href=index.php?op=confirmdeletetask&task_id=$task[task_id]&project_id=$id>
                                                <img src=$_XOOPS_project_manager_image_dir/d-on.gif border=0 title=\"Delete Task\" alt=\"Delete Task\">
                                                </a>";
                            }

                            echo "<a href=index.php?op=projectdetail&reset=$task[task_id]&id=$id>
                                            <img src=$_XOOPS_project_manager_image_dir/r-on.gif border=0 title=\"Reactivate Task\" alt=\"Reactivate Task\">
                                            </a>";
                        }
                    }
                } else {
                    if ($_XOOPS_project_manager_is_admin) {
                        echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/d-off.gif border=0 alt=\"\"></a> ";
                    }

                    echo "<a href=#><img src=$_XOOPS_project_manager_image_dir/f-off.gif border=0 alt=\"\"></a>";
                }

                echo '</td>
                </tr>';

                printTree($result[$x][0], $count, $status);
            } //if
        } //for
    } // end printTree

    function taskDropdown($project_id)
    {
        // print the nice indented list

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $query = "SELECT task_id,title FROM $_XOOPS_project_manager_tasks_table WHERE id='$project_id'";

        $result = $xoopsDB->query($query);

        $task_options = '';

        while (false !== ($task = $xoopsDB->fetchArray($result))) {
            if (isTopLevel($task['task_id'])) {
                $task_options .= "<option value=$task[task_id]>$task[title]</option>\n";

                $task_options .= printTaskDropdown($task['task_id'], 0);
            }
        }

        return $task_options;
    }

    function printTaskDropdown($task_id, $count)
    {
        global $id;

        $count++;

        $indent = $count * 3;

        $spacing = '';

        for ($x = 0; $x < $indent; $x++) {
            $spacing .= '&nbsp;';
        }

        $result = getChildren($task_id, 2);

        $task_options = '';

        for ($x = 0, $xMax = count($result); $x < $xMax; $x++) {
            $task = getTaskInfo($result[$x][0]);

            $task_options .= "<option value=$task[task_id]>$spacing $task[title]</option>";

            $task_options .= printTaskDropdown($result[$x][0], $count);
        }

        return $task_options;
    }

    function getChildren($task_id, $status)
    {
        // returns the task_id's of all child tasks that are status=$status

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $count = 0;

        $table = $_XOOPS_project_manager_tasks_table;

        if ((0 == $status) || (1 == $status)) {
            $query = "SELECT task_id FROM $table WHERE parent_id='$task_id' AND status='$status'";
        } else {
            $query = "SELECT task_id FROM $table WHERE parent_id='$task_id'";
        }

        $result = $xoopsDB->query($query);

        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $children[$count][0] = $row['task_id'];

            $count++;
        }

        return $children ?? null;
    }

    function getDescendents($task_id, $status, $count = 0)
    {
        $result = getChildren($task_id, 2);

        for ($x = 0, $xMax = count($result); $x < $xMax; $x++) {
            $task = getTaskInfo($result[$x][0]);

            if (getChildren($task['task_id'], $status) || $status == $task['status']) {
                $descendents[$x][0] = $result[$x][0];
            }

            getDescendents($result[$x][0], $count, $status);
        }

        return $descendents ?? null;
    }

    function getTaskInfo($task_id)
    {
        // returns the data concerning a task

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $query = "SELECT * FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'";

        $result = $xoopsDB->query($query);

        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $info['task_id'] = $task_id;

            $info['title'] = stripslashes($row['title']);

            $info['hours'] = $row['hours'];

            $info['description'] = stripslashes($row['description']);

            $info['person'] = stripslashes($row['person']);

            $info['priority'] = $row['priority'];

            $info['status'] = $row['status'];

            $info['billable'] = $row['billable'];

            $info['comments'] = stripslashes($row['comments']);

            $info['parent_id'] = $row['parent_id'];
        }

        return $info;
    }

    function isTopLevel($task_id)
    {
        // returns 1 if the task is at the top level

        // returns null if the task is a subtask

        global $xoopsDB, $_XOOPS_project_manager_tasks_table;

        $query = "SELECT parent_id FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'";

        $result = $xoopsDB->query($query);

        while (false !== ($parent = $xoopsDB->fetchArray($result))) {
            if (0 == $parent['parent_id']) {
                return 1;
            }

            return;
        }
    }

    function getParent($task_id)
    {
        // returns task_id of parent task if it exists

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        if (isTopLevel($task_id)) {
            return;
        }

        $query = "SELECT parent_id FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'";

        $result = $xoopsDB->query($query);

        if ($parent = $xoopsDB->fetchArray($result)) {
            return $parent['parent_id'];
        }
    }

    function getTaskUsers($task_id)
    {
        global $xoopsDB;

        global $_XOOPS_project_manager_user_tasks_table;

        $users = [];

        $i = 0;

        $query = "SELECT uid FROM $_XOOPS_project_manager_user_tasks_table WHERE task_id='$task_id'";

        $result = $xoopsDB->query($query);

        while (false !== ($list = $xoopsDB->fetchRow($result))) {
            $users[$i] = $list['uid'];
        }

        return $users;
    }

    function listProjects($type)
    {
        // This function goes to the database and generates a list of current projects.

        // It also builds a menu for editing/deleting/completing projects.

        //dbconnect();

        global $order, $_XOOPS_project_manager_projects_table;

        global $xoopsDB, $xoopsTpl;

        $table = $_XOOPS_project_manager_projects_table;

        switch ($order) {
            case 'nameup':
            $myorder = 'name ASC';
            break;
            case 'namedown':
            $myorder = 'name DESC';
            break;
            case 'datedown':
            $myorder = 'enddate ASC';
            break;
            case 'dateup':
            $myorder = 'enddate DESC';
            break;
            default:
            $myorder = 'enddate ASC';
            break;
        }

        $completed = 0;

        if ('completed' == (string)$type) {
            $completed = 1;
        }

        $xoopsTpl->assign('list_projects_type', $completed);

        $list_query = "SELECT id, name, enddate, comments, completed_date FROM $table WHERE completed='$completed' ORDER BY $myorder";

        $listresult = $xoopsDB->query($list_query);

        while (false !== ($list_row = $xoopsDB->fetchArray($listresult))) {
            $xoopsTpl->append('list_row', $list_row);
        }
    } //end listprojects

    function projectDetail($id)
    {
        // This function displays everyting you need to see about a project

        // It shows the startdate, deadline, todo list, finished list, total hours for todo and finished,

        // and it calculates the percentage of the project completed. It also gives the option to

        // add/edit/delete tasks, edit/delete/complete the project, update list items, and view item descriptions.

        global $ordertodo, $orderdone, $finish, $reset;

        global $_XOOPS_project_manager_projects_table;

        global $xoopsDB, $xoopsTpl;

        if (isset($finish)) {
            finishTask($finish);
        }

        if (isset($reset)) {
            resetTask($reset);
        }

        $xoopsTpl->assign('ordertodo', $ordertodo);

        $xoopsTpl->assign('orderdone', $orderdone);

        $query = "SELECT * FROM $_XOOPS_project_manager_projects_table WHERE id=$id";

        $result = $xoopsDB->query($query);

        while (false !== ($list_row = $xoopsDB->fetchArray($result))) {
            $xoopsTpl->append('project_detail_row', $list_row);

            switch ($ordertodo) {
                case 'nameup':
                $todo_order = 'title asc';
                break;
                case 'namedown':
                $todo_order = 'title desc';
                break;
                case 'commentsup':
                $todo_order = 'comments asc';
                break;
                case 'commentsdown':
                $todo_order = 'comments desc';
                break;
                case 'personup':
                $todo_order = 'person asc';
                break;
                case 'persondown':
                $todo_order = 'person desc';
                break;
                case 'priorityup':
                $todo_order = 'priority asc';
                break;
                case 'prioritydown':
                $todo_order = 'priority desc';
                break;
                case 'hoursup':
                $todo_order = 'hours asc';
                break;
                case 'hoursdown':
                $todo_order = 'hours desc';
                break;
                default:
                $todo_order = 'person asc';
            }

            switch ($orderdone) {
                case 'nameup':
                $done_order = 'title asc';
                break;
                case 'namedown':
                $done_order = 'title desc';
                break;
                case 'commentsup':
                $done_order = 'comments asc';
                break;
                case 'commentsdown':
                $done_order = 'comments desc';
                break;
                case 'personup':
                $done_order = 'person asc';
                break;
                case 'persondown':
                $done_order = 'person desc';
                break;
                case 'priorityup':
                $done_order = 'priority asc';
                break;
                case 'prioritydown':
                $done_order = 'priority desc';
                break;
                case 'hoursup':
                $done_order = 'hours asc';
                break;
                case 'hoursdown':
                $done_order = 'hours desc';
                break;
                default:
                $done_order = 'person asc';
            }
        }
    } //end projectdetail

    function completeProject($id)
    {
        // This function makes the current project complete, taking it off of the

        // main active project list.

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        $completed_date = date('Y-m-d');

        $query = "UPDATE $_XOOPS_project_manager_projects_table SET completed='1', completed_date='$completed_date' WHERE id='$id'";

        $result = $xoopsDB->queryF($query);

        if ($result) {
            return 'Project Completed Successfully';
        }

        return 'Error Completing Project';
        //listProjects("completed");
    }

    function reactivateProject($id)
    {
        // This function reactivates a project, taking it off of the

        // completed project list.

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        $result1 = $xoopsDB->queryF("UPDATE $_XOOPS_project_manager_projects_table SET completed='0' WHERE id='$id'");

        $result2 = $xoopsDB->queryF("UPDATE $_XOOPS_project_manager_projects_table SET completed_date='0' WHERE id='$id'");

        if ($result1 && $result2) {
            return 'Project Reactivated Successfully';
        }

        return 'Error Reactivating Project';
    }

    function viewTask($task_id, $message)
    {
        // This function build the menu for viewing a task in a project.

        global $xoopsDB, $xoopsTpl, $xoopsUser;

        global $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_projects_table;

        global $_XOOPS_project_manager_user_tasks_table, $_XOOPS_project_manager_users_table;

        $query = "SELECT * FROM $_XOOPS_project_manager_tasks_table WHERE task_id=$task_id";

        $result = $xoopsDB->query($query);

        if ($view = $xoopsDB->fetchArray($result)) {
            $xoopsTpl->assign('view_task_row', $view);

            if ($view['person'] == $xoopsUser->uid()) {
                $xoopsTpl->assign('view_task_is_owner', 'yes');
            }

            $query = "SELECT name FROM $_XOOPS_project_manager_projects_table WHERE id=$view[id]";

            $result = $xoopsDB->query($query);

            $list = $xoopsDB->fetchArray($result);

            $xoopsTpl->assign('view_task_project_name', $list['name']);

            $table1 = $_XOOPS_project_manager_users_table;

            $table2 = $_XOOPS_project_manager_user_tasks_table;

            $query = "SELECT * FROM $table1 LEFT JOIN $table2 ON $table1.uid = $table2.uid WHERE task_id = '$task_id'";

            $result = $xoopsDB->query($query);

            while (false !== ($user = $xoopsDB->fetchArray($result))) {
                $xoopsTpl->append('view_task_users', $user);
            }

            $result2 = $xoopsDB->query("SELECT uname,name FROM $_XOOPS_project_manager_users_table WHERE uid='$view[person]'");

            if ($user = $xoopsDB->fetchArray($result2)) {
                $xoopsTpl->assign('view_task_owner', $user);
            }

            if ($parent_task = getParent($task_id)) {
                $parent = getTaskInfo($parent_task);

                $xoopsTpl->assign('view_task_parent', $parent['title']);
            } else {
                $xoopsTpl->assign('view_task_parent', 'TOP LEVEL (none)');
            }

            $xoopsTpl->assign('view_task_comments', displayComments($task_id));
        } // end if
    } // end viewtask

    function addTask($id, $return)
    {
        // This function builds the form for adding a task to a project.

        // When this form is submitted the variables are passed to the function

        // "addTaskAction()"

        global $xoopsDB, $xoopsUser, $xoopsTpl;

        global $_XOOPS_project_manager_projects_table;

        global $_XOOPS_project_manager_users_table;

        global $_XOOPS_project_manager_user_tasks_table;

        $query = "SELECT * FROM $_XOOPS_project_manager_projects_table WHERE id=$id";

        $result = $xoopsDB->query($query);

        $add = $xoopsDB->fetchArray($result);

        $query2 = "SELECT uid,uname FROM $_XOOPS_project_manager_users_table";

        $result2 = $xoopsDB->query($query2);

        $query3 = "SELECT task_id, title FROM $_XOOPS_project_manager_tasks_table WHERE id=$id";

        $result3 = $xoopsDB->query($query3);

        $xoopsTpl->assign('add_task_project_id', $id);

        $xoopsTpl->assign('add_task_name', $add['name']);

        $xoopsTpl->assign('add_task_return', $return);

        $xoopsTpl->assign('xoops_uid', $xoopsUser->uid());

        $user_dropdown = '';

        while (false !== ($users = $xoopsDB->fetchArray($result2))) {
            $user_dropdown .= "<option value=$users[uid]>$users[uid]-$users[uname]</option>";
        }

        $add_task_dropdown = '<option value="0" selected>TOP LEVEL (none)</option>';

        $add_task_dropdown .= taskDropdown($id);

        $xoopsTpl->assign('add_task_parent_dropdown', $add_task_dropdown);

        $xoopsTpl->assign('add_task_users', $user_dropdown);
    }

    function addTaskAction($id, $title, $hours, $priority, $description, $creator, $person, $notify, $billable, $parent_id)
    {
        // This function takes the results from the function "addtask()"

        // and inserts them into the database, then displays the results

        global $xoopsDB, $xoopsModule;

        global $_XOOPS_project_manager_company;

        global $_XOOPS_project_manager_projects_table, $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_user_tasks_table;

        if ('' == (string)$title) {
            $title = '<No name given>';
        }

        if ('' == (string)$description) {
            $description = '<No description given>';
        }

        $tags['TASK_NAME'] = $title;

        $tags['DESCRIPTION'] = $description;

        $tags['COMPANY'] = $_XOOPS_project_manager_company;

        // Get name of project for the notification email

        $project = $xoopsDB->fetchArray($xoopsDB->query("SELECT name FROM $_XOOPS_project_manager_projects_table WHERE id='$id'"));

        $tags['PROJECT_NAME'] = $project['name'];

        //$description =  str_replace("\"", "&quot;", $description);

        //$title =  str_replace("\"", "&quot;", $title);

        $description = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5);

        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        // Add the task to the tasks table

        if (!$xoopsDB->queryF("INSERT INTO $_XOOPS_project_manager_tasks_table 
                            VALUES (NULL,'$id','$title','$hours','0','$description','1','$creator','$billable','0','$parent_id','$priority')")) {
            return 'Error inserting task';
        }

        // Get the task id of the new task

        $query = "SELECT max(task_id) FROM $_XOOPS_project_manager_tasks_table";

        if (!$result = $xoopsDB->fetchRow($xoopsDB->query($query))) {
            return 'Error getting task_id';
        }

        $task_id = $result[0];

        $error = '';

        $notificationHandler = xoops_getHandler('notification');

        // Create an entry in the user_tasks table mapping each user to the task

        foreach ($person as $user) {
            if (0 == $user) {
                continue;
            } // "None" was selected

            if (!$xoopsDB->queryF("INSERT INTO $_XOOPS_project_manager_user_tasks_table VALUES (NULL, '$user', '$task_id')")) {
                $error .= "Error updating user_tasks for uid $user";

                continue;
            }

            $user_list = [$user];

            if ($notify) {
                $notificationHandler->triggerEvent('tasks', 0, 'new_task', $tags, $user_list);
            }
        }

        if ('' == $error) {
            return 'Task Added Successfully';
        }

        return "Task Add Errors:\n" . $error;
    }

    function editTask($task_id)
    {
        // This function build the menu for editing a pre-existing task in a project.

        // When submitted, the variables are passed to the function "editTaskAction()".

        global $xoopsDB, $xoopsTpl, $xoopsUser;

        global $_XOOPS_project_manager_is_admin;

        global $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_projects_table;

        global $_XOOPS_project_manager_users_table, $_XOOPS_project_manager_user_tasks_table;

        $auth_user = $xoopsDB->fetchArray($xoopsDB->query("SELECT person FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'"));

        if ($_XOOPS_project_manager_is_admin || $auth_user['person'] == $xoopsUser->uid()) {
            $xoopsTpl->assign('edit_task_authorized', 'yes');
        } else {
            return;
        }

        $query = "SELECT * FROM $_XOOPS_project_manager_tasks_table where task_id=$task_id";

        $result = $xoopsDB->query($query);

        if ($edit = $xoopsDB->fetchArray($result)) {
            $xoopsTpl->assign('edit_task_row', $edit);

            $query2 = "SELECT name FROM $_XOOPS_project_manager_projects_table where id=$edit[id]";

            $result2 = $xoopsDB->query($query2);

            $list = $xoopsDB->fetchArray($result2);

            $table1 = $_XOOPS_project_manager_users_table;

            $table2 = $_XOOPS_project_manager_user_tasks_table;

            $query3 = "SELECT $table1.uid FROM $table1 LEFT JOIN $table2 ON $table1.uid = $table2.uid WHERE task_id=$task_id";

            $result3 = $xoopsDB->query($query3);

            $query4 = "SELECT uid,uname FROM $_XOOPS_project_manager_users_table";

            $result4 = $xoopsDB->query($query4);

            $xoopsTpl->assign('edit_task_project_name', $list['name']);

            // Make an array of "selected" users

            $selectedusers = [];

            $is_none = 1;

            while (false !== ($susers = $xoopsDB->fetchArray($result3))) {
                $index = $susers['uid'];

                $selectedusers[$index] = 1;

                $is_none = 0;
            }

            $edit_task_user_options = '<option value=0';

            if ($is_none) {
                $edit_task_user_options .= ' selected';
            }

            $edit_task_user_options .= '>None</option>';

            while (false !== ($users = $xoopsDB->fetchArray($result4))) {
                $edit_task_user_options .= "<option value=$users[uid]";

                // if in user_tasks add a "selected" here

                $index = $users['uid'];

                if (isset($selectedusers[$index]) && $selectedusers[$index]) {
                    $edit_task_user_options .= ' selected';
                }

                $edit_task_user_options .= ">$users[uid]-$users[uname]</option>";
            }

            $xoopsTpl->assign('edit_task_user_options', $edit_task_user_options);

            if (1 == $edit['billable']) {
                $edit_task_billable = '<input type=checkbox checked name=billable value=1> Billable?';
            } else {
                $edit_task_billable = '<input type=checkbox name=billable value=1> Billable?';
            }

            $xoopsTpl->assign('edit_task_billable', $edit_task_billable);

            $edit_task_priority_options = '<option value=1';

            if ($edit['priority'] <= 3) {
                $edit_task_priority_options .= ' selected';
            }

            $edit_task_priority_options .= '>Low</option>';

            $edit_task_priority_options .= '<option value=5';

            if ($edit['priority'] > 3 && $edit['priority'] <= 6) {
                $edit_task_priority_options .= ' selected';
            }

            $edit_task_priority_options .= '>Medium</option>';

            $edit_task_priority_options .= '<option value=10';

            if ($edit['priority'] > 6) {
                $edit_task_priority_options .= ' selected';
            }

            $edit_task_priority_options .= '>High</option>';

            $xoopsTpl->assign('edit_task_priority_options', $edit_task_priority_options);

            if ($parent_task = getParent($task_id)) {
                $parent = getTaskInfo($parent_task);

                $edit_task_parent = "<option value=$parent[task_id] selected>$parent[title]</option>
                   <option value=\"0\">TOP LEVEL (none)</option>";
            } else {
                $edit_task_parent = '<option value="0" selected>TOP LEVEL (none)</option>';
            }

            $xoopsTpl->assign('edit_task_parent', $edit_task_parent);

            $xoopsTpl->assign('edit_task_dropdown', taskDropdown($edit['id']));
        } // end if
    } // end edittask

    function editTaskAction($task_id, $id, $title, $hours, $priority, $description, $person, $billable, $parent_id)
    {
        // This function takes the variables from editTask() and makes the changes

        // to the database where neccessary, then displays the results.

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_user_tasks_table, $_XOOPS_project_manager_projects_table;

        global $_XOOPS_project_manager_company;

        if ('' == (string)$title) {
            $title = '<No title given>';
        }

        if ('' == (string)$description) {
            $description = '<No description given>';
        }

        $tags['TASK_NAME'] = $title;

        $tags['DESCRIPTION'] = $description;

        $tags['COMPANY'] = $_XOOPS_project_manager_company;

        // Get name of project for the notification email

        $project = $xoopsDB->fetchArray($xoopsDB->query("SELECT name FROM $_XOOPS_project_manager_projects_table WHERE id='$id'"));

        $tags['PROJECT_NAME'] = $project['name'];

        //$description = str_replace("\"", "&quot;", $description);

        //$title = str_replace("\"", "&quot;", $title);

        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        $description = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5);

        $error = 0;

        if ($parent_id == $task_id) {
            $error = 1;

            $errortext .= '<BR>YOU CAN NOT LINK A TASK TO ITSELF!';
        }

        $children = getChildren($task_id, 2);

        for ($x = 0, $xMax = count($children); $x < $xMax; $x++) {
            if ($children[$x][0] == $parent_id) {
                $error = 1;

                $errortext .= '<BR>YOU CAN NOT LINK A TASK TO ONE OF ITS CHILDREN!';
            }
        }

        if (!$error) {
            $query = "UPDATE $_XOOPS_project_manager_tasks_table
                SET title='$title', hours='$hours', priority='$priority', description='$description', billable='$billable', parent_id='$parent_id'
                WHERE task_id='$task_id'";

            $result = $xoopsDB->queryF($query);

            // Fetch the old user-task mappings

            $result = $xoopsDB->query("SELECT uid FROM $_XOOPS_project_manager_user_tasks_table WHERE task_id='$task_id'");

            while (false !== ($user_id = $xoopsDB->fetchArray($result))) {
                $old_users[] = $user_id;
            }

            // Delete the old mappings

            $xoopsDB->queryF("DELETE FROM $_XOOPS_project_manager_user_tasks_table WHERE task_id='$task_id'");

            // Add the new ones in

            $notificationHandler = xoops_getHandler('notification');

            foreach ($person as $user) {
                //Update user_tasks with a reference for every user

                if (0 == $user) {
                    continue;
                } // skip inserting None

                $xoopsDB->queryF("INSERT INTO $_XOOPS_project_manager_user_tasks_table VALUES (NULL, '$user', '$task_id')");

                // notify if new

                if (!in_array($user, $old_users, true)) {
                    $notificationHandler->triggerEvent('tasks', 0, 'new_task', $tags, [$user]);
                }
            }
        }

        //projectdetail($id);

        if ($result) {
            return 'Task Updated Successfully';
        }

        return "Error Updating Task $errortext";
    }

    function confirmDeleteTask($task_id, $project_id)
    {
        // This function may save your ass. It pops up to make sure you want to delete

        // a task. It waits for an answer. If the answer is "yes" then the id is passed

        // to the "deletetask()" function. Otherwise you are returned to the project

        // detail page

        global $xoopsDB, $xoopsTpl, $xoopsUser;

        global $_XOOPS_project_manager_is_admin;

        global $_XOOPS_project_manager_tasks_table;

        $auth_user = $xoopsDB->fetchArray($xoopsDB->query("SELECT person FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'"));

        if ($_XOOPS_project_manager_is_admin || $auth_user['person'] == $xoopsUser->uid()) {
            $xoopsTpl->assign('delete_task_authorized', 'yes');
        } else {
            return;
        }

        $query = "SELECT title FROM $_XOOPS_project_manager_tasks_table WHERE task_id=$task_id";

        $result = $xoopsDB->query($query);

        $list = $xoopsDB->fetchArray($result);

        $xoopsTpl->assign('delete_task_title', $list['title']);

        $xoopsTpl->assign('delete_task_id', $task_id);

        $xoopsTpl->assign('delete_task_project_id', $project_id);
    }

    function confirmRemoveUser($task_id)
    {
        global $xoopsDB, $xoopsTpl;

        global $_XOOPS_project_manager_tasks_table;

        $query = "SELECT title FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'";

        $result = $xoopsDB->query($query);

        $list = $xoopsDB->fetchArray($result);

        $xoopsTpl->assign('remove_task_title', $list['title']);

        $xoopsTpl->assign('remove_task_id', $task_id);
    }

    function removeUserAction($task_id)
    {
        global $xoopsDB, $xoopsUser;

        global $_XOOPS_project_manager_user_tasks_table;

        $query = "DELETE FROM $_XOOPS_project_manager_user_tasks_table WHERE task_id='$task_id' AND uid='" . $xoopsUser->uid() . "'";

        if ($xoopsDB->queryF($query)) {
            return 'Successfully removed yourself from task';
        }

        return 'Remove from task failed';
    }

    function displayComments($task_id)
    {
        global $xoopsDB, $xoopsUser;

        global $_XOOPS_project_manager_image_dir, $_XOOPS_project_manager_comments_table, $_XOOPS_project_manager_users_table;

        global $_XOOPS_project_manager_is_admin;

        $query = "SELECT *,DATE_FORMAT(date,'%b %e, %Y - %r')\"formatted_date\" 
                FROM $_XOOPS_project_manager_comments_table
                WHERE task_id='$task_id' 
                ORDER BY date ASC";

        $result = $xoopsDB->query($query);

        $my_comments = "<table class=outer width='100%'>
                <tr valign=top>
                    <th width='20%' class=head align=left>
                        <b>Comments</b>
                    </th>
                    <th width='80%' class=head align=right>
                        [<a class=itemHead href='index.php?op=addcomment&task_id=$task_id'>ADD COMMENT</a>]
                    </th>
                </tr>";

        while (false !== ($comments = $xoopsDB->fetchArray($result))) {
            $query2 = "SELECT uid, uname FROM $_XOOPS_project_manager_users_table WHERE uid='$comments[user]'";

            $result2 = $xoopsDB->query($query2);

            $name = $xoopsDB->fetchArray($result2);

            $formatted_comments = stripslashes(nl2br($comments['comment']));

            $my_comments .= '<tr valign=top>
                        <td class=even align=center>';

            if ($name['uname']) {
                $my_comments .= "<b>$name[uname]</b>";
            } else {
                $my_comments .= 'User Removed';
            }

            $my_comments .= "<br>$comments[formatted_date]<br>";

            if ($name['uid'] == $xoopsUser->uid() && $_XOOPS_project_manager_is_admin) {
                $my_comments .= "[<a href=index.php?op=editcomment&comment_id=$comments[comment_id]&task_id=$task_id>EDIT</a>]:: ";

                $my_comments .= "[<a href=index.php?op=deletecomment&comment_id=$comments[comment_id]&task_id=$task_id>DELETE</a>]";
            }

            $my_comments .= "</td>
                        <td class=odd>
                            $formatted_comments
                        </td>
                    </tr>";
        } //while

        $my_comments .= '</table><br><br>';

        return $my_comments;
    }

    function addComment($task_id)
    {
        // This function builds the form for adding a comment to a task.

        // When this form is submitted the variables are passed to the function

        // "addCommentAction()"

        global $xoopsDB, $xoopsTpl;

        global $_XOOPS_project_manager_tasks_table;

        $query = "SELECT title FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'";

        $result = $xoopsDB->query($query);

        $task = $xoopsDB->fetchArray($result);

        $xoopsTpl->assign('add_comment_task_title', $task['title']);

        $xoopsTpl->assign('add_comment_task_id', $task_id);
    }

    function addCommentAction($task_id, $comment)
    {
        // This function takes the results from the function "addComment()"

        // and inserts them into the database, then displays the results

        global $xoopsDB, $xoopsUser;

        global $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_user_tasks_table;

        global $_XOOPS_project_manager_comments_table, $_XOOPS_project_manager_projects_table;

        global $_XOOPS_project_manager_company;

        $date = date('YmdHis');

        if ('' == (string)$comment) {
            $comment = '<Empty comment>';
        }

        $tags = [];

        $result = $xoopsDB->fetchArray($xoopsDB->query("SELECT title FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'"));

        $tags['TASK_NAME'] = $result['title'];

        $result = $xoopsDB->fetchArray($xoopsDB->query("
            SELECT name 
            FROM $_XOOPS_project_manager_projects_table 
            LEFT JOIN $_XOOPS_project_manager_tasks_table 
            ON $_XOOPS_project_manager_projects_table.id = $_XOOPS_project_manager_tasks_table.id 
            WHERE task_id = '$task_id'"));

        $tags['PROJECT_NAME'] = $result['name'];

        $tags['COMMENT'] = $comment;

        $tags['COMPANY'] = $_XOOPS_project_manager_company;

        //$comment = str_replace("\"", "&quot;", $comment);

        $comment = htmlspecialchars($comment, ENT_QUOTES | ENT_HTML5);

        $flag_comments_on = $xoopsDB->queryF("UPDATE $_XOOPS_project_manager_tasks_table SET comments='1' WHERE task_id='$task_id'");

        if ($xoopsDB->queryF("INSERT INTO $_XOOPS_project_manager_comments_table
                                VALUES (NULL,'$task_id','" . $xoopsUser->uid() . "','$date','$comment')")) {
            // notify users assigned to the Task

            $result = $xoopsDB->query("SELECT uid FROM $_XOOPS_project_manager_user_tasks_table WHERE task_id='$task_id'");

            $notificationHandler = xoops_getHandler('notification');

            while (false !== ($user = $xoopsDB->fetchArray($result))) {
                $user_list = [$user['uid']];

                $notificationHandler->triggerEvent('comments', 0, 'new_comment', $tags, $user_list);
            }

            return 'Comment Added Successfully';
        }

        return 'Error Adding Comment';
    }

    function editComment($comment_id, $task_id)
    {
        global $xoopsDB, $xoopsUser, $xoopsTpl;

        global $_XOOPS_project_manager_comments_table;

        // get comment's owner

        $query = "SELECT user,comment FROM $_XOOPS_project_manager_comments_table WHERE comment_id='$comment_id'";

        $result = $xoopsDB->query($query);

        if ($owner = $xoopsDB->fetchArray($result)) {
            $auth_user = $owner['user'];

            $formatted_comment = str_replace('<br>', "\n", $owner['comment']);

            $formatted_comment = stripslashes($formatted_comment);

            $formatted_comment = htmlspecialchars($formatted_comment, ENT_QUOTES | ENT_HTML5);

            $xoopsTpl->assign('edit_comment_formatted_comment', $formatted_comment);

            $xoopsTpl->assign('edit_comment_auth_user', $auth_user);
        }

        $xoopsTpl->assign('edit_comment_id', $comment_id);

        $xoopsTpl->assign('edit_comment_task_id', $task_id);

        $xoopsTpl->assign('edit_comment_current_user', $xoopsUser->uid());
    }

    function editCommentAction($comment_id, $comment, $task_id)
    {
        global $xoopsDB, $xoopsUser;

        global $message, $_XOOPS_project_manager_is_admin;

        global $_XOOPS_project_manager_comments_table;

        // get comment's owner

        $query = "SELECT user FROM $_XOOPS_project_manager_comments_table WHERE comment_id='$comment_id'";

        $result = $xoopsDB->query($query);

        if ($owner = $xoopsDB->fetchArray($result)) {
            $auth_user = $owner['user'];
        }

        if ($auth_user == $xoopsUser->uid() || $_XOOPS_project_manager_is_admin) {
            $date = date('M j, Y - h:i:s A');

            $formatted_comment = addslashes($comment);

            //$formatted_comment .= "<br><br> <i>**EDITED BY $current_user -- [$date]</i>";

            if ($xoopsDB->queryF("UPDATE $_XOOPS_project_manager_comments_table
                                SET comment='$formatted_comment' WHERE comment_id='$comment_id'")) {
                $message = 'Comment Updated Successfully';
            } else {
                $message = 'Error Updating Comment';
            }
        } else {
            $message = 'You are not allowed to edit this comment';
        }
    }

    function deleteComment($comment_id, $task_id)
    {
        global $xoopsDB, $xoopsUser, $xoopsTpl;

        global $_XOOPS_project_manager_comments_table;

        // get comment's owner

        $query = "SELECT user,comment FROM $_XOOPS_project_manager_comments_table WHERE comment_id='$comment_id'";

        $result = $xoopsDB->query($query);

        if ($owner = $xoopsDB->fetchArray($result)) {
            $auth_user = $owner['user'];

            $formatted_comment = str_replace('<br>', "\n", $owner['comment']);

            $formatted_comment = stripslashes($formatted_comment);

            $xoopsTpl->assign('delete_comment_formatted_comment', $formatted_comment);

            $xoopsTpl->assign('delete_comment_auth_user', $auth_user);
        }

        $xoopsTpl->assign('delete_comment_current_user', $xoopsUser->uid());

        $xoopsTpl->assign('delete_comment_id', $comment_id);

        $xoopsTpl->assign('delete_comment_task_id', $task_id);
    }

    function deleteCommentAction($comment_id, $task_id)
    {
        global $_XOOPS_project_manager_is_admin;

        global $_XOOPS_project_manager_comments_table;

        global $_XOOPS_project_manager_tasks_table;

        global $xoopsDB, $xoopsUser;

        $message = '';

        // get comment's owner

        $query = "SELECT user FROM $_XOOPS_project_manager_comments_table WHERE comment_id='$comment_id'";

        $result = $xoopsDB->query($query);

        if ($owner = $xoopsDB->fetchArray($result)) {
            $auth_user = $owner[user];
        }

        $current_user = $xoopsUser->uid();

        // only let the comment owner or an admin edit a comment

        if (($auth_user == $current_user) || $_XOOPS_project_manager_is_admin) {
            if ($xoopsDB->queryF("DELETE FROM $_XOOPS_project_manager_comments_table WHERE comment_id='$comment_id'")) {
                $message = 'Comment Deleted';
            } else {
                $message = 'Error Deleting Comment';
            }

            // get number of comments left, if 0 then un-flag comments for the task

            $query = "SELECT comment_id FROM $_XOOPS_project_manager_comments_table WHERE task_id='$task_id'";

            $result = $xoopsDB->query($query);

            if (!$list = $xoopsDB->fetchArray($result)) {
                $xoopsDB->queryF("UPDATE $_XOOPS_project_manager_tasks_table SET comments='0' WHERE task_id='$task_id'");
            }
        }

        return $message;
    }

    function insert_popupLoader($args)
    {
        echo "<a class=$args[class] name=$args[popup_id] href=$args[link] 
                onMouseOver=\"window.status='" . addslashes($args['linkname']) . "'; show('box-$args[popup_id]'); return true;\" onMouseOut=\"hide('box-$args[popup_id]'); return true;\">
                $args[linkname]
        </a>
        <DIV ID=box-$args[popup_id] class=hidden>
        <table class=outer>
            <tr>
                <th>
                    &nbsp;&nbsp;$args[linkname]<br>
                    <li class=even>";

        if ('' == $args['description']) {
            echo 'No Description';
        } else {
            echo nl2br($args['description']);
        }

        echo '</th>
            </tr>
       </table>
       </div>';
    }
