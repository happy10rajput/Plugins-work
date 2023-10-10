<?php

require_once('../../config.php');
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once('lib.php');
global $DB, $USER, $CFG;
$sort='bd.timecreated';
if(isset($_POST["program"] ,$_POST["stream"] ,$_POST['semester'] ,$_POST['semesteryear'])) {
	$pid = $_POST["program"];  
	$sm =$_POST["stream"]; 	 
	$smster = $_POST["semester"];  
	$smyear = $_POST["semesteryear"];  
	$sql = "SELECT  bd.id,  bd.batchname as batchname, bd.batchcode as batchcode, sp.fullname as program, ss.fullname as stream, sse.semester as semester, sy.semester as year, bd.cohortid as cohortid, bd.createdby, bd.timecreated
	FROM {batch_details} as bd 
	JOIN {student_programme} as sp ON bd.programme = sp.id
	JOIN {student_stream} as ss ON bd.stream = ss.id
	JOIN {student_semester} as sse ON bd.semester = sse.id
	JOIN {student_sem_year} as sy ON bd.semyear = sy.id
	WHERE bd.createdby=".$USER->id. " and sp.id=".$pid. " and ss.id=".$sm. " and sse.id=".$smster." and sy.id=".$smyear." ORDER BY $sort"  ;  
}/*else{
	if (is_siteadmin()) {		
		$sql = "SELECT bd.id,  bd.batchname as batchname, bd.batchcode as batchcode, sp.fullname as program, ss.fullname as stream, sse.semester as semester, sy.semester as year, bd.cohortid as cohortid, bd.createdby, bd.timecreated
		FROM {batch_details} as bd 
		JOIN {student_programme} as sp ON bd.programme = sp.id
		JOIN {student_stream} as ss ON bd.stream = ss.id
		JOIN {student_semester} as sse ON bd.semester = sse.id
		JOIN {student_sem_year} as sy ON bd.semyear = sy.id
		WHERE bd.createdby=".$USER->id;
	}else{
		$sql = "SELECT  DISTINCT(bd.batchname),bd.id,  bd.batchname as batchname, bd.batchcode as batchcode, sp.fullname as program, ss.fullname as stream, sse.semester as semester, sy.semester as year, bd.cohortid as cohortid, bd.createdby, bd.timecreated
		FROM {batch_details} as bd 
		JOIN {student_programme} as sp ON bd.programme = sp.id
		JOIN {student_stream} as ss ON bd.stream = ss.id
		JOIN {student_semester} as sse ON bd.semester = sse.id
		JOIN {student_sem_year} as sy ON bd.semyear = sy.id
		WHERE bd.createdby=".$USER->id;
	}
	
}*/
$results = $DB->get_records_sql($sql);
$c = 0;
if(!empty($results)){
	$table = new html_table();
	$table->id =  'example1';
	$table->head = (array) get_strings(array('sno', 'batchname','batchcode','programme','stream','semester','semesteryear','action','courses'), 'local_batchmanagement');
	$i = 1;
	
	foreach($results as $result) {
		$users = '';

		$cohortDetails = $DB->get_records('cohort_members', ['cohortid' => $result->cohortid]);
		$count = 0;
		if (!empty($cohortDetails)) {

			foreach ($cohortDetails as $cohortDetail) {

				$userrec = $DB->get_record('user', ['id' => $cohortDetail->userid, 'deleted' => 0]);
				if ($userrec) {
					$usertyperec = $DB->get_record('usertype', ['userid' => $cohortDetail->userid, 'deleted' => 0]);
				}
				if ($usertyperec && $userrec) {
					$count++;
				}
			}
			if ($count == 0) {
				$username = $count;
			} else {
				$username = $count;
			}
		} else {
			$username = 0;
		}
		$url = new moodle_url($CFG->wwwroot.'/local/batchmanagement/listusers.php',array('batchid'=>$result->id,'cohortid'=>$result->cohortid));
		$ausers = '<div class="btn btn-primary" id="viewaUsers" data-value="#"><a href="'.$url.'" style="color:#fff;">View <span class="badge badge-success">' .$username.'</span></a></div>'; 
		$buttons = $ausers;

		$edit = '<span id="emty_sec'.$result->id.'">'.$result->batchname.'<a class="quickediticon" data-value="'.$result->batchname.'--'.$result->id.'"><span class="visibleifjs"><i class="icon fa fa-pencil fa-fw"></i></span></a></span>';

		$viewurl = new moodle_url($CFG->wwwroot.'/local/batchmanagement/course_list.php',array('batchid'=>$result->id,'cohortid'=>$result->cohortid));
		$view = '<div class="btn btn-primary" id="viewaUsers" data-value="#"><a href="'.$viewurl.'" style="color:#fff;">View</a></div>'; 
		$table->data[] = array(
			$i,
			$edit,
			$result->batchcode,
			$result->program,
			$result->stream,
			$result->semester,
			$result->year,
			$buttons,
			$view	
		);
		$i++;
		$c++;
	}
	echo "<hr><br>";
	echo '<strong><p>Total Batches '.$c.' </p></strong>';
	echo html_writer::table($table);
}else{
	echo "<hr><br>";
	echo '<strong><p>Total Baches '.$c.' </p></strong>';
	echo '<div class="alert alert-danger">No data to display for the selected values. </div>';
}


?>
<style type="text/css">
	.row {
		margin: 1%!important;
	}
</style>