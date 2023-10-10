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
require_login();
$schemeid = optional_param('schemeid', null, PARAM_RAW);
$pageurl = new moodle_url('/local/schememanagement/view_mapping.php?schemeid='.$schemeid);
$schemename = $DB->get_record('schemedata', array('schemeid' => $schemeid));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('custom');
$PAGE->set_url($pageurl);
    $sql = "SELECT DISTINCT c.id,c.fullname,
                            sm.batchsize,
                            s.name as schemename,DATE_FORMAT(s.startdate, '%d/%m/%Y') as startdate,DATE_FORMAT(s.enddate, '%d/%m/%Y') as enddate,
                            cd.mode_id,CASE WHEN EXISTS (SELECT 1 FROM {center_status} cs WHERE cs.courseid=c.id AND cs.schemeid='$schemeid') THEN 1 ELSE 0 END AS is_mapped
                            FROM {course} c 
                            JOIN {schememangement_mapping} sm ON FIND_IN_SET(c.id, sm.courseid) 
                            JOIN {local_course_partners} lc ON sm.cpid=lc.id 
                            JOIN {schemedata} s ON s.schemeid=sm.schemeid 
                            JOIN {center_details} cd ON c.id=cd.courseid
                        WHERE (lc.partner_name='$USER->username' 
                            OR  lc.partner_name = '".$USER->firstname." ".$USER->lastname."')
                            AND s.schemeid='$schemeid'";
$coursevalues = array_values($DB->get_records_sql($sql));

foreach ($coursevalues as $index => $row) {
    if ($row->mode_id==1) {
        $coursevalues[$index]->mode_online = 1;
    } else {
        $coursevalues[$index]->mode_online = 0;
    }   
    $coursevalues[$index]->counter = $index + 1;
}
$scheme_head = get_string('viewmapping', 'local_schememanagement'); 
$PAGE->navbar->add($scheme_head);
$PAGE->set_title($scheme_head);
$PAGE->set_heading($schemename->name);
echo $OUTPUT->header();
$hash =array(
    'cfg' => $CFG->wwwroot,
    'pageurl' => $pageurl,
    'coursevalues' => $coursevalues,
    'href' => 'coursecenter.php',
    'schemeid' => $schemeid,
);
echo $OUTPUT->render_from_template('local_schememanagement/viewmapping', $hash);
$PAGE->requires->js_call_amd('local_schememanagement/center_status', 'init', array('schemeid' => $schemeid));
echo $OUTPUT->footer();


