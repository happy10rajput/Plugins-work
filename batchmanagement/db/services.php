<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// We defined the web service functions to install.
defined('MOODLE_INTERNAL') || die();
$functions = array(
    'local_batchmanagement_add_batch' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'add_batch',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to create a Batch/Cohort',
        'type' => 'write',
        'ajax' => true
    ),
    'local_batchmanagement_fetch_stream' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'fetch_stream',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to fetch stream',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false
    ),
    'local_batchmanagement_fetch_semester' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'fetch_semester',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to fetch semester',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false
    ),
    'local_batchmanagement_fetch_semester_year' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'fetch_semester_year',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to fetch semester year',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false
    ),
    'local_batchmanagement_fetch_batch' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'fetch_batch',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to fetch batch',
        'type' => 'read',
        'ajax' => true
    ),
    'local_batchmanagement_fetch_users' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'fetch_users',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to fetch users',
        'type' => 'read',
        'ajax' => true
    ),
    'local_batchmanagement_assign_users' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'assign_users',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to assign users to batch',
        'type' => 'write',
        'ajax' => true
    ),
    'local_batchmanagement_unassign_users_frombatch' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'unassign_users_frombatch',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'Remove users from batch',
        'type' => 'write',
        'ajax' => true
    ),
    'local_batchmanagement_view_assigned_user' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'view_assigned_user',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'Get assigned users modal',
        'type' => 'write',
        'ajax' => true
    ),
    'local_batchmanagement_batch_migration' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'batch_migration',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'Batch MIgration',
        'type' => 'write',
        'ajax' => true
    ),
    'local_batchmanagement_fetch_batchall' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'fetch_batchall',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to fetch all batch',
        'type' => 'read',
        'ajax' => true
    ),'local_batchmanagement_fetch_semester_year_new' => array(
        'classname' => 'local_batchmanagement_external',
        'methodname' => 'fetch_semester_year_new',
        'classpath' => 'local/batchmanagement/externallib.php',
        'description' => 'This function is using to fetch semester year',
        'type' => 'read',
        'ajax' => true
    )

);
// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Batch Management Services' => array(
        'functions' => array(
            'local_batchmanagement_add_batch',
            'local_batchmanagement_fetch_stream',
            'local_batchmanagement_fetch_semester',
            'local_batchmanagement_fetch_semester_year',
            'local_batchmanagement_fetch_batch',
            'local_batchmanagement_fetch_users',
            'local_batchmanagement_assign_users',
            'local_batchmanagement_unassign_users_frombatch',
            'local_batchmanagement_view_assigned_user',
            'local_batchmanagement_batch_migration',
            'local_batchmanagement_fetch_batchall',
            'local_batchmanagement_fetch_semester_year_new',
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);