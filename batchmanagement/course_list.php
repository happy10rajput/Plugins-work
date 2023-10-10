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
$batchid = required_param('batchid', PARAM_INT);
$cohortid = required_param('cohortid', PARAM_INT);

$context = context_system::instance();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/batchmanagement/course_list.php', ['batchid' => $batchid, 'cohortid' => $cohortid]);
$alre_sql = 'SELECT * FROM {batch_details} WHERE id = '.$batchid;
$exist = $DB->get_record_sql($alre_sql);
$PAGE->navbar->add(get_string('batchlist', 'local_batchmanagement'), new moodle_url('/local/batchmanagement/batchlist.php'));
$PAGE->navbar->add($exist->batchname);
$PAGE->set_title('Mapped Courses:'.$exist->batchname);
$PAGE->set_heading('Mapped Courses:'.$exist->batchname);
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/hiringcompany/css/hiring_custom.css'));

echo $OUTPUT->header();

$ctb_sql = 'SELECT * FROM {course_to_batch} WHERE batchid = '.$batchid;
$ctbs = $DB->get_records_sql($ctb_sql);
$i = 0;
foreach ($ctbs as $ctb) {

	// $course = get_course($ctb->courseid);
	$course_object =  $DB->get_record('course',array('id'=>$ctb->courseid));
	$catnumber =  $DB->get_record('course_categories',array('id'=>$course_object->category));
	$usre_object =  $DB->get_record('user',array('id'=>$ctb->createdby));

	if ($course_object) {

		$i++;

		$data = new stdClass();
		$data->i = $i;
		$data->course_category = $catnumber->name;
		$data->course_code = $course_object->shortname;
		$data->course_name = $course_object->fullname;
		$data->course_mapped_on = userdate($ctb->timecreated, get_string('strftimedatetime', 'core_langconfig'));
		$data->course_mapped_by = fullname($usre_object);
		$data->visibility = $course_object->visible == 1 ? 'Visible' : 'Hidden';

		$course_data[] = $data;

		
	}
}

$url = new moodle_url($CFG->wwwroot.'/local/batchmanagement/batchlist.php');
$hash = array(
	'course_data' => $course_data,
	'course_count' => $i,
	'url'=>$url
);

echo $OUTPUT->render_from_template('local_batchmanagement/course_list', $hash);
$PAGE->requires->js_call_amd('local_batchmanagement/datatable', 'init');
echo $OUTPUT->footer();
