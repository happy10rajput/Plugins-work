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
require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
class local_batchmanagement_external extends external_api {

    public static function add_batch_parameters() {
        return new external_function_parameters(
            array(
                'programme' => new external_value(PARAM_TEXT, 'programme'),
                'stream' => new external_value(PARAM_TEXT, 'stream'),
                'semester' => new external_value(PARAM_TEXT, 'semester'),
                'semesteryear' => new external_value(PARAM_TEXT, 'semesteryear'),
                'batchname' => new external_value(PARAM_TEXT, 'batchname'),
                'batchcode' => new external_value(PARAM_TEXT, 'batchcode')
            )
        );
    }
    public static function add_batch($programme,$stream,$semester,$semesteryear,$batchname,$batchcode) {
        global $DB,$CFG,$USER;
        //add condition here 
            $exist = $DB->record_exists('batch_details',array('programme'=>$programme,'stream'=>$stream,'semester'=>$semester,'semyear'=>$semesteryear,'batchname'=>$batchname));
            if(!$exist){
                $cohort = new stdClass();
                $cohort->contextid = 1;
                $cohort->name = $batchname;
                $cohort->idnumber = mt_rand(1000, 100000000);
                $cohort->description = $batchname;
                $cohort->descriptionformat = 2;
                $cohort->visible = 1;
                $cohort->component = '';
                $cohort->timecreated = time();
                $cohort->timemodified = null;
                $cohort->theme = '';
                $cohortid = cohort_add_cohort($cohort);

                $batch = new stdClass();
                $batch->cohortid = $cohortid;
                $batch->batchname = $batchname;
                $batch->batchcode = $batchcode;
                $batch->programme = $programme;
                $batch->stream = $stream;
                $batch->semester = $semester;
                $batch->semyear = $semesteryear;
                $batch->createdby = $USER->id;
                $batch->timecreated = time();
                $batch->timemodified = time();
                $DB->insert_record('batch_details',$batch);
                $txt = '1';
            }else{
                $txt = '0';
            }   
            return $txt;
     }
     public static function add_batch_returns() {
        return new external_value(PARAM_TEXT, 'Batch Created Successfully');
    }


    public static function fetch_stream_parameters() {
        return new external_function_parameters(
            array(
                'selectedval' => new external_value(PARAM_INT, 'programme')
            )
        );
    }
    public static function fetch_stream($selectedval) {
        global $DB;
        $stream = $DB->get_records('student_stream',array('programme'=>$selectedval, 'deleted' => 0));
        return $stream;
        
    }
    public static function fetch_stream_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id'),
                    'fullname' => new external_value(PARAM_RAW, 'fullname')
                )
            )
        );
    }

    public static function fetch_semester_parameters() {
        return new external_function_parameters(
            array(
                'selectedval' => new external_value(PARAM_INT, 'programme')
            )
        );
    }
    public static function fetch_semester($selectedval) {
        global $DB;
        $semester = $DB->get_records('student_semester',array('programme'=>$selectedval, 'deleted' => 0));
        return $semester;
        
    }
    public static function fetch_semester_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id'),
                    'semester' => new external_value(PARAM_RAW, 'semester')
                    
                )
            )
        );
    }

    public static function fetch_semester_year_parameters() {
        return new external_function_parameters(
            array(
                'selectedval' => new external_value(PARAM_INT, 'programme')
            )
        );
    }
    public static function fetch_semester_year($selectedval) {
        global $DB;
        $semester_years = $DB->get_records('student_sem_year',array('programme'=>$selectedval, 'deleted' => 0));
        return $semester_years;
        
    }
    public static function fetch_semester_year_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id'),
                    'semester' => new external_value(PARAM_RAW, 'semester')
                    
                )
            )
        );
    }

    public static function fetch_batch_parameters() {
        return new external_function_parameters(
            array(
                'selectedval' => new external_value(PARAM_TEXT, 'semesteryear'),
                'stream' => new external_value(PARAM_TEXT, 'stream'),
                'programme' => new external_value(PARAM_TEXT, 'programme'),
                'semester' => new external_value(PARAM_TEXT, 'semester')
            )
        );
    }
    public static function fetch_batch($selectedval,$stream,$programme,$semester) {
        global $DB, $USER;

        $get_professor = $DB->get_record('usertype',array('userid'=>$USER->id,'usertype' => 'Professor'));
        $batch = array();
        if ($get_professor) {

            $batch = $DB->get_records('batch_details',array('programme' => $programme , 
                'stream' => $stream, 'semester' => $semester , 'semyear' => $selectedval, 'createdby' => $get_professor->createdby), 'batchname ASC');
        } else {

            $batch = $DB->get_records('batch_details',array('programme' => $programme , 
                'stream' => $stream, 'semester' => $semester , 'semyear' => $selectedval, 'createdby' => $USER->id), 'batchname ASC');
        }

        if($batch) {
            return $batch;
        } else {
            return $batch;
        }
        
        
    }
    public static function fetch_batch_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'batchname' => new external_value(PARAM_TEXT, 'Batch Name'),
                    'cohortid' => new external_value(PARAM_TEXT, 'Cohort Id')
                )
            )
        );
    }

    public static function fetch_users_parameters() {
        return new external_function_parameters(
            array(
                'selectedval' => new external_value(PARAM_TEXT, 'batch'),
                'stream' => new external_value(PARAM_TEXT, 'stream'),
                'programme' => new external_value(PARAM_TEXT, 'programme'),
                'semester' => new external_value(PARAM_TEXT, 'semester'),
                'semesteryear' => new external_value(PARAM_TEXT, 'semesteryear')
            )
        );
    }
    public static function fetch_users($selectedval,$stream,$programme,$semester,$semesteryear) {
        global $DB, $USER;
        $array = array(
            'userid'       => $USER->id,
            'program'      => $programme,
            'semester'     => $semester,
            'semesteryear' => $semesteryear,
            'cohortid'     => $selectedval,
            'stream'       => $stream
        );
        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, ut.usertype, ut.createdby,
        IF(cm.cohortid IS NULL, '0', '1') AS checked
        FROM {user} u
        JOIN {usertype} ut ON ut.userid = u.id
        LEFT JOIN {cohort_members} cm ON cm.userid = u.id
        WHERE ut.usertype LIKE '%Student%' AND ut.createdby = :userid
        AND ut.programme = :program AND ut.stream = :stream AND ut.semester = :semester
        AND ut.semyear = :semesteryear AND u.deleted = 0 AND ut.deleted = 0
        AND (cm.cohortid IS NULL OR cm.cohortid = :cohortid)";
        $users = $DB->get_records_sql($sql, $array);
        $content = '';
        foreach ($users as $user) {
            $class = '';
            $asCls = '';
            if ($user->checked == '1') {
                $class = 'checked disabled';
                $asCls = 'Assigned';
            }
            $content .= '<tr class="'.$asCls.'">
            <td>'. $user->firstname .'</td>
            <td>'. $user->lastname .'</td>
            <td>'. $user->email .'</td>
            <td><input type = "checkbox" id="userchk'. $user->id .'" class="user" name="user" value="'. $user->id .'"  '.$class.'></td></tr>';
        }
        if ($content == '') {
            $content .= '<tr><td></td><td></td><td>NO RECORDS FOUND</td><td></td></tr>';
        }
        // if (!empty($users)) {
        //     foreach ($users as $user) {
        //         $cohortRS = $DB->get_record('cohort_members', ['cohortid' => $selectedval, 'userid' => $user->id]);
        //         // If user exist in cohort then checked.
        //         if (!empty($cohortRS)) {
        //             $class = 'checked disabled';
        //             $asCls = 'Assigned';
        //         } else {
        //             $class = '';
        //             $asCls = '';
        //         }
        //         $content .= '<tr class="'.$asCls.'">
        //         <td>'. $user->firstname .'</td>
        //         <td>'. $user->lastname .'</td>
        //         <td>'. $user->email .'</td>
        //         <td><input type = "checkbox" id="userchk'. $user->id .'" class="user" name="user" value="'. $user->id .'"  '.$class.'></td></tr>';
        //     }
        // } else {
        //     $content .= '<tr><td></td><td></td><td>NO RECORDS FOUND</td><td></td></tr>';
        // }
        return $content;
    }

    public static function fetch_users_returns() {
        return new external_value(PARAM_RAW, 'User list');
    }

    public static function assign_users_parameters() {
        return new external_function_parameters(
            array(
                'batch' => new external_value(PARAM_TEXT, 'batch'),
                'users' => new external_value(PARAM_TEXT, 'users')
            )
        );
    }
    public static function assign_users($batch,$users) {
        global $DB;
        $users = json_decode($users);
        foreach ($users as $user) {
            cohort_add_member($batch, $user);
        }
        return "Users Assigned To Batch Successfully";
    }
    public static function assign_users_returns() {
        return new external_value(PARAM_TEXT, 'Users Assigned To Batch Successfully');
    }


    public static function unassign_users_frombatch_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_TEXT, 'userid'),
                'cohortid' => new external_value(PARAM_TEXT, 'cohortid/Bathcid'),
            )
        );
    }

    public static function unassign_users_frombatch($userid, $cohortid) {
        global $DB,$CFG;
        require_once($CFG->dirroot.'/cohort/lib.php');
        // Remove user from cohort & Course.
        cohort_remove_member($cohortid, $userid);
        return true;
    }

    public static function unassign_users_frombatch_returns() {
        return new external_value(PARAM_RAW, 'Successful'); 
    }

    public static function view_assigned_user_parameters() {
        return new external_function_parameters(
            array(
                'cohortid' => new external_value(PARAM_TEXT, 'cohortid/Bathcid')
            )
        );
    }

    public static function view_assigned_user($cohortid) {
        global $DB, $CFG, $USER;
        //iTrack Core Logic
        // $cohortDetails = $DB->get_records('cohort_members', ['cohortid' => $cohortid]);
        // $count = 0;
        // $tab = '<table class="admintable generaltable" id="filterssetting">';
        // $tab .= '<thead>';
        // $tab .= '<tr>';
        // $tab .= '<th class="header c0">Sl. No.</th>';
        // $tab .= '<th class="header c2">Full Name</th>';
        // $tab .= '<th class="header c1">Action</th>';
        // $tab .= '</tr>';
        // $tab .= '</thead>';
        // if (!empty($cohortDetails)) {
        //     $tab .= '<tbody>';
        //     foreach ($cohortDetails as $cohortDetail) {
        //         $userrec = $DB->get_record('user', ['id' => $cohortDetail->userid, 'deleted' => 0]);
        //         if ($userrec) {
        //             $usertyperec = $DB->get_record('usertype', ['userid' => $cohortDetail->userid, 'deleted' => 0]);
        //         }
        //         if ($usertyperec && $userrec) {
        //             $count++;
        //             $tab .= '<tr class="">';
        //             $tab .= '<td class="cell c0">'.$count.'</td>';
        //             $tab .= '<td class="cell c1">'.fullname($userrec).'</td>';
        //             $tab .= '<td class="cell c2"><span aria-hidden="true" id="'. $userrec->id .'" data-value="'. $cohortid .'" class="unAssign" style="cursor:pointer;"><i class="fa fa-trash" title="Remove User From Batch"></i></span></td>';
        //             $tab .= '</tr>';
        //         }
        //     }
        //     if ($count == 0) {
        //         $tab .= '<tbody>';
        //         $tab = 'No Users Assigned';
        //     }
        //     $tab .= '</tbody>';
        // } else {
        //     $tab .= '<tbody>';
        //     $tab = 'No Users Assigned';
        //     $tab .= '</tbody>';
        // }
        // $tab .= '</table>';

        $splicohort = explode("|", $cohortid);
        $sbatchid = $splicohort[0];
        $sbatchtype = $splicohort[1];
        $cohortdetails = new stdClass();   

        switch ($sbatchtype) {
            case 'of':
                    $cohortoffline = $DB->get_record_sql(
                                        "SELECT ob.id as batchid,
                                                ob.code as batchcode,
                                                c.fullname as coursename,
                                                c.shortname as coursecode,
                                                ob.jobseekids as batchallottees
                                            FROM {offline_batches} ob
                                            INNER JOIN {course} c ON c.id = ob.courseid
                                            -- LEFT JOIN {logstore_standard_log} lsl ON lsl.courseid = ob.courseid
                                            LEFT JOIN {local_course_creator} lsl ON lsl.courseid = ob.courseid
                                            WHERE lsl.`userid` = $USER->id
                                            -- AND   lsl.`action` = 'created'
                                            AND   c.`visible` = 1
                                            AND   ob.`id` = $sbatchid"
                                    );
                                    
                    if (!empty($cohortoffline) && $cohortoffline->batchid) {
                        $cohortdetails = $cohortoffline;
                    }
                    break;

            case 'on':
                    $cohortonline =  $DB->get_record_sql(
                                        "SELECT ob.id as batchid,
                                                ob.code as batchcode,
                                                c.fullname as coursename,
                                                c.shortname as coursecode,
                                                ob.jobseekids as batchallottees
                                            FROM {online_batches} ob
                                            INNER JOIN {course} c ON c.id = ob.courseid
                                            -- LEFT JOIN {logstore_standard_log} lsl ON lsl.courseid = ob.courseid
                                            LEFT JOIN {local_course_creator} lsl ON lsl.courseid = ob.courseid
                                            WHERE lsl.`userid` = $USER->id
                                            -- AND   lsl.`action` = 'created'
                                            AND   c.`visible` = 1
                                            AND   ob.`id` = $sbatchid"
                                    );

                    if (!empty($cohortonline) && $cohortonline->batchid ) {
                        $cohortdetails = $cohortonline;
                    }
                    break;

            default:
                    break;                
        }
        
        $sl = 1;
        if (!empty($cohortdetails) && $cohortdetails->batchid) {
            $ballotteesarr = array();
            $comacount = substr_count($cohortdetails->batchallottees, ",");
            if ($comacount > 0) {
                $btchcomarr = explode(",", $cohortdetails->batchallottees);
                for ($u = 0; $u < sizeof($btchcomarr); $u++) {
                    if ($btchcomarr[$u] > 0) {
                        $usrdata = $DB->get_record('user', array('id' => $btchcomarr[$u], 'deleted' => 0), 'id, firstname, lastname, email');
                        if (!empty($usrdata) && $usrdata->id) {
                            $ballotteesarr2 = array();
                            $ballotteesarr2['sl'] = $sl;
                            $ballotteesarr2['uid'] = $btchcomarr[$u];
                            $ballotteesarr2['coursename'] = $cohortdetails->coursename;
                            $ballotteesarr2['coursecode'] = $cohortdetails->coursecode;
                            $ballotteesarr2['batchcode'] = $cohortdetails->batchcode;
                            $ballotteesarr2['uname'] = ucwords($usrdata->firstname." ".$usrdata->lastname);
                            $ballotteesarr2['uemail'] = $usrdata->email;
                            // $allotteescount++:$allotteescount=$allotteescount;
                            $ballotteesarr[] = $ballotteesarr2;
                        }                        
                    }                        
                    $sl++;
                }
                
            } else {

              if ($cohortdetails->batchallottees > 0) {
                $usrdata = $DB->get_record('user', array('id' => $cohortdetails->batchallottees, 'deleted' => 0), 'id, firstname, lastname, email');
                if (!empty($usrdata) && $usrdata->id) {
                    $ballotteesarr2 = array();
                    $ballotteesarr2['sl'] = $sl;
                    $ballotteesarr2['uid'] = $cohortdetails->batchallottees;
                    $ballotteesarr2['coursename'] = $cohortdetails->coursename;
                    $ballotteesarr2['coursecode'] = $cohortdetails->coursecode;
                    $ballotteesarr2['batchcode'] = $cohortdetails->batchcode;
                    $ballotteesarr2['uname'] = ucwords($usrdata->firstname." ".$usrdata->lastname);
                    $ballotteesarr2['uemail'] = $usrdata->email;
                    $ballotteesarr[] = $ballotteesarr2;
                }
                //$allotteescount++:$allotteescount=$allotteescount;
              }
            }

            $count = 0;
            $tab = '<div class="row col-md-12" style="margin:-20px 0px 10px 0px; font-size:13px !important;">';
            $tab .= '<div class="col-md-4"><b>Course Name&nbsp;:</b> </div><div class="col-md-8">'.$cohortdetails->coursename.'</div>';
            $tab .= '<div class="col-md-4"><b>Course Code&nbsp;&nbsp;:</b></div><div class="col-md-8">'.$cohortdetails->coursecode.'</div>';
            $tab .= '<div class="col-md-4"><b>Batch Code&nbsp;&nbsp;:</b><br></div><div class="col-md-8">'.$cohortdetails->batchcode.'<br></div>';
            $tab .= '</div>';
            $tab .= '<table class="admintable generaltable" id="filterssetting">';
            $tab .= '<thead>';
            $tab .= '<tr>';
            $tab .= '<th>Sl. No.</th>';
            // $tab .= '<th>Course Name</th>';
            // $tab .= '<th>Course Code</th>';
            // $tab .= '<th>Batch Code</th>';
            $tab .= '<th>Full Name</th>';
            $tab .= '<th>Email</th>';
            // $tab .= '<th class="header c1">Action</th>';
            $tab .= '</tr>';
            $tab .= '</thead>';
            if (!empty($ballotteesarr)) {
                $tab .= '<tbody>';
                foreach ($ballotteesarr as $ballottee) {
                        $tab .= '<tr class="">';
                        $tab .= '<td>';
                        $tab .= $ballottee['sl'];
                        $tab .= '</td>';
                        
                        // $tab .= '<td>';
                        // $tab .= $ballottee['coursename'];
                        // $tab .= '</td>';

                        // $tab .= '<td>';
                        // $tab .= $ballottee['coursecode'];
                        // $tab .= '</td>';

                        // $tab .= '<td>';
                        // $tab .= $ballottee['batchcode'];
                        // $tab .= '</td>';

                        $tab .= '<td>';
                        $tab .= $ballottee['uname'];
                        $tab .= '</td>';

                        $tab .= '<td>';
                        $tab .= $ballottee['uemail'];
                        $tab .= '</td>';

                        $tab .= '</tr>';
                    }
                $tab .= '</tbody>';    
            }  else {
                $tab .= '<tbody>';
                $tab = 'No Users Assigned';
                $tab .= '</tbody>';
            }
            $tab .= '</table>';
        }
        return $tab;
    }

    public static function view_assigned_user_returns() {
        return new external_value(PARAM_RAW, 'Successful'); 
    }

    //batch migration 
    //parameter 
    public static function batch_migration_parameters() {
        return new external_function_parameters(
            array(
                'programme' => new external_value(PARAM_TEXT, 'programme'),
                'stream' => new external_value(PARAM_TEXT, 'stream'),
                'semester' => new external_value(PARAM_TEXT, 'semester'),
                'year' => new external_value(PARAM_TEXT, 'semesteryear'),
                'batch' => new external_value(PARAM_TEXT, 'batchname')
            )
        );
    }

    //main function 
    //return function 
    public static function batch_migration($programme,$stream,$semester,$year,$batch){
        global $DB,$USER;
        //echo $programme;
        //echo 'i am here ';
        //echo $programme.'-'.$stream.'-'.$semester.'-'$year.'-'.$batch;
        //record is exist or not check here
        $sm = $DB->get_record('student_semester',array('semester'=>$semester,'programme'=>$programme));
        $semester = $sm->id;
        $condition = [
            'programme'=>$programme,
            'stream'=>$stream,
            'semester'=>$semester,
            'semyear'=>$year,
            'cohortid'=>$batch

        ];
        $flag = '0';
        //print_object($condition);
        if(!($DB->record_exists('batch_details',$condition))){
            //assign tp
            $batchdeatails = $DB->get_record('batch_details',array('cohortid'=>$batch));
            $cohort = $DB->get_record('cohort',array('name'=>$batchdeatails->batchname));

            $insert = new stdClass();
            $insert->cohortid = $batch;
            $insert->batchname = $batchdeatails->batchname;
            $insert->batchcode  =$batchdeatails->batchcode;
            $insert->programme = $programme;
            $insert->stream = $stream;
            $insert->semester = $semester;
            $insert->semyear = $year;
            $insert->createdby = $USER->id;
            $insert->timecreated = time();
            $insert->timemodified = time();
            $DB->insert_record('batch_details',$insert);
            //new user entry in mdl_usertype
            $users = $DB->get_records('cohort_members',array('cohortid'=>$cohort->id));
            //$prgm = $DB->get_record('student_programme',array('id'=>$programme));
            //$stm = $DB->get_record('student_stream',array('id'=>$stream));
            if(!empty($users)){

                foreach ($users as $key => $userd) {
                    $condition = [
                        'userid'=>$userd->userid,
                        'programme'=>$programme,
                        'stream'=>$stream,
                        'semester'=>$semester,
                        'semyear'=>$year,
                        'usertype'=>'Student'
                    ];
                    if(!($DB->record_exists('usertype',$condition))){

                        $usertype = new stdClass();
                        $usertype->userid = $userd->userid ;
                        $usertype->usertype = 'Student' ;
                        $usertype->programme = $programme;
                        $usertype->stream = $stream ;
                        $usertype->semester = $semester;
                        $usertype->semyear=$year ;
                        $usertype->createdby =$USER->id ;
                        $usertype->timecreated=time() ;
                        $usertype->timemodified = time();
                        $usertype->deleted= 0;
                        $DB->insert_record('usertype',$usertype);
                    }

                }
            }
            $flag = '1';
            //return "Batch Migration is done Successfully";
            return $flag ;
        }else{

            return $flag;
            //return "Failed! Migration of this batch was done earlier.";
        }

        //else
        //insert into batch table 
    }
    public static function batch_migration_returns() {
        return new external_value(PARAM_TEXT, 'Migration is Done Successfully');
    } 

    //fetch all batch 
    public static function fetch_batchall_parameters() {
        return new external_function_parameters(
            array(
                'selectedval' => new external_value(PARAM_TEXT, 'semesteryear'),
                'stream' => new external_value(PARAM_TEXT, 'stream'),
                'programme' => new external_value(PARAM_TEXT, 'programme'),
                'semester' => new external_value(PARAM_TEXT, 'semester')
            )
        );
    }
    public static function fetch_batchall($selectedval,$stream,$programme,$semester) {
        global $DB, $USER;
        $batch = $DB->get_records('batch_details',array('programme' => $programme, 
            'stream' => $stream,'semester'=>$semester,'semyear'=>$selectedval,'createdby' => $USER->id), 'batchname,cohortid');
        /*$sql = "SELECT batchname,cohortid from {batch_details}   WHERE programme = $programme and stream = $stream and semester = $semester and semyear = $selectedval and  createdby = $USER->id";
        echo $sql;
        $batch = $DB->get_records_sql($sql);*/
        //print_object($batch);
        return $batch;

    }
    public static function fetch_batchall_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'batchname' => new external_value(PARAM_TEXT, 'Batch Name'),
                    'cohortid' => new external_value(PARAM_TEXT, 'Cohort Id')
                )
            )
        );
    }

    //new year filter 
    public static function fetch_semester_year_new_parameters() {
        return new external_function_parameters(
            array(
                'selectedval' => new external_value(PARAM_INT, 'programme'),
                'stream' => new external_value(PARAM_TEXT, 'stream'),
                'semester' => new external_value(PARAM_TEXT, 'semester')
            )
        );
    }
    public static function fetch_semester_year_new($selectedval,$stream,$semester) {
        global $DB,$USER;
        $array=[];
        $sql = "SELECT id,semyear from {batch_details}   WHERE programme = $selectedval and stream = $stream and semester = $semester and createdby = $USER->id ORDER by id Desc LIMIT 0,1";
        
        $batch= $DB->get_record_sql($sql);
        if(!empty($batch->semyear)){
            $semester_years = $DB->get_record('student_sem_year',array('id'=>$batch->semyear, 'deleted' => 0));
            $smyears = $DB->get_records('student_sem_year',array('programme'=>$semester_years->programme,'deleted' => 0));
           // print_object($smyears);
            //print_object($semester_years->semester);
            foreach ($smyears as $key => $value) {

                if($value->semester >= $semester_years->semester){
                    $newinsert = new stdClass();
                    $newinsert->id = $value->id;
                    $newinsert->semester = $value->semester;
                    $array[] =$newinsert;
                }             
            }
            return $array;
        }

    }
    public static function fetch_semester_year_new_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id'),
                    'semester' => new external_value(PARAM_RAW, 'semester')

                )
            )
        );
    }


}
