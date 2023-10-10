<?php

require_once('../../config.php');
// require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once('lib.php');
global $DB,$USER,$CFG;
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<?php

$delete       = optional_param('delete', 0, PARAM_INT);
    $confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
    $confirmuser  = optional_param('confirmuser', 0, PARAM_INT);
    $sort         = optional_param('sort', 'name', PARAM_ALPHANUM);
    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);
    $perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page
    $ru           = optional_param('ru', '2', PARAM_INT);            // show remote users
    $lu           = optional_param('lu', '2', PARAM_INT);            // show local users
    $acl          = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)
    $suspend      = optional_param('suspend', 0, PARAM_INT);
    $unsuspend    = optional_param('unsuspend', 0, PARAM_INT);
    $unlock       = optional_param('unlock', 0, PARAM_INT);
    $resendemail  = optional_param('resendemail', 0, PARAM_INT);

    // admin_externalpage_setup('editusers');

    $context = context_system::instance();
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('custom');
    $PAGE->set_url('/local/batchmanagement/assignuser.php', ['college' => $USER->id]);
    $PAGE->navbar->add(get_string('addbatch', 'local_batchmanagement'), new moodle_url('/local/batchmanagement/index.php'));
    $PAGE->navbar->add(get_string('batchlist', 'local_batchmanagement'));
    $PAGE->set_title(get_string('batchlist', 'local_batchmanagement'));
    $PAGE->set_heading(get_string('batchlist', 'local_batchmanagement'));
    $PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/hiringcompany/css/hiring_custom.css'));

    $site = get_site();
    $stredit   = get_string('edit');
    $strdelete = get_string('delete');
    $strdeletecheck = get_string('deletecheck');
    $strshowallusers = get_string('showallusers');
    $strsuspend = get_string('suspenduser', 'admin');
    $strunsuspend = get_string('unsuspenduser', 'admin');
    $strunlock = get_string('unlockaccount', 'admin');
    $strconfirm = get_string('confirm');
    $strresendemail = get_string('resendemail');

    $returnurl = new moodle_url('/local/batchmanagement/batch_list.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page'=>$page));

    // The $user variable is also used outside of these if statements.
    $user = null;

    // create the user filter form
    $ufiltering = new user_filtering();
    echo $OUTPUT->header();
    ?>
<!--     <div id="student_csv_pdf">
        <button class="btn btn-primary dt-buttons" type="button" id="generate_button">
          <span class="spinner-border-sm" role="status" aria-hidden="true"></span>
          Generate Downloadable Formats
      </button>
  </div> -->
<!--     <div id="student_csv_pdf">
        <button class="btn btn-primary dt-buttons" type="button">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Please wait ! Generating downloadable formats...
      </button>
  </div> -->
  <?php
    // Carry on with the user listing
  $context = context_system::instance();
    // These columns are always shown in the users list.
  $requiredcolumns = array('city', 'country', 'lastaccess');
    // Extra columns containing the extra user fields, excluding the required columns (city and country, to be specific).
  $extracolumns = get_extra_user_fields($context, $requiredcolumns);
    // Get all user name fields as an array.
  $allusernamefields = get_all_user_name_fields(false, null, null, null, true);
  $columns = array_merge($allusernamefields, $extracolumns, $requiredcolumns);

  foreach ($columns as $column) {
    $string[$column] = get_user_field_name($column);
    if ($sort != $column) {
        $columnicon = "";
        if ($column == "lastaccess") {
            $columndir = "DESC";
        } else {
            $columndir = "ASC";
        }
    } else {
        $columndir = $dir == "ASC" ? "DESC":"ASC";
        if ($column == "lastaccess") {
            $columnicon = ($dir == "ASC") ? "sort_desc" : "sort_asc";
        } else {
            $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
        }
        $columnicon = $OUTPUT->pix_icon('t/' . $columnicon, get_string(strtolower($columndir)), 'core',
            ['class' => 'iconsort']);

    }
    $$column = "<a href=\"user.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
}

    // We need to check that alternativefullnameformat is not set to '' or language.
    // We don't need to check the fullnamedisplay setting here as the fullname function call further down has
    // the override parameter set to true.
$fullnamesetting = $CFG->alternativefullnameformat;
    // If we are using language or it is empty, then retrieve the default user names of just 'firstname' and 'lastname'.
if ($fullnamesetting == 'language' || empty($fullnamesetting)) {
        // Set $a variables to return 'firstname' and 'lastname'.
    $a = new stdClass();
    $a->firstname = 'firstname';
    $a->lastname = 'lastname';
        // Getting the fullname display will ensure that the order in the language file is maintained.
    $fullnamesetting = get_string('fullnamedisplay', null, $a);
}

    // Order in string will ensure that the name columns are in the correct order.
$usernames = order_in_string($allusernamefields, $fullnamesetting);
$fullnamedisplay = array();
foreach ($usernames as $name) {
        // Use the link from $$column for sorting on the user's name.
    $fullnamedisplay[] = ${$name};
}
    // All of the names are in one column. Put them into a string and separate them with a /.
$fullnamedisplay = implode(' / ', $fullnamedisplay);
    // print_object($fullnamedisplay);
    // If $sort = name then it is the default for the setting and we should use the first name to sort by.
if ($sort == "name") {
        // Use the first item in the array.
    $sort = reset($usernames);
}





///added by sandip
if (is_siteadmin()) {
    //new filter value sending her 
    //pid is program id 
    list($extrasql, $params) = $ufiltering->get_sql_filter();
    $users = get_users_listing_custom_college($page*$perpage, $perpage,$USER->id,$extrasql, $params, $context);
    $usercount = count($users);
    $usersearchcount = get_users_listing_custom_record_count_college();

} else if (has_capability('local/usermanagment:collegesuperadmin', $context)) {

    $csaChildArr = collegesa_childs($USER->id);

    if (!empty($csaChildArr)) {
        $csaChildStr = implode(',', $csaChildArr);
        $extrasql = '';
        list($extrasql, $params) = $ufiltering->get_sql_filter();
        $users = get_users_listing_custom($page*$perpage, $perpage,$csaChildStr,$extrasql, $params, $context);
        $usercount = count($users);
        $usersearchcount = get_users_listing_custom_record_count($csaChildStr);
    }
} else if (has_capability('local/usermanagment:college', $context)) {

    $extrasql = '';
    list($extrasql, $params) = $ufiltering->get_sql_filter();
    $users = get_batch_listing_custom_college($page*$perpage, $perpage,$USER->id,$extrasql, $params, $context);
    $usercount = count($users);
    $usersearchcount = get_batch_listing_custom_record_count_college();



}

// echo $OUTPUT->heading("$usersearchcount / $usercount ".'Batches');
//$cmapp = college_mapto_industry($USER->id);
// $indusArr[] = !empty($cmapp) ? $cmapp : 0;
//$indusArr = !empty($cmapp) ? explode(',', $cmapp) : [];
//$collegeArr[] = $USER->id;
//$createdbyArr = array_merge($cArr, $indusArr, $collegeArr);
//$cteatedbyStr = implode(',', $createdbyArr);
$program = '';
$stream ='';
$semester = '';
$semesteryear = '';
$html = '';
if (has_capability('local/usermanagment:college', context_system::instance())) {
    $get_myadmin = $DB->get_record('college',array('userid'=>$USER->id),'createdby');
    $student_programme = $DB->get_records_sql("SELECT * FROM {student_programme} WHERE deleted=0 AND createdby=".$get_myadmin->createdby." ORDER BY fullname");
}else{
    $student_programme = $DB->get_records_sql("SELECT * FROM {student_programme} WHERE deleted=0 AND createdby IN (" . $cteatedbyStr . ") ORDER BY fullname");
}
//print_object($student_programme);

$stream = $DB->get_records_sql("SELECT * FROM {student_stream} WHERE deleted=0 AND createdby=".$get_myadmin->createdby." ORDER BY fullname");
//print_object($stream);

// $html .='<div class="d-flex align-items-center mb-5 mobile-filters">
// <div class="font-weight-bold">Filter by:</div>
// <div class="ml-20">
// <select class="career-select-filter" id="programme" name="programme" onchange="program(this);">
// <option value="all"> Select Program</option>
// ';
// if(!empty($student_programme)){
//     foreach ($student_programme as $key => $stdprogramme) {
//         $html .='<option value="'.$stdprogramme->id.'">'.$stdprogramme->fullname.'</option>';
//     }
// }
// $html .='</select>
// </div>
// <div class="ml-20">
// <select class="career-select-filter" id="stream" name="stream" onchange="stream(this);">
// <option disabled="" value="" selected>Select Stream</option>
// </select></div>
// <div class="ml-20">
// <select class="career-select-filter" id="semester" name="semester" onchange="semester(this);">&gt;
// <option disabled="" value="" selected>Select Semester</option>
// </select>
// </div>
// <div class="ml-20">
// <select class="career-select-filter" id="semesteryear" name="semesteryear" onchange="semesteryear(this);">
// <option disabled="" value="" selected>Select Semester Year</option>
// </select>
// </div>

// </div>';
// echo $html;

//Logic based on changed requirement

$batches = new stdClass();
$batcharr = array();
$batches = array_values(
                        $DB->get_records_sql(
                                            "SELECT ob.id as batchid,
                                                    ob.code as batchcode,
                                                    c.fullname as coursename,
                                                    c.shortname as coursecode,
                                                    ob.jobseekids as batchallottees,
                                                    ob.courseid as coursid 
                                                FROM {online_batches} ob
                                                INNER JOIN {course} c ON c.id = ob.courseid
                                                -- LEFT JOIN {logstore_standard_log} lsl ON lsl.courseid = ob.courseid
                                                LEFT JOIN {local_course_creator} lsl ON lsl.courseid = ob.courseid
                                                WHERE lsl.`userid` = $USER->id
                                                -- AND   lsl.`action` = 'created'
                                                AND   c.`visible` = 1"
                                        )
                        );    
                        
// coursename coursecode batchcode allottescount
if (!empty($batches) && $batches[0]->batchid) {
    foreach ($batches as $btch) {
        $allotteescount = 0;
        $batcharr2 = array();
        $batcharr2['batchid'] = $btch->batchid;
        $batcharr2['batchcode'] = $btch->batchcode;
        $batcharr2['coursename'] = $btch->coursename;
        $batcharr2['coursecode'] = $btch->coursecode;
        $batcharr2['coursid'] = $btch->coursid;
        $batcharr2['btype'] = 'on';
        $comacount = substr_count($btch->batchallottees,",");
        if ($comacount > 0) {
            $btchcomarr = explode(",", $btch->batchallottees);
            for ($u = 0; $u < sizeof($btchcomarr); $u++) {
                ($btchcomarr[$u] > 0) ? $allotteescount++ : $allotteescount = $allotteescount;
            }
        } else {
            ($btch->batchallottees > 0) ? $allotteescount++ : $allotteescount = $allotteescount;
        }
        $batcharr2['allottescount'] = $allotteescount;
        $btchccidd = $batcharr2['batchid']."|".$batcharr2['btype'];
        $btchallotees = $batcharr2['allottescount'];
        $batcharr2['ausers'] = '<div class="btn btn-primary" id="viewaUsers" data-value="'.$btchccidd.'">View <span class="badge badge-success">'.$btchallotees.'</span></div>';
        $batcharr[] = $batcharr2;
    }
}

$batches2 = array_values(
                        $DB->get_records_sql(
                                            "SELECT ob.id as batchid,
                                                    ob.code as batchcode,
                                                    c.fullname as coursename,
                                                    c.shortname as coursecode,
                                                    ob.jobseekids as batchallottees,
                                                    ob.courseid as coursid 
                                                FROM {offline_batches} ob
                                                INNER JOIN {course} c ON c.id = ob.courseid
                                                -- LEFT JOIN {logstore_standard_log} lsl ON lsl.courseid = ob.courseid
                                                LEFT JOIN {local_course_creator} lsl ON lsl.courseid = ob.courseid
                                                WHERE lsl.`userid` = $USER->id
                                                -- AND   lsl.`action` = 'created'
                                                AND   c.`visible` = 1"
                                        )
                        );    
                        
// coursename coursecode batchcode allottescount
if (!empty($batches2) && $batches2[0]->batchid) {
    foreach ($batches2 as $btch) {
        $allotteescount = 0;
        $batcharr2 = array();
        $batcharr2['batchid'] = $btch->batchid;
        $batcharr2['batchcode'] = $btch->batchcode;
        $batcharr2['coursename'] = $btch->coursename;
        $batcharr2['coursecode'] = $btch->coursecode;
        $batcharr2['coursid'] = $btch->coursid;
        $batcharr2['btype'] = 'of';
        $comacount = substr_count($btch->batchallottees, ",");
        if ($comacount > 0) {
            $btchcomarr = explode(",", $btch->batchallottees);
            for ($u = 0; $u < sizeof($btchcomarr); $u++) {
                ($btchcomarr[$u] > 0) ? $allotteescount++ : $allotteescount = $allotteescount;
            }
        } else {
            ($btch->batchallottees > 0) ? $allotteescount++ : $allotteescount = $allotteescount;
        }
        $batcharr2['allottescount'] = $allotteescount;
        $btchccidd = $batcharr2['batchid']."|".$batcharr2['btype'];
        $btchallotees = $batcharr2['allottescount'];
        $batcharr2['ausers'] = '<div class="btn btn-primary" id="viewaUsers" data-value="'.$btchccidd.'">View <span class="badge badge-success">'.$btchallotees.'</span></div>';
        $batcharr[] = $batcharr2;
    }
}

$table = new html_table();
$table->head = array ();
$table->colclasses = array();
$table->head[] = 'Sl. No.';
$table->head[] = 'Course name';
$table->attributes['class'] = 'admintable generaltable';
$table->head[] = 'Course Code';
$table->head[] = 'Batch Code';
$table->head[] = 'Allottees';
// $table->id = "users";
$table->id = "blist_tbl";

$sl = 1; 
if (!empty($batcharr) && $batcharr[0]['batchid']) 
{
    foreach($batcharr as $bcharr)
    {
        $row = array ();
        $row[] = $sl;
        $row[] = $bcharr['coursename'];
        $row[] = $bcharr['coursecode'];
        $row[] = $bcharr['batchcode'];
        $row[] = $bcharr['ausers'];
        $table->data[] = $row;
        $sl++;
    }
}

$usercount = sizeof($batcharr); //$usersearchcount;

if (!empty($table)) {
    echo html_writer::start_tag('div', array('class'=>'no-overflow'));
    echo html_writer::table($table);
    echo html_writer::end_tag('div');
    // echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
}

//Logic based on changed requirement

$strall = get_string('all');

$baseurl = new moodle_url('/local/batchmanagement/batch_list.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
// echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

flush();


// if (!$users) {
//     $match = array();
//     echo $OUTPUT->heading(get_string('nousersfound'));

//     $table = NULL;

// } else {

//     $table = new html_table();
//     $table->head = array ();
//     $table->colclasses = array();
//     // if (is_siteadmin()) {

//     // } else {
//     //     $table->head[] = 'Select';
//     // }
    
//     // $table->head[] =  'Sl. No.';
//     // $table->head[] = 'Batch name';
//     // $table->attributes['class'] = 'admintable generaltable';
//     // $table->head[] = 'Batch Code';
//     // $table->head[] = 'Programme';
//     // $table->head[] = 'Stream';
//     // $table->head[] = 'Semester';
//     // $table->head[] = 'Semester Year';
//     // $table->head[] = 'Users';
    
//     // $i = 1;
//     // foreach ($users as $user) {
//     //     // $buttons = array();
//     //     $lastcolumn = '';
//     //     // $fullname = fullname($user, true);

//     //     $row = array ();
//     //     // if (is_siteadmin()) {

//     //     // } else {
//     //     //     $row[] = '<input type = "checkbox" id="userchk_c-'. $user->id .'" class="check_users" name="user" value="student-'. $user->id .'">';

//     //     // }
//     //     $row[] = $i;
//     //     $row[] = $user->batchname;
//     //     $row[] = $user->batchcode;
//     //     $row[] = $user->program;;
//     //     $row[] = $user->stream;
//     //     $row[] = $user->semester;
//     //     $row[] = $user->year;
//     //     // $batchLists = $DB->get_records('batch_details', ['createdby' => $userid], 'batchname ASC');
//     //     $users = '';

//     //     $cohortDetails = $DB->get_records('cohort_members', ['cohortid' => $user->cohortid]);
//     //     $count = 0;
//     //     if (!empty($cohortDetails)) {

//     //         foreach ($cohortDetails as $cohortDetail) {

//     //             $userrec = $DB->get_record('user', ['id' => $cohortDetail->userid, 'deleted' => 0]);
//     //             if ($userrec) {
//     //                 $usertyperec = $DB->get_record('usertype', ['userid' => $cohortDetail->userid, 'deleted' => 0]);
//     //             }
//     //             if ($usertyperec && $userrec) {
//     //                 $count++;
//     //             }
//     //         }
//     //         if ($count == 0) {
//     //             $username = $count;
//     //         } else {
//     //             $username = $count;
//     //         }
//     //     } else {
//     //         $username = 0;
//     //     }

//     //     $ausers = '<div class="btn btn-primary" id="viewaUsers" data-value="'.$user->cohortid.'">View <span class="badge badge-success">' .$username.'</span></div>';
//     //     $buttons = $ausers;
//     //     $row[] = $buttons;
//     //     // $row[] = '<input type = "checkbox" id="userchk_c-'. $user->id .'" class="check_users" name="user" value="student-'. $user->id .'">';
//     //     $table->data[] = $row;
//     //     $i++;
//     // }
// }


// if (!empty($table)) {
//     echo html_writer::start_tag('div', array('class'=>'no-overflow'));
//     echo html_writer::table($table);
//     echo html_writer::end_tag('div');
//     echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
//     // if (is_siteadmin()) {

//     // } else {

//     //     echo '<div class="form-group" id="submit">
//     //     <div class="form-group row text-right">
//     //     <div class="col-sm-12">
//     //     <button type="submit" class="btn btn-primary add" id="users_deletion_student">Delete Selected User</button>
//     //     </div>
//     //     </div>
//     //     </div>';
//     // }

// }

// $PAGE->requires->js_call_amd('local_usermanagement/userLists', 'init');
// $PAGE->requires->js_call_amd('local_usermanagement/csv_pdf', 'init');
$PAGE->requires->js_call_amd('local_batchmanagement/assignbatch', 'init');
echo $OUTPUT->footer();
?>
<!-- Modal -->
<div class="modal fade" id="assignUsers" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modaltitle">Batch Allottee(s) List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div id="modalMsg" class="hidden">
                <div class="alert alert-icon alert-success alert-block alert-dismissible fade show " role="alert">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="icon fa fa-check" aria-hidden="true"></i>
                    User removed successfully from the Batch
                </div>
            </div>

            <div class="modal-body" id="userModal">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
 //$( document ).ready(function() {
    function program(sel){
       var p_id = sel.options[sel.selectedIndex].value;
       if (p_id.length > 0 ) { 
           $.ajax({
            type: "POST",
            url: "lib.php",
            data: "pid="+p_id,
            cache: false,
            success: function(data) {

            }
        });
       }

   }
//});
</script>
