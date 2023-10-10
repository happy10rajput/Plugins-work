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
$courseid = optional_param('courseid', null, PARAM_RAW);
$pageurl = new moodle_url('/local/schememanagement/training_centerapproval.php');
$sql = "SELECT cc.id,c.id as courseid,c.fullname,cc.center,cn.name as constname,d.name,cc.status,cc.reapply,sm.cpid FROM {course} c JOIN 
        {center_creation} cc ON c.id=cc.courseid join {districts} d  on d.id=cc.districtid
        join {constituencies} cn on cn.id=cc.constid join  {schememangement_mapping} sm ON sm.courseid=cc.courseid join {local_course_partners} lc 
        on sm.cpid=lc.id WHERE (lc.partner_name='$USER->username' 
        OR  lc.partner_name = '" . $USER->firstname . " " . $USER->lastname . "')
        AND (cc.reapply IS NOT NULL OR cc.status=1 OR status=2)";
       
$record = array_values($DB->get_records_sql($sql));

foreach ($record as $index => $row) {
    $record[$index]->counter = $index + 1;
    if ($row->status == "2") {
        $row->status = 0;
    }
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('custom');
$PAGE->set_url($pageurl);
$scheme_head = get_string('center_approval', 'local_schememanagement'); 
$PAGE->set_heading($scheme_head);
$PAGE->navbar->add($scheme_head);
$PAGE->set_title($scheme_head);
echo $OUTPUT->header();
$hash =array(
    'cfg' => $CFG->wwwroot,
    'pageurl' => $pageurl,
    'record' => $record,   
);
echo $OUTPUT->render_from_template('local_schememanagement/training_centerapproval', $hash);
$PAGE->requires->js_call_amd('local_schememanagement/center_status', 'init');
echo $OUTPUT->footer();