<?php

define('AJAX_SCRIPT', true);
require_once(__DIR__.'/../../config.php');
require_once $CFG->dirroot . '/local/student/lib.php';
require_once $CFG->dirroot . '/local/usermanagement/lib.php';
// New Webinar Logic Here
// Enrol User into the Webinar Course
$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$schemeid = required_param('schemeid', PARAM_RAW);
$centerid = required_param('centerid', PARAM_INT);

global $DB, $CFG;

$onlndata = $DB->get_record('job_seeker_application', array('course_id' => $courseid, 'user_id' => $userid, 'isremoved' => 0));
$reponlndata = $DB->get_record('job_seeker_application', array('course_id' => $courseid, 'user_id' => $userid, 'isreapplied' => 1));
$oflndata = $DB->get_record('job_seeker_offline_application', array('course_id' => $courseid, 'user_id' => $userid, 'isremoved' => 0));
$repoflndata = $DB->get_record('job_seeker_offline_application', array('course_id' => $courseid, 'user_id' => $userid, 'isreapplied' => 1));
$shmmedata = $DB->get_record('job_seeker_scheme_application', array('userid' => $userid, 'isremoved' => 0));
$repshmmedata = $DB->get_record('job_seeker_scheme_application', array('userid' => $userid, 'isreapplied' => 1));
$wtgonndata = $DB->get_record('waiting_list', array('course_id' => $courseid, 'user_id' => $userid, 'isremoved' => 0));
$repowtgondata = $DB->get_record('waiting_list', array('course_id' => $courseid, 'user_id' => $userid, 'isreapplied' => 1));
$wtgoffndata = $DB->get_record('waiting_list_offline', array('course_id' => $courseid, 'user_id' => $userid, 'isremoved' => 0));
$repowtgoffdata = $DB->get_record('waiting_list_offline', array('course_id' => $courseid, 'user_id' => $userid, 'isreapplied' => 1));

if ($courseid) {
    $course_for_centerid = $DB->get_record('center_details', array('courseid' => $courseid), 'id, courseid, mode_id');
} else {
    $course_for_centerid = new stdClass();
}

$offstats = 1;
$cocreate_data = $DB->get_record_sql(
                                    // "SELECT * 
                                    //         FROM {user} u 
                                    //         LEFT JOIN {local_course_creator} l ON u.id = l.userid
                                    //     WHERE l.courseid = $courseid"
                                    "SELECT l.* 
                                            FROM {local_course_creator} l 
                                            INNER JOIN {user} u ON u.id = l.userid
                                        WHERE l.courseid = $courseid"    
                                );
$cocretorid = (!empty($cocreate_data) && $cocreate_data->id) ? $cocreate_data->userid : 0;                               

if ((!empty($cocreate_data) && $cocreate_data->id) && ($cocreate_data->username == 'ictacademy' || $cocreate_data->email == 'ictacademy@gmail.com')) {
    $csiteurl = $CFG->wwwroot;
    $ictmatch = get_config('local_usermanagement', 'ictmatch');
    $produrl = substr_count($csiteurl, $ictmatch);
    if ($produrl) {
        $offlindata = offline_applycourse($courseid, $centerid);
        if (!empty($offlindata)) {
            $offstats = 0;
        }
    }
}

if ($cocreate_data->username == 'kba_kerala' || $cocreate_data->email == 'kba.admin@iiitmk.ac.in') {
    $csiteurl = $CFG->wwwroot;
    $kbamatch = get_config('local_usermanagement', 'kbamatch');
    $produrl = substr_count($csiteurl, $kbamatch);
    if ($produrl) {
        $ofdt = 1;
        $offlindatakba = kba_applycourse($courseid, $centerid);
        if (!empty($offlindatakba)) {
            $offstats = 1;
        } else {
            $offstats = 0;
        }
    }
}

if ($onlndata || $reponlndata || $oflndata || $repoflndata   || $shmmedata || $repshmmedata  || $wtgonndata || $repowtgondata || $wtgoffndata || $repowtgoffdata) {
      $ret_val = array('success' => 4);
} else {
    $schemedata = $DB->get_record('job_seeker_scheme_application', array('courseid' => $courseid, 'userid' => $userid, 'schemeid' => $schemeid, 'isremoved' => 1));
    if ($schemedata) {
        $apply = new stdClass();
        $apply->id = $schemedata->id;
        $apply->centerid = $centerid;
        $apply->timemodified = time();
        $apply->isreapplied = 1;
        try {
            $updaate = $DB->update_record('job_seeker_scheme_application', $apply);
            if ($updaate) {
                if ($courseid) {
                    $course_for_centerid = $DB->get_record('center_details', array('courseid' => $courseid), 'id, courseid, mode_id');
                } else {
                    $course_for_centerid = new stdClass();
                }
                if ($userid && !empty($course_for_centerid) && $course_for_centerid->id ) {
                    addschcandstats(0, $userid, $course_for_centerid->courseid, 0, $course_for_centerid->mode_id, $cocretorid, $centerid);
                }  
                if ($ofdt == 1) {
                    $ret_val = array('success' => 10,  'succurl' => $offlindatakba);
                } else {
                    $ret_val = array('success' => 3);
                }
            } else {
                $ret_val = array('success' => 2);
            }
        } catch(Exception $e) {
            print_object($e);
        }
    } else {
        if ($offstats) {
            $apply = new stdClass();
            $apply->courseid = $courseid;
            $apply->userid = $userid;
            $apply->schemeid = $schemeid;
            $apply->centerid = $centerid;
            $apply->timecreated = time();
            $scheme_applicants_insert = $DB->insert_record('job_seeker_scheme_application', $apply);
            if ($scheme_applicants_insert) {
                // if ($courseid) {
                //     $course_for_centerid = $DB->get_record('center_details', array('courseid' => $courseid), 'id, courseid, mode_id');
                // } else {
                //     $course_for_centerid = new stdClass();
                // }
                if ($userid && !empty($course_for_centerid) && $course_for_centerid->id ) {
                    addschcandstats(0, $userid, $course_for_centerid->courseid, 0, $course_for_centerid->mode_id, $cocretorid, $centerid);
                }
                if ($ofdt == 1) {
                    $ret_val = array('success' => 9,  'succurl' => $offlindatakba);
                } else {
                    $ret_val = array('success' => 1);
                }
            } else {
                $ret_val = array('success' => 2);
            }
        } else {
            removcandstats(0, $userid, $course_for_centerid->courseid, $centerid, $course_for_centerid->mode_id);
            if ($ofdt == 1) {
                $ret_val = array('success' => 8);
            } else {
                $ret_val = array('success' => 5);
            }
        }
    }
} 

header("Content-Type: application/json");
echo json_encode($ret_val);

