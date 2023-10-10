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

$schemeid = optional_param('schemeid', null, PARAM_RAW);
$schadmn = optional_param('schadmn', '', PARAM_BOOL);
$pageurl = new moodle_url('/local/schememanagement/courses_and_partners.php?schemeid='.$schemeid);
$partner = array_values($DB->get_records('local_course_partners', array(), 'partner_name ASC'));
$schemename = $DB->get_record('schemedata', array('schemeid' => $schemeid));
if ($schadmn === 0) {
    $sql = "SELECT DISTINCT c.fullname,c.id,cp.partner_name,cp.id as cpid,sm.id as smid,sm.batchsize as batch_size
            FROM {course} c JOIN {schememangement_mapping} sm ON c.id=sm.courseid
            JOIN {local_course_partners} cp ON cp.id=sm.cpid WHERE sm.schemeid='$schemeid' AND sm.sch_own=1";
            $allocate=1;
} else if ($schadmn === 1) {
    $sql = "SELECT DISTINCT c.fullname,c.id,cp.partner_name,cp.id as cpid,sm.id as smid,sm.batchsize as batch_size
            FROM {course} c JOIN {schememangement_mapping} sm ON c.id=sm.courseid
            JOIN {local_course_partners} cp ON cp.id=sm.cpid WHERE sm.schemeid='$schemeid'";
            $allocate=0;

} else {
    $sql = "SELECT DISTINCT c.fullname,c.id,cp.partner_name,cp.id as cpid,sm.id as smid,sm.batchsize as batch_size
    FROM {course} c
            JOIN {schememangement_mapping} sm ON c.id=sm.courseid
            JOIN {local_course_partners} cp ON cp.id=sm.cpid WHERE sm.schemeid='$schemeid' AND sm.sch_own=0";
            $allocate=1;
}

$record = array_values($DB-> get_records_sql($sql));
foreach ($record as $index => $row) {
    $record[$index]->counter = $index + 1;
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('custom');
$PAGE->set_url($pageurl);
$scheme_head = get_string('coursepage', 'local_schememanagement'); 

$PAGE->set_heading($scheme_head. " - ". $schemename->name);
$PAGE->navbar->add($scheme_head);
$PAGE->set_title($scheme_head);
echo $OUTPUT->header();

$hash = array(
    'cfg' => $CFG->wwwroot,
    'pageurl' => $pageurl,
    'coursepartner' => $partner,
    'record' => $record,
    'allocate' => $allocate
);
echo $OUTPUT->render_from_template('local_schememanagement/courses_and_partners', $hash);
$PAGE->requires->js_call_amd('local_schememanagement/courses_and_partners', 'init', ['schemeid' => $schemeid, 'schadmn' => $schadmn]);
echo $OUTPUT->footer();