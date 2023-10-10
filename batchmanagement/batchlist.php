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
global $DB,$USER,$CFG;
$userid = optional_param('college', null, PARAM_INT);
$userid = !empty($userid) ? $userid : $USER->id;

$context = context_system::instance();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('custom');
$PAGE->set_url('/local/batchmanagement/assignuser.php', ['college' => $userid]);
$PAGE->navbar->add(get_string('addbatch', 'local_batchmanagement'), new moodle_url('/local/batchmanagement/index.php'));
$PAGE->navbar->add(get_string('batchlist', 'local_batchmanagement'));
$PAGE->set_title(get_string('batchlist', 'local_batchmanagement'));
$PAGE->set_heading(get_string('batchlist', 'local_batchmanagement'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/hiringcompany/css/hiring_custom.css'));

echo $OUTPUT->header();
if (has_capability('local/usermanagment:college', context_system::instance())) {
	$get_myadmin = $DB->get_record('college',array('userid'=>$USER->id),'createdby');
	$student_programme = $DB->get_records_sql("SELECT * FROM {student_programme} WHERE deleted=0 AND createdby=".$get_myadmin->createdby." ORDER BY fullname");
}else{
	$student_programme = $DB->get_records_sql("SELECT * FROM {student_programme} WHERE deleted=0 AND createdby IN (" . $cteatedbyStr . ") ORDER BY fullname");
}
$stream = $DB->get_records_sql("SELECT * FROM {student_stream} WHERE deleted=0 AND createdby=".$get_myadmin->createdby." ORDER BY fullname");
$html = '';
$html .='<div class="d-flex align-items-center mb-5 mobile-filters">
<div class="font-weight-bold">Select :</div>
<div class="ml-20">
<select class="career-select-filter" id="programme" name="programme">
<option value="all">Program</option>
';
if(!empty($student_programme)){
	foreach ($student_programme as $key => $stdprogramme) {
		$html .='<option value="'.$stdprogramme->id.'">'.$stdprogramme->fullname.'</option>';
	}
}
$html .='</select>
</div>
<div class="ml-20">
<select class="career-select-filter" id="stream" name="stream" >
<option value="" selected> Stream</option>
</select></div>
<div class="ml-20">
<select class="career-select-filter" id="semester" name="semester">&gt;
<option value="" selected>Semester</option>
</select>
</div>
<div class="ml-20">
<select class="career-select-filter" id="semesteryear" name="semesteryear" >
<option value="" selected>Year</option>
</select>
</div>
<div class="ml-20">
<input type="button" value="Search" class="btn-primary" value="Search" id="search">
</div>
</div>';
echo $html;
$PAGE->requires->js_call_amd('local_batchmanagement/batchmigrationfilter', 'init');
$PAGE->requires->js_call_amd('local_batchmanagement/datatable', 'init');
echo '<div id="pagedata">
</div>';
echo $OUTPUT->footer();
