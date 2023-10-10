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

/**
 * Handles uploading files
 */

require_once('../../config.php');

global $CFG,$COURSE;
require_once($CFG->dirroot. "/lib/enrollib.php");

$id = required_param('deleteid',PARAM_INT);
/*$courseid = required_param('courseid',PARAM_INT);
$cohortid = required_param('cohortid',PARAM_INT);*/

//Setting the coures context.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($CFG->wwwroot.'/local/batchmanagement/deletebatch.php');
global $DB,$CFG;
require_login();
//for delete and cancel url//
$DB->delete_records('cohort_members', array('id'=>$id));
$redirecrurl = new moodle_url('/local/batchmanagement/batchlist.php');


//unenrolements 
$batchnumber =  $DB->get_record('cohort_members',array('id'=>$id));
redirect($redirecrurl);
echo $OUTPUT->header();
echo $OUTPUT->footer();
