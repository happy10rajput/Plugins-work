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
global $DB, $USER, $CFG;
$userid = optional_param('college', null, PARAM_INT);
$userid = !empty($userid) ? $userid : $USER->id;

$context = context_system::instance();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('custom');
$PAGE->set_url('/local/batchmanagement/batchmigration.php', ['college' => $userid]);
$PAGE->navbar->add(get_string('batch_list', 'local_batchmanagement'), new moodle_url('/local/batchmanagement/batchlist.php'));
$PAGE->navbar->add(get_string('batchmigration', 'local_batchmanagement'));
$PAGE->set_title(get_string('batchmigration', 'local_batchmanagement'));
$PAGE->set_heading(get_string('batchmigration', 'local_batchmanagement'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/hiringcompany/css/hiring_custom.css'));
echo $OUTPUT->header();
// $student_programme = $DB->get_records('student_programme', ['deleted' => 0]);
// can see the lists createdby College, SA, IA (if mapped).
$admins = get_admins();
foreach ($admins as $admin) {
    $cArr[] = $admin->id;
}
$cmapp = college_mapto_industry($USER->id);
// $indusArr[] = !empty($cmapp) ? $cmapp : 0;
$indusArr = !empty($cmapp) ? explode(',', $cmapp) : [];
$collegeArr[] = $USER->id;
$createdbyArr = array_merge($cArr, $indusArr, $collegeArr);
$cteatedbyStr = implode(',', $createdbyArr);
if (has_capability('local/usermanagment:college', context_system::instance())) {
    $get_myadmin = $DB->get_record('college',array('userid'=>$USER->id),'createdby');
    $student_programme = $DB->get_records_sql("SELECT * FROM {student_programme} WHERE deleted=0 AND createdby=".$get_myadmin->createdby." ORDER BY fullname");
}else{
    $student_programme = $DB->get_records_sql("SELECT * FROM {student_programme} WHERE deleted=0 AND createdby IN (" . $cteatedbyStr . ") ORDER BY fullname");
}
// If programme fullname is same then give createdby within bracket. abc(Admin User)
$temp = [];
$newPname = [];
foreach ($student_programme as $key => $value) {
    if (in_array($value->fullname, $temp)) {
        $newPname[$key]['fullname'] = $value->fullname . ' (' . rolbased_user_name($value->createdby) . ')';
        $newPname[$key]['id'] = $value->id;
        $newPname[$key]['createdby'] = $value->createdby;
        $matchs = array_keys($temp, $value->fullname);
        $keyval = $matchs[0];
        if (!isset($temp[$keyval.'fullname'])) {
            $newPname[$keyval]['fullname'] = $value->fullname . ' (' .  rolbased_user_name($newPname[$keyval]['createdby']) . ')';
            $temp[$keyval.'fullname'] = 1;
        }
    } else {
        $newPname[$key]['fullname'] = $value->fullname;
        $newPname[$key]['id'] = $value->id;
        $newPname[$key]['createdby'] = $value->createdby;
        $temp[$key] = $value->fullname;
    }
}

$hash = array(
	'batchcode' => rand(1000,100000),
    // 'student_programme' => array_values($student_programme)
	'student_programme' => array_values($newPname)
);
echo $OUTPUT->render_from_template('local_batchmanagement/batchmigration', $hash);
$PAGE->requires->js_call_amd('local_batchmanagement/batchmigration', 'init');

echo $OUTPUT->footer();
?>
<style type="text/css">
    #id_s_assignsubmission_file_filetypes:disabled, #id_s_assignsubmission_file_filetypes[readonly], .form-control:disabled, .form-control[readonly], input:disabled[type=text], input[readonly][type=text] {
        color: #fff !important;
        background-color: #474545 !important;
    }
</style>