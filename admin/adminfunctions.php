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

    function projectManagerHeader($level)
    {
        global $_XOOPS_project_manager_image_dir;

        global $xoopsUser;

        ini_set('display_errors', 0);

        ini_set('display_startup_errors', 0);

        set_error_handler('errorHandle');

        error_reporting(0);

        echo '<link rel=stylesheet type=text/css href=' . XOOPS_URL . "/modules/project-manager/style/default.css>\n";

        echo "<script type='text/javascript' src='" . XOOPS_URL . "/modules/project-manager/popups.js'></script>\n";

        echo '<table width=100% border=0 cellspacing=0 cellpadding=4>
                <tr>
                    <td class=smaller align=left>
                        Logged in as: <b>' . $xoopsUser->uname() . '</b>
                    </td>
                    <td class=smaller align=right>';

        echo '[<a href=index.php?op=listprojects>ALL PROJECTS</a>]';

        echo ':: [<a href=index.php?op=addproject>NEW PROJECT</a>]';

        echo "<img src=$_XOOPS_project_manager_image_dir/spacer.gif width=8 height=1 border=0>
                    </td>
                </tr>
            </table>
            <div id=\"object1\" style=\"showing\">&nbsp;</div>";
    }

    function userList($message)
    {
        // Show a list of all IPM users and provide links for adding/editing/deleting them

        global $xoopsDB;

        global $_XOOPS_project_manager_users_table;

        $query = "SELECT * FROM $_XOOPS_project_manager_users_table";

        $result = $xoopsDB->query($query);

        $query = 'SELECT uid,uname FROM ' . $xoopsDB->prefix('users');

        $result2 = $xoopsDB->query($query);

        echo ' <form action=index.php method=post>
                <table class=outer align=center width=100%>
                    <tr valign=middle>
                        <th align=left>
                            Full Name
                        </th>
                        <th align=center nowrap>
                            Username
                        </th>
                        <th align=center nowrap>
                            Actions
                        </th>
                    </tr>';

        while (false !== ($list = $xoopsDB->fetchArray($result))) {
            echo "<tr valign=top>
                        <td align=left class=odd>
                            $list[name]
                        </td>
                        <td align=center class=odd width=10% nowrap>
                            $list[uname]
                        </td>
                        <td align=center class=odd width=10% nowrap>
                            <a href=index.php?op=confirmdeleteuser&id=$list[uid]>[REMOVE]</a>
                        </td>
                    </tr>";
        }

        echo '<tr valign=top>
                        <td align=left class=head>
                            <b>Add XOOPS User to Project Manager:</b>
                        </td>
                        <td align=center class=head width=10% nowrap>
                            <select name=user>';

        while (false !== ($ulist = $xoopsDB->fetchArray($result2))) {
            echo "<option value=$ulist[uid]>$ulist[uname]</option>";
        }

        echo '</select>
                        </td>
                        <td align=center class=head width=10% nowrap>
                            <input type=hidden name=op value=useraddaction>
                            <input type=submit name=submit value="Add User" class=button>
                        </td>
                    </tr>
                </table>
            </form>';

        if ('' != $message) {
            echo "<center><a class=importantmessage>$message</a></center>";
        }
    }

    function confirmDeleteUser($id)
    {
        // Makes sure you REALLY want to delete someone

        global $xoopsDB;

        global $_XOOPS_project_manager_users_table;

        $query = "SELECT * FROM $_XOOPS_project_manager_users_table WHERE uid=$id";

        $result = $xoopsDB->query($query);

        $list = $xoopsDB->fetchArray($result);

        echo "<table class=outer align=center>
                <tr>
                    <th align=center>
                        Are you sure you want to delete <b>$list[uname]</b>?
                    </th>
                </tr>
                <tr>
                    <td align=center class=even>
                        <a href=index.php?op=deleteuseraction&id=$id>[YES]</a> :: <a href=Javascript:history.go(-1)>[NO]</a>
                    </td>
                </tr>
            </table>";
    }

    function deleteUserAction($uid)
    {
        // deletes a user from the database but leaves their tasks to be

        // reassigned or deleted manually

        global $xoopsDB;

        global $_XOOPS_project_manager_users_table;

        global $_XOOPS_project_manager_user_tasks_table;

        $query = "DELETE FROM $_XOOPS_project_manager_users_table WHERE uid='$uid'";

        $query2 = "DELETE FROM $_XOOPS_project_manager_user_tasks_table WHERE uid='$uid'";

        // unsubscribe notifications

        $notificationHandler = xoops_getHandler('notification');

        $notificationHandler->unsubscribe('tasks', null, 'new_task', null, $uid);

        $notificationHandler->unsubscribe('comments', null, 'new_comment', null, $uid);

        if ($xoopsDB->queryF($query) && $xoopsDB->queryF($query2)) {
            return 'User Successfully Deleted';
        }

        return 'User Delete Failed';
    }

    function userAddAction($uid)
    {
        // deletes a user from the database but leaves their tasks to be

        // reassigned or deleted manually

        global $xoopsDB;

        global $_XOOPS_project_manager_users_table;

        /* check that the user doesn't already exist in the IPM db */

        $query = "SELECT uid FROM $_XOOPS_project_manager_users_table WHERE uid='$uid'";

        if ($xoopsDB->fetchRow($xoopsDB->query($query))) {
            return 'User already exists';
        }

        $query = 'SELECT uid,uname,name FROM ' . $xoopsDB->prefix('users') . " WHERE uid='$uid'";

        $result = $xoopsDB->query($query);

        $user = $xoopsDB->fetchArray($result);

        $query = "INSERT INTO $_XOOPS_project_manager_users_table VALUES ('$user[uid]', '$user[uname]', '$user[name]');";

        // update user's notification preferences

        $notificationHandler = xoops_getHandler('notification');

        //$notificationHandler->unsubscribe($category, $item_id, $event, $module_id, $user_id);

        $notificationHandler->subscribe('tasks', 0, 'new_task', null, null, $uid);

        $notificationHandler->subscribe('comments', 0, 'new_comment', null, null, $uid);

        if ($result = $xoopsDB->queryF($query)) {
            return 'User Add Succeeded';
        }

        return 'User Add Failed';
    }

    function listProjects($status)
    {
        // This function goes to the database and generates a list of current projects.

        // It also builds a menu for editing/deleting/completing projects.

        //dbconnect();

        global $order, $_XOOPS_project_manager_image_dir, $_XOOPS_project_manager_is_admin;

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        $table = $_XOOPS_project_manager_projects_table;

        switch ($order) {
            case 'nameup':
            $myorder = 'name ASC';
            break;
            case 'namedown':
            $myorder = 'name DESC';
            break;
            case 'datedown':
            $myorder = 'enddate DESC';
            break;
            case 'dateup':
            $myorder = 'enddate ASC';
            break;
            default:
            $myorder = 'enddate ASC';
        }

        $list_query = "SELECT id, name, enddate, comments FROM $table WHERE completed='$status' ORDER BY $myorder";

        $listresult = $xoopsDB->query($list_query);

        echo '<table class=outer align=center width=100%>
            <tr>
                <th align=center>';

        if (0 == $status) {
            echo 'Active Projects';
        } else {
            echo 'Completed Projects';
        }

        echo '</th>
            </tr>
        </table>';

        echo "<table class=outer align=center width=100%>
                <tr class=head width='100%'>";

        echo '<th align=left>';

        if ('nameup' == $order) {
            echo "<a onClick=\"location='index.php?op=listprojects&order=namedown';\" ><b>Project Name</b></a>";
        } else {
            echo "<a onClick=\"location='index.php?op=listprojects&order=nameup';\" ><b>Project Name</b></a>";
        }

        echo "</th>
                    <th width='10%' align=center>
                        <b>Percent Completed</b>
                    </th>";

        echo "<th width='10%' align=center>";

        if ('dateup' == $order) {
            echo "<a onClick=\"location='index.php?op=listprojects&order=datedown';\"><b>Deadline</b></a>";
        } else {
            echo "<a onClick=\"location='index.php?op=listprojects&order=dateup';\"><b>Deadline</b></a>";
        }

        echo '</th>';

        echo "<th width='10%' align=center>
                            <b>Actions</b>
                    </th>
                </tr>";

        $color = true;

        while (false !== ($list_row = $xoopsDB->fetchArray($listresult))) {
            echo "<tr valign=top>
                    <td class=odd align=left>
                        <b>$list_row[name]</b><br>
                        <i>($list_row[comments])</i>
                    </td>
                    <td nowrap class=even align=right>";

            insert_drawTimeAndProgressBars($list_row['id']);

            echo '</td>
                    <td nowrap class=even align=center>';

            insert_prettyDate(['id' => $list_row['enddate']]);

            echo "</td>
                    <td nowrap class=even align=center>[<a href=index.php?op=editproject&id=$list_row[id]>
                        EDIT</a>] :: [<a href=index.php?op=confirmprojectdelete&id=$list_row[id]>DELETE</a>]
                    </td>
                </tr>";
        }

        echo '</table><br>';
    }

    function addProject()
    {
        // This function builds the form for adding a new project.

        // When this form is submitted the variables are passed to the function

        // "addprojectaction()"

        $today = date('j');

        $thismonthname = date('M');

        $thismonthvalue = date('n');

        $thisyear = date('Y');

        echo "<form action=util.php method=post>
                <table class=outer>
                    <tr>
                        <th colspan=2 width=100% align=left>
                            <b>Add New Project</b>
                        </th>
                    </tr>
                    <tr>
                        <td class=even width=38% align=right>
                            <b>Project Name: </b>
                        </td>
                        <td class=odd width=62%>
                            <input name=name type=text size=40>
                        </td>
                    </tr>
                    <tr>
                        <td class=even align=right>
                            <b>Start Date: </b>
                        </td>
                        <td class=odd>
                            <select name=startmonth size=1>
                                <option value=$thismonthvalue selected>$thismonthname</option>
                                <option value=$thismonthvalue>-----</option>
                                <option value=01>Jan</option>
                                <option value=02>Feb</option>
                                <option value=03>Mar</option>
                                <option value=04>Apr</option>
                                <option value=05>May</option>
                                <option value=06>Jun</option>
                                <option value=07>Jul</option>
                                <option value=08>Aug</option>
                                <option value=09>Sep</option>
                                <option value=10>Oct</option>
                                <option value=11>Nov</option>
                                <option value=12>Dec</option>
                            </select>
                            <select name=startday size=1>
                                <option value=$today selected>$today</option>
                                <option value=$today>-----</option>
                                <option value=01>01</option>
                                <option value=02>02</option>
                                <option value=03>03</option>
                                <option value=04>04</option>
                                <option value=05>05</option>
                                <option value=06>06</option>
                                <option value=07>07</option>
                                <option value=08>08</option>
                                <option value=09>09</option>
                                <option value=10>10</option>
                                <option value=11>11</option>
                                <option value=12>12</option>
                                <option value=13>13</option>
                                <option value=14>14</option>
                                <option value=15>15</option>
                                <option value=16>16</option>
                                <option value=17>17</option>
                                <option value=18>18</option>
                                <option value=19>19</option>
                                <option value=20>20</option>
                                <option value=21>21</option>
                                <option value=22>22</option>
                                <option value=23>23</option>
                                <option value=24>24</option>
                                <option value=25>25</option>
                                <option value=26>26</option>
                                <option value=27>27</option>
                                <option value=28>28</option>
                                <option value=29>29</option>
                                <option value=30>30</option>
                                <option value=31>31</option>
                            </select>
                            <select name=startyear size=1>
                                <option value=$thisyear selected>$thisyear</option>";

        $nextyear = $thisyear + 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class=even align=right>
                            <b>Deadline: </b>
                        </td>
                        <td class=odd>
                            <select name=endmonth size=1>
                                <option value=$thismonthvalue selected>$thismonthname</option>
                                <option value=$thismonthvalue>-----</option>
                                <option value=01>Jan</option>
                                <option value=02>Feb</option>
                                <option value=03>Mar</option>
                                <option value=04>Apr</option>
                                <option value=05>May</option>
                                <option value=06>Jun</option>
                                <option value=07>Jul</option>
                                <option value=08>Aug</option>
                                <option value=09>Sep</option>
                                <option value=10>Oct</option>
                                <option value=11>Nov</option>
                                <option value=12>Dec</option>
                            </select>
                            <select name=endday size=1>
                                <option value=$today selected>$today</option>
                                <option value=$today>-----</option>
                                <option value=01>01</option>
                                <option value=02>02</option>
                                <option value=03>03</option>
                                <option value=04>04</option>
                                <option value=05>05</option>
                                <option value=06>06</option>
                                <option value=07>07</option>
                                <option value=08>08</option>
                                <option value=09>09</option>
                                <option value=10>10</option>
                                <option value=11>11</option>
                                <option value=12>12</option>
                                <option value=13>13</option>
                                <option value=14>14</option>
                                <option value=15>15</option>
                                <option value=16>16</option>
                                <option value=17>17</option>
                                <option value=18>18</option>
                                <option value=19>19</option>
                                <option value=20>20</option>
                                <option value=21>21</option>
                                <option value=22>22</option>
                                <option value=23>23</option>
                                <option value=24>24</option>
                                <option value=25>25</option>
                                <option value=26>26</option>
                                <option value=27>27</option>
                                <option value=28>28</option>
                                <option value=29>29</option>
                                <option value=30>30</option>
                                <option value=31>31</option>
                            </select>
                            <select name=endyear size=1>
                                <option value=$thisyear selected>$thisyear</option>";

        $nextyear = $thisyear + 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>
                            </select>
                        </td>
                    </tr>
                    <tr align=top>
                        <td class=even align=right>
                            <b>Comments: </b>
                        </td>
                        <td class=odd>
                            <textarea cols=40 rows=4 name=comments></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th align=center colspan=2>
                            <input class=button type=reset> <input class=button type=submit value=\"Add Project\">
                        </th>
                    </tr>
                </table>
                <input type=hidden name=util_op value=addprojectaction>
            </form>";
    } //addproject

    function addProjectAction($name, $startmonth, $startday, $startyear, $endmonth, $endday, $endyear, $comments)
    {
        // This function receives the variables sent by "addproject()"

        // then it adds the project to the database.

        global $xoopsDB;

        global $message;

        global $_XOOPS_project_manager_projects_table;

        $startdate = "$startyear.$startmonth.$startday";

        $enddate = "$endyear.$endmonth.$endday";

        //Format DB input

        if ('' == $name) {
            $name = '<No Project Name Given>';
        }

        if ('' == $comments) {
            $comments = '<No Description Given>';
        }

        $name = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);

        $comments = htmlspecialchars($comments, ENT_QUOTES | ENT_HTML5);

        $result = $xoopsDB->queryF("INSERT INTO $_XOOPS_project_manager_projects_table VALUES (NULL,'$name','$startdate','$enddate','$comments','0','0000-00-00')");

        if ($result) {
            return "Project \"$name\" Added Successfully";
        }

        return 'Error Adding Project';
    }

    function confirmProjectDelete($id)
    {
        // This function may save your ass someday. When you try to delete a project,

        // this function is called. It warns you, that the function and all associated tasks

        // are about to be deleted. Then it give the choice of continuing or going back to the

        // project list.

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        $query = "SELECT name FROM $_XOOPS_project_manager_projects_table WHERE id=$id";

        $result = $xoopsDB->query($query);

        $list = $xoopsDB->fetchArray($result);

        echo "<table width='100%' class=outer align=center>
                <tr>
                    <th align=center>
                        Are you sure you want to delete the <b>\"$list[name]\"</b> project?
                    </th>
                </tr>
                <tr>
                    <td class=head align=center>
                        ALL TASKS AND ASSOCIATED INFO WILL BE DELETED
                    </td>
                </tr>
                <tr>
                    <td align=center class=even>
                        <a href=util.php?util_op=deleteprojectaction&id=$id>[YES]</a> :: <a href=index.php?op=listprojects>[NO]</a>
                    </td>
                </tr>
            </table>";
    }

    function deleteProjectAction($id)
    {
        // We warned you. This function deletes a project and takes all of its tasks with it.

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        global $_XOOPS_project_manager_tasks_table;

        // Go through and delete all tasks and everything else associated with this project

        $result = $xoopsDB->query("SELECT task_id FROM $_XOOPS_project_manager_tasks_table WHERE id='$id'");

        while (false !== ($list = $xoopsDB->fetchArray($result))) {
            $ret_val .= deleteTaskAction($list['task_id']);

            $ret_val .= '<br>';
        }

        $query = "DELETE FROM $_XOOPS_project_manager_projects_table WHERE id='$id'";

        if ($xoopsDB->queryF($query)) {
            $ret_val .= 'Project deleted successfully!';
        } else {
            $ret_val .= 'Project delete had ERRORS!';
        }

        return $ret_val;
    }

    function editProject($id)
    {
        // This function builds the form for editing a pre-existing project.

        // When submitted, the variables are sent to "editprojectaction()"

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        $query = "SELECT name,startdate,enddate,comments FROM $_XOOPS_project_manager_projects_table where id=$id";

        $result = $xoopsDB->query($query);

        $list = $xoopsDB->fetchArray($result);

        $startyear = mb_substr($list['startdate'], 0, 4);

        $thisyear = date('Y');

        $startmonth = mb_substr($list['startdate'], 5, 2);

        $startday = mb_substr($list['startdate'], 8, 2);

        $endyear = mb_substr($list['enddate'], 0, 4);

        $endmonth = mb_substr($list['enddate'], 5, 2);

        $endday = mb_substr($list['enddate'], 8, 2);

        $Uname = stripslashes($list['name']);

        $Ucomments = stripslashes((string)$list[comments]);

        echo "<form action=util.php method=post>
                <table class=outer>
                    <tr>
                        <th colspan=2>
                            Project Editor
                        </th>
                    </tr>
                    <tr>
                        <td width=38% align=right class=even>
                            Project Name:
                        </td>
                        <td class=odd align=left width=62%>
                            <input value=\"$Uname\" size=40 name=name type=text>
                        </td>
                    </tr>
                    <tr>
                        <td class=even align=right>
                            Start Date:
                        </td>
                        <td class=odd>
                            <select name=startmonth size=1>";

        // MySQL doesn't store pretty names for months. So this is my workaround.

        switch ($startmonth) {
                                    case '01':
                                    $startmonthname = 'Jan';
                                    break;
                                    case '02':
                                    $startmonthname = 'Feb';
                                    break;
                                    case '03':
                                    $startmonthname = 'Mar';
                                    break;
                                    case '04':
                                    $startmonthname = 'Apr';
                                    break;
                                    case '05':
                                    $startmonthname = 'May';
                                    break;
                                    case '06':
                                    $startmonthname = 'Jun';
                                    break;
                                    case '07':
                                    $startmonthname = 'Jul';
                                    break;
                                    case '08':
                                    $startmonthname = 'Aug';
                                    break;
                                    case '09':
                                    $startmonthname = 'Sep';
                                    break;
                                    case '10':
                                    $startmonthname = 'Oct';
                                    break;
                                    case '11':
                                    $startmonthname = 'Nov';
                                    break;
                                    case '12':
                                    $startmonthname = 'Dec';
                                    break;
                                }

        echo "<option value=$startmonth selected>$startmonthname</option>
                                <option value=$startmonth>-----</option>
                                <option value=01>Jan</option>
                                <option value=02>Feb</option>
                                <option value=03>Mar</option>
                                <option value=04>Apr</option>
                                <option value=05>May</option>
                                <option value=06>Jun</option>
                                <option value=07>Jul</option>
                                <option value=08>Aug</option>
                                <option value=09>Sep</option>
                                <option value=10>Oct</option>
                                <option value=11>Nov</option>
                                <option value=12>Dec</option>
                            </select>
                            <select name=startday size=1>
                                <option value=$startday selected>$startday</option>
                                <option value=$startday>-----</option>
                                <option value=01>01</option>
                                <option value=02>02</option>
                                <option value=03>03</option>
                                <option value=04>04</option>
                                <option value=05>05</option>
                                <option value=06>06</option>
                                <option value=07>07</option>
                                <option value=08>08</option>
                                <option value=09>09</option>
                                <option value=10>10</option>
                                <option value=11>11</option>
                                <option value=12>12</option>
                                <option value=13>13</option>
                                <option value=14>14</option>
                                <option value=15>15</option>
                                <option value=16>16</option>
                                <option value=17>17</option>
                                <option value=18>18</option>
                                <option value=19>19</option>
                                <option value=20>20</option>
                                <option value=21>21</option>
                                <option value=22>22</option>
                                <option value=23>23</option>
                                <option value=24>24</option>
                                <option value=25>25</option>
                                <option value=26>26</option>
                                <option value=27>27</option>
                                <option value=28>28</option>
                                <option value=29>29</option>
                                <option value=30>30</option>
                                <option value=31>31</option>
                            </select>
                            <select name=startyear size=1>
                                <option value=$startyear selected>$startyear</option>";

        $nextyear = $startyear + 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align=right class=even>
                            Deadline:
                        </td>
                        <td class=odd>
                            <select name=endmonth size=1>";

        // MySQL doesn't store pretty names for months. So this is my workaround.

        switch ($endmonth) {
                                    case '01':
                                    $endmonthname = 'Jan';
                                    break;
                                    case '02':
                                    $endmonthname = 'Feb';
                                    break;
                                    case '03':
                                    $endmonthname = 'Mar';
                                    break;
                                    case '04':
                                    $endmonthname = 'Apr';
                                    break;
                                    case '05':
                                    $endmonthname = 'May';
                                    break;
                                    case '06':
                                    $endmonthname = 'Jun';
                                    break;
                                    case '07':
                                    $endmonthname = 'Jul';
                                    break;
                                    case '08':
                                    $endmonthname = 'Aug';
                                    break;
                                    case '09':
                                    $endmonthname = 'Sep';
                                    break;
                                    case '10':
                                    $endmonthname = 'Oct';
                                    break;
                                    case '11':
                                    $endmonthname = 'Nov';
                                    break;
                                    case '12':
                                    $endmonthname = 'Dec';
                                    break;
                                }

        echo "<option value=$endmonth selected>$endmonthname</option>
                                <option value=$endmonth>-----</option>
                                <option value=01>Jan</option>
                                <option value=02>Feb</option>
                                <option value=03>Mar</option>
                                <option value=04>Apr</option>
                                <option value=05>May</option>
                                <option value=06>Jun</option>
                                <option value=07>Jul</option>
                                <option value=08>Aug</option>
                                <option value=09>Sep</option>
                                <option value=10>Oct</option>
                                <option value=11>Nov</option>
                                <option value=12>Dec</option>
                            </select>
                            <select name=endday size=1>
                                <option value=$endday selected>$endday</option>
                                <option value=$endday>-----</option>
                                <option value=01>01</option>
                                <option value=02>02</option>
                                <option value=03>03</option>
                                <option value=04>04</option>
                                <option value=05>05</option>
                                <option value=06>06</option>
                                <option value=07>07</option>
                                <option value=08>08</option>
                                <option value=09>09</option>
                                <option value=10>10</option>
                                <option value=11>11</option>
                                <option value=12>12</option>
                                <option value=13>13</option>
                                <option value=14>14</option>
                                <option value=15>15</option>
                                <option value=16>16</option>
                                <option value=17>17</option>
                                <option value=18>18</option>
                                <option value=19>19</option>
                                <option value=20>20</option>
                                <option value=21>21</option>
                                <option value=22>22</option>
                                <option value=23>23</option>
                                <option value=24>24</option>
                                <option value=25>25</option>
                                <option value=26>26</option>
                                <option value=27>27</option>
                                <option value=28>28</option>
                                <option value=29>29</option>
                                <option value=30>30</option>
                                <option value=31>31</option>
                            </select>
                            <select name=endyear size=1>
                                <option value=$endyear selected>$endyear</option>";

        $nextyear = $thisyear + 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>";

        $nextyear += 1;

        echo "<option value=$nextyear>$nextyear</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign=top>
                        <td class=even align=right>
                            Comments:
                        </td>
                        <td class=odd>
                            <textarea cols=40 rows=4 name=comments>$Ucomments</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th align=center colspan=2>
                            <input class=button value=Restore type=reset> <input class=button type=submit value=\"Update Project\">
                        </th>
                    </tr>
                </table>
                <input type=hidden name=util_op value=editprojectaction><input type=hidden name=id value=$id>
            </form>";
    }

    function editProjectAction($id, $name, $startmonth, $startday, $startyear, $endmonth, $endday, $endyear, $comments)
    {
        // This function receives variables from "editproject()" and updated

        // the project in the database. Then it tells you if it worked or not.

        global $xoopsDB;

        global $_XOOPS_project_manager_projects_table;

        $startdate = "$startyear-$startmonth-$startday";

        $enddate = "$endyear-$endmonth-$endday";

        // Format DB input

        if ('' == $name) {
            $name = '<No Project Name Given>';
        }

        if ('' == $comments) {
            $comments = '<No Description Given>';
        }

        $name = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);

        $comments = htmlspecialchars($comments, ENT_QUOTES | ENT_HTML5);

        $result = $xoopsDB->queryF("UPDATE $_XOOPS_project_manager_projects_table SET name='$name', startdate='$startdate', enddate='$enddate', comments='$comments' WHERE id=$id");

        if ($result) {
            return "Project \"$name\" Updated Successfully";
        }

        return 'Error Updating Project';
    }
