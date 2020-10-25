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

    require dirname(__DIR__, 3) . '/include/cp_header.php';
    include '../../../mainfile.php';
    xoops_cp_header();

    // Local includes have to come after call to xoops_cp_header()
    include 'adminfunctions.php';
    include '../common.php';

    extract($_GET);
    extract($_POST);

    projectManagerHeader();

    if (!isset($op)) {
        $op = '';
    }

    switch ($op) {
        case 'useradmin':
        userList('');
        break;
        case 'projectadmin':
        listProjects(0);
        listProjects(1);
        break;
        case 'addproject':
        addProject();
        break;
        case 'confirmprojectdelete':
        confirmProjectDelete($id);
        break;
        case 'editproject':
        editProject($id);
        break;
        case 'useraddaction':
        userList(userAddAction($user));
        break;
        case 'confirmdeleteuser':
        confirmDeleteUser($id);
        break;
        case 'deleteuseraction':
        userList(deleteUserAction($id));
        break;
        case 'listprojects':
        listProjects(0);
        listProjects(1);
        break;
    }

    xoops_cp_footer();
