<?php

include '../../../mainfile.php';
    include 'adminfunctions.php';
    include '../common.php';

    extract($_GET);
    extract($_POST);
    if (isset($util_op)) {
        switch ($util_op) {
            case 'addprojectaction':
            $message = addProjectAction($name, $startmonth, $startday, $startyear, $endmonth, $endday, $endyear, $comments);
            $redirect_url = 'index.php?op=listprojects';
            break;
            case 'editprojectaction':
            $message = editProjectAction($id, $name, $startmonth, $startday, $startyear, $endmonth, $endday, $endyear, $comments);
            $redirect_url = 'index.php?op=listprojects';
            break;
            case 'deleteprojectaction':
            $message = deleteProjectAction($id);
            $redirect_url = 'index.php?op=listprojects';
            break;
        }

        redirect_header($redirect_url, 3, $message);
    } else {
        echo 'what?';
    }
