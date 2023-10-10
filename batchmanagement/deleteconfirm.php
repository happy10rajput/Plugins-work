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

$id = optional_param('id','',PARAM_INT);
$courseid = optional_param('courseid','',PARAM_INT);
$cohortid = optional_param('cohortid','',PARAM_INT);
//Setting the coures context.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($CFG->wwwroot.'/local/batchmanagement/deleteconfirm.php');
global $DB,$CFG;
require_login();
//for delete and cancel url//
$deleteurl = new moodle_url('/local/batchmanagement/deletebatch.php', array('deleteid' => $id,'flag'=>2));
$message = get_string("deletecatinfo",'local_batchmanagement');

$continuebutton = new single_button($deleteurl, get_string('deleteconfirm','local_batchmanagement'), 'post');
$cancelurl = new moodle_url('/local/batchmanagement/batchlist.php');
echo $OUTPUT->header();
echo $OUTPUT->confirm($message, $continuebutton,$cancelurl);
echo $OUTPUT->footer();
exit;