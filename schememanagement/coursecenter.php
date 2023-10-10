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


require_once(__DIR__.'/../../config.php');
require_once('create_center.php');
$courseid = optional_param('courseid', null, PARAM_INT);
$schemeid = optional_param('schemeid', null, PARAM_RAW);
$pageurl = new moodle_url('/local/schememanagement/coursecenter.php?schemeid='.$schemeid.'&courseid='.$courseid);
$sql = "SELECT c.id,c.fullname FROM {course} c JOIN {center_status} cs ON c.id=cs.courseid";
$record = array_values($DB->get_records_sql($sql));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('custom');
$PAGE->set_url($pageurl);
$scheme_head = get_string('center', 'local_schememanagement'); 
$PAGE->set_heading($scheme_head);
$PAGE->navbar->add($scheme_head);
$PAGE->set_title($scheme_head);
echo $OUTPUT->header();
$mform = new Create_center(null, array('schemeid' => $schemeid, 'courseid' => $courseid));

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/schememanagement/schemes.php'));
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data(false)) {
    // print_object($fromform);exit;
    $recordexist = $DB->get_record('center_creation', array('courseid' => $_REQUEST['choosecourse'], 'districtid' => $_REQUEST['choosedistrict'], 'constid' => $_REQUEST['chooseconst'], 'schemeid' => $schemeid), 'id');
    if ($recordexist) {
        $data = new stdClass();
        $data->id = $recordexist->id;
        $data->batchsize = $_REQUEST['batch'];
        $data->image = $_REQUEST['myfile'];
        $data->userid = $USER->id;
        $data->schemeid = $fromform->schemeid;
        $data->createdby = $USER->id;
        $data->createdtime = time();
        $DB->update_record('center_creation', $data);
    } else {
        $data = new stdClass();
        $data->courseid = $_REQUEST['choosecourse'];
        $data->districtid = $_REQUEST['choosedistrict'];
        $data->constid = $_REQUEST['chooseconst'];
        $data->center = $_REQUEST['choosecenter'];
        $data->batchsize = $_REQUEST['batch'];
        $data->image = $_REQUEST['myfile'];
        $data->schemeid = $fromform->schemeid;
        $data->userid = $USER->id;
        $data->createdby = $USER->id;
        $data->createdtime = time();
        try{
            $DB->insert_record('center_creation', $data);
        }catch(Exception $e){
            print_object($e);
        }
    }
    $maxbytes = 5000000;
    file_save_draft_area_files($_REQUEST['myfile'], context_user::instance($USER->id)->id, 'local_schememanagement', 'center_creation', $_REQUEST['myfile'], array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 10));
    $url = new moodle_url('/local/schememanagement/view_mapping.php?schemeid='.$fromform->schemeid);
    $message = 'Center Created Successfully';
    redirect($url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
    //In this case you process validated data. $mform->get_data() returns data posted in form.
} 
$mform->display();


$hash =array(
    'cfg' => $CFG->wwwroot,
    'pageurl' => $pageurl,
    'record' => $record,   
    'href' => 'coursecenter.php',
);
$PAGE->requires->js_call_amd('local_schememanagement/center_status', 'init');
echo $OUTPUT->footer();