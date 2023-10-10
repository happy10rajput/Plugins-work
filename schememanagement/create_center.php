<?php
require_once("$CFG->libdir/formslib.php");


class Create_center extends moodleform {
    //Add elements to form
    public function definition() {
        global $DB,$CFG,$USER;
        $mform = $this->_form;
        $schemeid = $this->_customdata['schemeid'];
        $courseid = $this->_customdata['courseid'];
        $courses = [
            '' => 'Select Course',
        ];
        $districts = [
            '' => 'Select District',
        ];
        $sql = "SELECT c.id,c.fullname,sm.cpid FROM {course} c JOIN {center_status} cs ON c.id=cs.courseid JOIN 
                    {schememangement_mapping} sm ON sm.courseid=cs.courseid join {local_course_partners} lc 
                    on sm.cpid=lc.id WHERE sm.schemeid='$schemeid' AND c.id=$courseid AND
                    (lc.partner_name='$USER->username' 
                OR  lc.partner_name = '" . $USER->firstname . " " . $USER->lastname . "') ORDER BY c.fullname ASC";
        $record = array_values($DB->get_records_sql($sql));
        
        foreach ($record as $key => $value) {
            $courses[$value->id] = $value->fullname;
        }
        $distrcitvalue = $DB->get_records('districts', array(), 'name ASC', 'id,name');
        foreach ($distrcitvalue as $key => $value) {
            $districts[$value->id] = $value->name;
        }
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('select', 'choosecourse', 'Course Name:', $courses, array('id' => 'choosecourse'));
        $mform->setType('choosecourse', PARAM_INT);
        $mform->addRule('choosecourse', 'Course can not be empty', 'required', null, 'client');
        $mform->addElement('select', 'choosedistrict', 'District Name:', $districts, array('id' => 'choosedistrict'));
        $mform->setType('choosedistrict', PARAM_INT);
        $mform->addRule('choosedistrict', 'District can not be empty', 'required', null, 'client');
        
        $mform->addElement('select', 'chooseconst', 'Choose Constituency:', '', array('id' => 'chooseconst'));
        $mform->setType('chooseconst', PARAM_INT);
        $mform->addRule('chooseconst', 'Constituency can not be empty', 'required', null, 'client');
       
        $mform->addElement('text', 'choosecenter', 'Enter Center Name:', '', array('id' => 'choosecenter'));
        $mform->setType('choosecenter', PARAM_RAW);
        $mform->addRule('choosecenter', 'Center is required', 'required', null, 'client');

        $mform->addElement('text', 'batch', 'Batch Size:', null, array('value' => '','id' => 'batch'));
        $mform->addRule('batch', 'Batch size is required', 'required', null, 'client');
        $mform->setType('batch', PARAM_TEXT);
        $mform->addElement('filemanager', 'myfile', 'Upload Image:', null, ['subdirs' => 0, 'maxbytes' => 204800, 'areamaxbytes' => 2097152, 'maxfiles' => 10, 'accepted_types' => array('.png', '.gif', '.jpg', '.jpeg'),'return_types' => FILE_INTERNAL | FILE_EXTERNAL,]);
        $mform->addRule('myfile', 'Image is mandatory', 'required', null, 'client');
        $mform->setType('myfile', PARAM_FILE);
        $mform->addElement('hidden', 'schemeid', $schemeid);
        $mform->setType('schemeid', PARAM_RAW);
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
        $this->add_action_buttons(array('id' => 'centercreate'), 'Create');

    }
    //Custom validation should be added here
    function validation($data, $files) {
        global $USER;
        $errors = array();
        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        $draftitemid = file_get_submitted_draft_itemid('myfile');
        $files = $fs->get_area_files($usercontext->id, 'local_schememanagement', 'center_creation', $draftitemid);
       
        foreach ($files as $file) {
            $filename = $file->get_filename();
            if ($filename != '.') {
                $errors['myfile'] = 'Image is mandatory';
                return $errors;
            }
        }

        return $errors;
    }
}
