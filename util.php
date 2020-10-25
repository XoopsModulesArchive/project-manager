<?php

include '../../mainfile.php';
    include 'functions.php';
    include 'common.php';

    extract($_GET);
    extract($_POST);

    if (isset($util_op)) {
        switch ($util_op) {
            case 'addtaskaction':
            $message = addTaskAction($id, $title, $hours, $priority, $description, $creator, $person, $notify, $billable, $parent_id);
            if ('mytasks' == (string)$return) {
                $redirect_url = 'index.php';
            } else {
                $redirect_url = "index.php?op=projectdetail&id=$id";
            }
            break;
            case 'deletetaskaction':
            $message = deleteTaskAction($task_id, $project_id);
            $redirect_url = "index.php?op=projectdetail&id=$project_id";
            break;
            case 'edittaskaction':
            $message = editTaskAction($task_id, $id, $title, $hours, $priority, $description, $person, $billable, $parent_id);
            $redirect_url = "index.php?op=viewtask&task_id=$task_id";
            break;
            case 'removeuseraction':
            $message = removeUserAction($task_id);
            $redirect_url = 'index.php';
            break;
            case 'addcommentaction':
            $message = addCommentAction($task_id, $comment);
            $redirect_url = "index.php?op=viewtask&task_id=$task_id";
            break;
            case 'editcommentaction':
            $message = editCommentAction($comment_id, $comment, $task_id);
            $redirect_url = "index.php?op=viewtask&task_id=$task_id";
            break;
            case 'deletecommentaction':
            $message = deleteCommentAction($comment_id, $task_id);
            $redirect_url = "index.php?op=viewtask&task_id=$task_id";
            break;
            case 'completeprojectaction':
            $message = completeProjectAction($id);
            $redirect_url = 'index.php?op=listprojects&type=completed';
            break;
            case 'reactivateprojectaction':
            $message = reactivateProjectAction($id);
            $redirect_url = 'index.php?op=listprojects&type=active';
            break;
            default:
            $redirect_url = 'index.php';
        } //switch

        redirect_header($redirect_url, 3, $message);
    } //if util_op is set
