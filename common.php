<?php

// Globals
    $_XOOPS_project_manager_users_table = $xoopsDB->prefix('project_manager_users');
    $_XOOPS_project_manager_user_tasks_table = $xoopsDB->prefix('project_manager_user_tasks');
    $_XOOPS_project_manager_projects_table = $xoopsDB->prefix('project_manager_projects');
    $_XOOPS_project_manager_comments_table = $xoopsDB->prefix('project_manager_comments');
    $_XOOPS_project_manager_tasks_table = $xoopsDB->prefix('project_manager_tasks');

    $_XOOPS_project_manager_is_admin = $xoopsUser->isAdmin();
    $_XOOPS_project_manager_company = $xoopsModuleConfig['company'];
    $_XOOPS_project_manager_image_dir = XOOPS_URL . '/modules/project-manager/images';

    function errorHandle($number, $string, $file, $line)
    {
        error_log("Error($number) on line $line in file $file. The error was \"$string\"\n", 3, 'error.log');
    }

    function insert_drawTimeAndProgressBars($args)
    {
        $id = $args['id'];

        // FUNCTION SUBMITTED BY: DAVID HUGHES  (David.W.Hughes@cern.ch)

        $timedone = percentTimeComplete($id);

        $workdone = insert_percentComplete($args);

        echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=0>\n";

        $safe_td = max(0, min(100, $timedone));

        //if ($completed) { $safe_td = min(100, $safe_td); }

        //if ($completed || $safe_td < $workdone) {

        if ($safe_td < $workdone) {
            // project complete or within schedule

            $colour = 'green';
        } elseif ($timedone < 100) {
            // within deadline but behind schedule

            $colour = 'yellow';
        } else {
            // project over deadline

            $colour = 'red';
        }

        if ($timedone <= 0) {
            $timedone = 'scheduled';
        } elseif ($timedone >= 100) {
            $timedone = '<b>overdue</b>';
        } else {
            $timedone = "$timedone%&nbsp;time";
        }

        if ($workdone <= 0) {
            $workdone = 'not started';
        } elseif ($workdone >= 100) {
            $workdone = '<b>complete</b>';
        } else {
            $workdone = "$workdone%&nbsp;work";
        }

        echo "<TR><TD>$timedone</TD><TD>&nbsp;&nbsp;</TD><TD nowrap>", drawBarColor($colour, $safe_td), "</TD></TR>\n";

        echo "<TR><TD>$workdone</TD><TD>&nbsp;&nbsp;</TD><TD nowrap>", drawBarColor('green', $workdone), "</TD></TR>\n";

        echo "</TABLE>\n";
    }

    function drawBarColor($colour, $percent)
    {
        // This function takes the integer (from 0-100) $percent as an argument

        global $_XOOPS_project_manager_image_dir;

        // to build a bargraph using small images. It doesn't depend on the gd lib.

        // safety: normalise $percent if it's not between 0 and 100.

        // FUNCTION SUBMITTED BY: DAVID HUGHES  (David.W.Hughes@cern.ch)

        $percent = max(0, min(100, $percent));

        $l_colour = (0 == $percent ? 'grey' : $colour);

        $r_colour = (100 == $percent ? $colour : 'grey');

        // This is a hack to avoid bad browser behaviour from using <img width=0>

        if (0 == $percent) {
            $percent = 1;
        }

        if (100 == $percent) {
            $percent = 99;
        }

        echo "<img src=$_XOOPS_project_manager_image_dir/bars/bar-left-$l_colour.gif border=0 height=12 width=5 alt=\"\">";

        echo "<img src=$_XOOPS_project_manager_image_dir/bars/bar-tile-$l_colour.gif border=0 height=12 width=", ($percent * 2), ' alt="">';

        echo "<img src=$_XOOPS_project_manager_image_dir/bars/bar-tile-$r_colour.gif border=0 height=12 width=", (200 - $percent * 2), ' alt="">';

        echo "<img src=$_XOOPS_project_manager_image_dir/bars/bar-right-$r_colour.gif border=0 height=12 width=5 alt=\"\">\n";
    }

    function insert_percentComplete($args)
    {
        // This function takes a project id and and compares the todo hours

        // to the finished hours to make a percent done

        $id = $args['id'];

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $Qtodo = "SELECT * FROM $_XOOPS_project_manager_tasks_table WHERE id=$id AND status=1";

        $Qdone = "SELECT * FROM $_XOOPS_project_manager_tasks_table WHERE id=$id AND status=0";

        $Rtodo = $xoopsDB->query($Qtodo);

        $Rdone = $xoopsDB->query($Qdone);

        $totaltodo = 0;

        $totaldone = 0;

        while (false !== ($todo = $xoopsDB->fetchArray($Rtodo))) {
            $totaltodo += $todo['hours'];
        }

        while (false !== ($done = $xoopsDB->fetchArray($Rdone))) {
            $totaldone += $done['hours'];
        }

        $totalboth = $totaltodo + $totaldone;

        if ($totalboth > 0) {
            $percent = 100 * ($totaldone / $totalboth);

            $percentdone = sprintf('%2d', $percent);

            return $percentdone;
        }

        return $totaldone;
    }

    function percentTimeComplete($id)
    {
        // Use built-in MySQL functions to calculate timespan of project and see how

        // far into a project (time-wise) we are.

        // FUNCTION SUBMITTED BY: DAVID HUGHES  (David.W.Hughes@cern.ch)

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        $query = "SELECT to_days(current_date)-to_days(startdate) AS elapsed, to_days(enddate)-to_days(startdate) AS total, completed FROM
            $_XOOPS_project_manager_projects_table WHERE id = $id";

        $result = $xoopsDB->query($query);

        $row = $xoopsDB->fetchArray($result);

        $elapsed = $row['elapsed'];

        $total = $row['total'];

        if (0 == $total) {
            return 100;
        }

        if (0 == (100 * $elapsed / $total)) {
            return 0;
        }

        return sprintf('%02d', 100 * $elapsed / $total);
    }

    function finishTask($task_id)
    {
        // This function updates a task in the database.

        // All it really does is switch status from 1 to 0.

        // Then it reloads the page you were on.

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $today = date('Y-m-d');

        $query = "UPDATE $_XOOPS_project_manager_tasks_table SET status='0', enddate='$today' WHERE task_id='$task_id'";

        $result = $xoopsDB->queryF($query);

        if ($result) {
            return 'Task Completed';
        }

        return 'Failed to Complete Task';
    }

    function resetTask($task_id)
    {
        // This function updates a task in the database.

        // All it really does is switch status from 0 to 1.

        // Then it reloads the page you were on.

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $query = "UPDATE $_XOOPS_project_manager_tasks_table SET status='1' WHERE task_id='$task_id'";

        $result = $xoopsDB->queryF($query);

        if ($result) {
            return 'Task Reactivated';
        }

        return 'Failed to Reactivate Task';
    }

    function insert_prettyDate($args)
    {
        $enddate = $args['id'];

        // This function makes the date look more human readable

        $endyear = mb_substr($enddate, 0, 4);

        $endmonth = mb_substr($enddate, 5, 2);

        $endday = mb_substr($enddate, 8, 2);

        switch ($endmonth) {
            case '01':
            $prettymonth = 'January';
            break;
            case '02':
            $prettymonth = 'February';
            break;
            case '03':
            $prettymonth = 'March';
            break;
            case '04':
            $prettymonth = 'April';
            break;
            case '05':
            $prettymonth = 'May';
            break;
            case '06':
            $prettymonth = 'June';
            break;
            case '07':
            $prettymonth = 'July';
            break;
            case '08':
            $prettymonth = 'August';
            break;
            case '09':
            $prettymonth = 'September';
            break;
            case '10':
            $prettymonth = 'October';
            break;
            case '11':
            $prettymonth = 'November';
            break;
            case '12':
            $prettymonth = 'December';
            break;
        }

        echo "$prettymonth $endday, $endyear";
    }

    function insert_hoursToDo($args)
    {
        // input project id

        // returns the ammount of hours left todo on a project

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $id = $args['id'];

        $query = "SELECT SUM(hours) FROM $_XOOPS_project_manager_tasks_table WHERE id='$id' AND status=1";

        $result = $xoopsDB->query($query);

        if ($row = $xoopsDB->fetchRow($result)) {
            return $row[0];
        }
    }

    function insert_hoursDone($args)
    {
        // input project id

        // returns the ammount of hours done on a project

        global $xoopsDB;

        global $_XOOPS_project_manager_tasks_table;

        $id = $args['id'];

        $query = "SELECT SUM(hours) FROM $_XOOPS_project_manager_tasks_table WHERE id='$id' AND status=0";

        $result = $xoopsDB->query($query);

        if ($row = $xoopsDB->fetchRow($result)) {
            return $row[0];
        }
    }

    function deleteTaskAction($task_id)
    {
        // This function deletes a task flat out. You've already been warned at this point.

        global $xoopsDB, $_XOOPS_project_manager_is_admin;

        global $_XOOPS_project_manager_tasks_table, $_XOOPS_project_manager_comments_table;

        global $_XOOPS_project_manager_user_tasks_table;

        if (!$_XOOPS_project_manager_is_admin) {
            return 'Non-admin users not allowed to delete tasks';
        }

        $query = "SELECT parent_id,title FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'";

        $result = $xoopsDB->fetchArray($xoopsDB->query($query));

        $parent_id = $result['parent_id'];

        $task_name = $result['title'];

        // delete comments associated with the task

        $query = "DELETE FROM $_XOOPS_project_manager_comments_table WHERE task_id='$task_id'";

        if ($result = $xoopsDB->queryF($query)) {
            $deleted_comments_num = $xoopsDB->getRowsNum($result);

            $ret_val = "$deleted_comments_num Comments Deleted<br>";
        } else {
            return "Unabled to delete comments for task \"$task_name\"<br>Aborting...";
        }

        // clean up user-task associations

        $query = "DELETE FROM $_XOOPS_project_manager_user_tasks_table WHERE task_id=$task_id";

        if ($xoopsDB->queryF($query)) {
            $ret_val .= 'User-Task Associations Deleted<br>';
        } else {
            return (string)$ret_val . 'Unable to delete user-task associations!<br>Aborting...';
        }

        // Now that the task has been successfully deleted, make sure its subtasks get proper parent id

        $num_children = $xoopsDB->getRowsNum($xoopsDB->query("SELECT task_id FROM $_XOOPS_project_manager_tasks_table WHERE parent_id='$task_id'"));

        if ($num_children) {
            $query = "UPDATE $_XOOPS_project_manager_tasks_table SET parent_id = '$parent_id' WHERE parent_id='$task_id'";

            if ($xoopsDB->queryF($query)) {
                $ret_val .= "$num_chilren Tasks' parent_id Updated<br>";
            } else {
                return (string)$ret_val . 'Unable to updated parent_id of children tasks!<br>Aborting...';
            }
        }

        $query = "DELETE FROM $_XOOPS_project_manager_tasks_table WHERE task_id='$task_id'";

        if ($xoopsDB->queryF($query)) {
            return (string)$ret_val . "Task \"$task_name\" Deleted Successfully";
        }

        return 'Task Deletion Failed';
    }

    function completeProjectAction($id)
    {
        global $_XOOPS_project_manager_is_admin, $_XOOPS_project_manager_projects_table;

        global $xoopsDB;

        $completed_date = date('Y-m-d');

        if (100 == insert_percentComplete(['id' => $id]) && $_XOOPS_project_manager_is_admin) {
            $query = "UPDATE $_XOOPS_project_manager_projects_table SET completed='1', completed_date='$completed_date' WHERE id='$id'";

            $result = $xoopsDB->queryF($query);
        }
    }

    function reactivateProjectAction($id)
    {
        global $_XOOPS_project_manager_is_admin, $_XOOPS_project_manager_projects_table;

        global $xoopsDB;

        if (100 == insert_percentComplete(['id' => $id]) && $_XOOPS_project_manager_is_admin) {
            $query = "UPDATE $_XOOPS_project_manager_projects_table SET completed='0', completed_date='0' WHERE id='$id'";

            $result = $xoopsDB->queryF($query);
        }
    }


