<?php
require_once('../../config.php');
include_once(__DIR__ .'/lib.php');
require_login();
// Check permissions.
$context = context_system::instance();
$batchid = required_param('batchid',PARAM_INT);
$cohortid = required_param('cohortid',PARAM_INT);

$title = 'Batch Students List';
$PAGE->set_url('/local/batchmanagement/listusers.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->requires->jquery();
echo $OUTPUT->header();
$PAGE->requires->js_call_amd('local_batchmanagement/datatable', 'init');
//$permission = get_atalrolenamebyid($USER->msn);
$admin = is_siteadmin();
global $USER, $CFG, $DB,$OUTPUT;

//$courseid = 415;
$content = '';
$url = new moodle_url($CFG->wwwroot.'/local/batchmanagement/batchlist.php');
echo '<a href="'.$url.'"><div class="ml-20">
<input type="submit" class="btn-primary" value="Back to Batch list page" id="search">
</div></a><br><br>';
$table = new html_table(array('class'=>'table12','id'=>'pagination_data'));
$table->attributes = array('class' => 'generaltable bLists', 'id' => 'bList');

$table->id = 'example1';
$table->head = ['Sl. No.','User FirstName','User LastName','User Email','Action'];
$page = '';  
$output = '';
$i= 1;
$cohortDetails = $DB->get_records('cohort_members', ['cohortid' => $cohortid]);
if(!empty($cohortDetails)){
	foreach ($cohortDetails as $key => $cohortDetail) {
		# code...
		$user = $DB->get_record('user',array('id'=>$cohortDetail->userid));
		$table->data[] = array(
			$i,
			$user->firstname,
			$user->lastname,
			$user->email,
			html_writer::link(
				new moodle_url(
					$CFG->wwwroot.'/local/batchmanagement/deleteconfirm.php',
					array(
						'id'=>$cohortDetail->id,
						'delete'=>1
					)
				),
				'Delete',array('class'=>'btn btn-sm btn-danger text-white')
			)
		);
		$i++;
	}
	$tabeled = html_writer::table($table);
	echo $tabeled;
}
/*if(!empty($results)){
	$i=1;
	foreach ($results as $key => $result) {

		$user = $DB->get_record('user',array('id'=>$result->user_id));
		$table->data[] = array(
			$i,
			$user->firstname,
			$user->lastname,
			$user->email
		);
		$i++;
	}
	$tabeled = html_writer::table($table);
	echo $tabeled;
}else{
	echo '<div class="alert alert-danger" role="alert">
	No Records Found!.
	</div>';
}*/
/*}else{
	echo '<div class="alert alert-danger" role="alert">
	Access is denied!.
	</div>';
}*/

echo $OUTPUT->footer();
?>