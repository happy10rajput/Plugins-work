<?php
require_once(__DIR__.'/../../config.php');
require_once('lib.php');
require_login();
global $DB, $USER, $CFG;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$url = new moodle_url($CFG->wwwroot.'/local/dashboard/scheme_dashboard.php');
$PAGE->set_url($url);
$addnewpromo = 'Scheme Dashboard';
$PAGE->navbar->add(fullname($USER));
$PAGE->set_title($addnewpromo);
$PAGE->set_heading($addnewpromo);

// echo $OUTPUT->header();

$hash = array(

);

echo $OUTPUT->render_from_template('local_schememanagement/scheme_dashboard', $hash);