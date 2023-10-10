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
$id = optional_param('id', null, PARAM_RAW);
$record = $DB->get_record('center_creation', array('id' => $id), 'userid,image');
$fs = get_file_storage();
$files = $fs->get_area_files(context_user::instance($record->userid)->id, 'local_schememanagement', 'center_creation', $record->image);
$file_data = array(); 
foreach ($files as $file) {
    $filename = $file->get_filename();
    if ($filename != '.') {
        $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
        $url = $url->out();
        $file_info = array(
            'filename' => $filename,
            'url' => $url
        );
        $file_data[] = $file_info;
        
    }
        
}
$pageurl = new moodle_url('/local/schememanagement/center_creation_admin.php?id='.$id);
$sql = "SELECT c.fullname,d.name as dname,cn.name as cname,cc.center,cc.batchsize FROM {course} c JOIN {center_creation} cc ON cc.courseid=c.id JOIN {districts} d 
ON cc.districtid=d.id JOIN {constituencies} cn ON cc.constid=cn.id WHERE cc.id=$id";
$record = $DB->get_record_sql($sql);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('custom');
$PAGE->set_url($pageurl);
$scheme_head = get_string('center_admin', 'local_schememanagement'); 
$PAGE->set_heading($scheme_head);
$PAGE->navbar->add($scheme_head);
$PAGE->set_title($scheme_head);
echo $OUTPUT->header();

$hash = array(
    'cfg' => $CFG->wwwroot,
    'pageurl' => $pageurl,
    'record' => $record,
    'fileinfo' => $file_data
);
echo $OUTPUT->render_from_template('local_schememanagement/center_creation_admin', $hash);
echo $OUTPUT->footer();