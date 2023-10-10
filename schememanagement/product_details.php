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
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/buykart/lib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/local/marketplace/cslinks.php');
require_once($CFG->dirroot . '/local/marketplace/jslink.php');
require_once($CFG->dirroot . '/theme/remui/layout/customfront.php');
require_once(__DIR__ . '/lib.php');
global $DB, $USER, $CFG, $PAGE;
purge_caches();
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('marketplace');
$PAGE->set_url('/local/schememanagement/product_details.php');
$PAGE->navbar->add('Product Detail');
$PAGE->set_title('Product Detail');

//new css added 

$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/theme/remui/fontawesome/css/all.css'));
//$PAGE->requires->css(new moodle_url($CFG->wwwroot .'/local/marketplace/css/newdesigncss/font-awesome.css'));
// $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/local/marketplace/newdesign/index.css'));
// $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/local/marketplace/newdesign/mpHome.css'));
//$PAGE->requires->jquery();
$productid = required_param('id', PARAM_INT);
$adm = optional_param('adm', 0, PARAM_INT);
$pge = optional_param('pge', null, PARAM_INT);
$srch = optional_param('srch', null, PARAM_RAW);
$centplim = 10;
$pge = ($pge) ? $pge : 1;
$start = ($pge - 1) * $centplim;
$limit = $centplim;
$schemeid = optional_param('schemeid', null, PARAM_RAW);
$pagepass = $CFG->wwwroot.'/local/schememanagement/product_details.php?id='.$productid;
echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>';
echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">';
echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" rel="stylesheet">';
echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>';
echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
echo '<link href="' . $CFG->wwwroot . '/theme/remui/style/course-new.css" rel="stylesheet" true>';
echo '<script src="' . $CFG->wwwroot . '/theme/remui/javascript/customize.js" true></script>';

echo $OUTPUT->header();
$templatecontext = array();
$templatecontext['site_url'] = $CFG->wwwroot;
$templatecontext['pluginlink'] = $CFG->wwwroot . '/theme/remui';
if (isloggedin()) {
    $templatecontext['loginlink'] = $CFG->wwwroot . '/login/logout.php';
    $templatecontext['logintext'] = 'Logout';
} else {
    $templatecontext['loginlink'] = $CFG->wwwroot . '/login/index.php';
    $templatecontext['logintext'] = 'Login';
}
//for admin assigning activity 
if (is_siteadmin()) {
    $templatecontext['showactivity'] = 1;
}
$get_user_role = $DB->get_record('role_assignments', array('userid' => $USER->id), 'roleid'); 
$get_user_role_id = $get_user_role->roleid;
//&& $get_user_role_id == 21

if (!empty($adm)) {
    $co_critObj = $DB->get_record('course_criteria', array('product_id' => $productid), 'elligi');
    $co_criteria = $co_critObj->elligi;
    if (!empty($co_criteria)) {
        $c_criteria = 1;
    } else {
        $c_criteria = 0;
    }
    $coursedeatilsql =  "SELECT c.*,
                                ceg.*,
                                lbp.*,
                                lbv.*,
                                lcp.partner_name as coursepartner,
                                cmap.datetodisplay as cdispdate
                                FROM {course} c
                                LEFT JOIN {course_extrasettings_general} ceg ON ceg.courseid = c.id
                                LEFT JOIN {local_buykart_product} lbp ON lbp.course_id = c.id
                                LEFT JOIN {local_buykart_variation} lbv on lbv.product_id = lbp.id
                                LEFT JOIN {local_course_partners} lcp ON ceg.coursepartner = lcp.id
                                LEFT JOIN {course_mapped} cmap ON cmap.courseid = c.id
                        WHERE lbp.id = '".$productid."' ";

    $getcoursesdetails = $DB->get_record_sql($coursedeatilsql);
    $coursesid = $getcoursesdetails->course_id;

    // $get_course_creator = $DB->get_record('logstore_standard_log', array('courseid' => $coursesid, 'action' => 'created'), 'userid');
    $get_course_creator = $DB->get_record('local_course_creator', array('courseid' => $coursesid), 'userid');
    $course_creator_id = ($get_course_creator->userid) ? $get_course_creator->userid : 0;

    // $get_user_role = $DB->get_record('role_assignments', array('userid' => $USER->id), 'roleid'); 
    // $get_user_role_id = $get_user_role->roleid;

    if ($course_creator_id == 2) {
        $getcprecord = $DB->get_record('course_extrasettings_general', array('courseid' => $coursesid), 'coursepartner');
        $cpId = $getcprecord->coursepartner; 
        
        $cpNameRec = $DB->get_record('local_course_partners', array('id' => $cpId), 'partner_name');
        $cpName = strtoupper(trim($cpNameRec->partner_name));
        $cpName = str_replace(" ","_", $cpName);
        //echo "course_creator_id 2 : ".$cpName."<br>";
        $cpuserrecsql = "SELECT id,username
                            FROM {user}
                            WHERE UPPER(username) = '$cpName'";
        $cpuserrec = $DB->get_record_sql($cpuserrecsql);
        $cpuserID = $cpuserrec->id;
        $jskUsrId = $cpuserID;
    } else {
        $jskUsrId = $course_creator_id;    
    }

    $productsid = $productid;
    //get all product details 
    //$product = local_buykart_get_product($productid);
    //print_object($$product);die;
    $getcourse = $DB->get_record('local_buykart_product', array('id' => $coursesid), 'course_id');
    //to get courseid from product id 
    $courseid =  $coursesid;

    $cmodesql = "SELECT cdm.*,
                        cd.online_batch_size,
                        cd.noofonlinebatches,
                        cd.id as centrid
                            FROM {center_details} cd
                    LEFT JOIN {course_mode_master} cdm ON cdm.id = cd.mode_id
                    WHERE cd.courseid = '".$courseid."' ";
    $cmode = $DB->get_record_sql($cmodesql);
    $checkapplied = ($cmode->id > 1)?$DB->get_records('job_seeker_offline_application', array('course_id' => $coursesid, 'user_id' => $USER->id )):$DB->get_records('job_seeker_application', array('course_id' => $coursesid, 'user_id' => $USER->id ));
    $checkapplied = array_values($checkapplied);

    // $chqSql = "SELECT * from {job_seeker_application} WHERE `course_id` = $coursesid AND `user_id` = $USER->id";
    
    $checkwaitapplied = ($cmode->id > 1) ? $DB->get_records('waiting_list_offline', array('course_id' => $coursesid, 'user_id' => $USER->id )) : $DB->get_records('waiting_list', array('course_id' => $coursesid, 'user_id' => $USER->id ));
    $checkwaitapplied = array_values($checkwaitapplied);

    $bcheckapplied = ($cmode->id == 3) ? $DB->get_records('job_seeker_application', array('course_id' => $coursesid, 'user_id' => $USER->id )) : new stdClass();
    $bcheckapplied = array_values($bcheckapplied);

    $bcheckwaitapplied = ($cmode->id == 3) ? $DB->get_records('waiting_list', array('course_id' => $coursesid, 'user_id' => $USER->id )) : new stdClass();
    $bcheckwaitapplied = array_values($bcheckwaitapplied);

    //waiting_list
    $noofapplys = $DB->get_records('job_seeker_application', array('course_id' => $courseid, 'isremoved' => 0));
    $loguserapplyornot = $DB->get_record('job_seeker_application', array('course_id' => $courseid, 'user_id'=>$USER->id));

    $cofull = 0;
    // if($cmode->id == 1 && sizeof($noofapplys) >= ($cmode->online_batch_size * $cmode->noofonlinebatches) && !$loguserapplyornot->id)
    if (($cmode->id == 1 || $cmode->id == 3 ) && count($noofapplys) >= ($cmode->online_batch_size * $cmode->noofonlinebatches) && !$loguserapplyornot->id) {
        ($cmode->id == 1) ? $cofull = 1 : $bcofull = 1;
    }
        
    $special_status = 0;
    $wait_status = 0;
    if (empty($checkapplied) && empty($bcheckapplied) && empty($checkwaitapplied) && empty($bcheckwaitapplied)) {
        ($cofull) ? (!empty($checkwaitapplied)) ? $checkapply = "Applied Already (Waiting)" : $checkapply = "Apply Now (Waiting)" : $checkapply = (($cmode->id == 3) ? "Apply Now (Offline)" : "Apply Now");
        (!empty($checkwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
        //$capply_else = false;
        $capply = 1;
    } else {
        // $checkapply = "Already Applied";
        if (!empty($bcheckapplied)) {
            $checkapply = "Apply Now (Already Applied - Online)";
        } else {
            // $checkapply = "Already Applied (Offline)";
            // $checkapply = ($cmode->id == 1) ? "Already Applied" : "Already Applied (Offline)";
            $checkapply = "Already Applied";
        }

        //$capply_else = false;
        $capply = 0;
        if (($checkapplied[0]->isremoved == 1) || ($checkwaitapplied[0]->isremoved == 1 )) {
            $checkapply = ($cmode->id == 3) ? "Apply Now (Offline)" : "Apply Now";
            $special_status = 1;
        }
    }

    $reappliedstatus = 0;
    if (!empty($checkapplied) && $checkapplied[0]->id) {
        foreach ($checkapplied as $chckappl) {
            // if($chckappl->isremoved == 1 && $chckappl->isreapplied == 1) 
            if ($chckappl->isreapplied == 1) {
                $reappliedstatus = 1;
            }
        }
    }

    if (!$reappliedstatus) {
        if (!empty($checkwaitapplied) && $checkwaitapplied[0]->id) {
            foreach ($checkwaitapplied as $chckwappl) {
                // if($chckappl->isremoved == 1 && $chckappl->isreapplied == 1) 
                if ($chckwappl->isreapplied == 1) {
                    $reappliedstatus = 1;
                }   
            }
        }        
    }
    
    $bspecial_status = 0;
    $bwait_status = 0;
    if (empty($bcheckapplied) && empty($checkapplied) && empty($bcheckwaitapplied) && empty($checkwaitapplied)) {
        ($bcofull) ? (!empty($bcheckwaitapplied)) ? $bcheckapply = "Applied Already (Waiting - Online)" : $bcheckapply = "Apply Now (Waiting - Online)" : $bcheckapply = "Apply Now (Online)";
        (!empty($bcheckwaitapplied)) ? $bwait_status = 1 : $bwait_status = $bwait_status;
        //$capply_else = false;
        $bcapply = 1;
    } else {
        if (!empty($checkapplied) || !empty($checkwaitapplied) ) {
            $bcheckapply = "Already Applied ( Offline )";
        } else {
            $bcheckapply = "Already Applied ( Online )";
        }
        
        //$capply_else = false;
        $bcapply = 0;
        if ((($bcheckapplied[0]->isremoved == 1 && $bcheckapplied[0]->isreapplied == 0) || ($bcheckwaitapplied[0]->isremoved == 1 && $bcheckwaitapplied[0]->isreapplied == 0)) && $capply) {
            $bcheckapply = "Apply Now ( Online )";
            $bspecial_status = 1;
        }
    }

    $breappliedstatus = 0;
    if (!empty($bcheckapplied) && $bcheckapplied[0]->id) {
        foreach ($bcheckapplied as $bchckappl) {
            // if($chckappl->isremoved == 1 && $chckappl->isreapplied == 1) 
            if ($bchckappl->isreapplied == 1) {
                $breappliedstatus = 1;
            }
        }
    }

    if (!$breappliedstatus) {
        if (!empty($bcheckwaitapplied) && $bcheckwaitapplied[0]->id) {
            foreach ($bcheckwaitapplied as $bchckwappl) {
                // if($chckappl->isremoved == 1 && $chckappl->isreapplied == 1) 
                if ($bchckwappl->isreapplied == 1) {
                    $breappliedstatus = 1;
                }   
            }
        }
    }

    $cssetting = $DB->get_record('course', array('id' => $coursesid));
    //to get course extra settings from courseid
    $exsetting = $DB->get_record('course_extrasettings_general', array('courseid' => $cssetting->id));
    //check enrolled course 
    $context = context_course::instance($coursesid);
    $isEnrolled = is_enrolled($context, $USER, '', true);
    $normalCourse = 0;
    if ($getcoursesdetails->price == 0) {
        $price = 'Free';
    } else {
        $price = sprintf('%s', local_buykart_get_currency_symbol(get_config('local_buykart', 'currency')) . $getcoursesdetails->price);
    }
    if ($getcoursesdetails->price == 0) {
        if ($isEnrolled == 0) {
            $isfreeenrolled = 0;
        } else {
            $isfreeenrolled = 1;
        }
        $isfree = 1;
    } else {
        $isfree = 0;
        $isfreeenrolled = 0;
    }
    if ($exsetting) {
        if ($exsetting->coursemode == 0 || $exsetting->coursemode == 2) {
            $normalCourse = 1;
        } else {
            $batchdetails = $DB->get_records_sql("SELECT * FROM {mpbatch_details} WHERE isdisplayed = 1 AND  courseid = " . $courseid . " ORDER BY batchnumber ASC");

            if (empty($batchdetails)) {
                $normalCourse = 1;
            } else {
                foreach ($batchdetails as $bkey => $bvalue) {
                    if ($bvalue->batchnumber > 1 && $bvalue->isdisplayed == 0) {
                        $normalCourse = 1;
                    }
                }
            }
        }
    }
    //course skills 
    $skills = $exsetting->courseskill;
    if ($skills) {
        $extSkills = $DB->get_records_sql("SELECT s.id, s.skill_required FROM {industry_skill_required} s WHERE s.id IN (" . $skills . ")");
    } else {
        $extSkills = '';
    }
    $skillset = '';
    if ($extSkills) {
        foreach ($extSkills as $skey => $svalue) {
            $skillset .= '<span class="span-technology">' . $svalue->skill_required . '</span>';
        }
    } else {
        $skillset .= "No Skills Found";
    }

    //product price details 
    // $discountval = $DB->get_record_sql('SELECT v.* 
    //     FROM {local_buykart_product} p
    //     JOIN {local_buykart_variation} v ON p.id = v.product_id
    //     WHERE p.course_id = ' . $coursesid);
    $discountval = $DB->get_record('schemedata', array('schemeid' => "$schemeid"), 'scholarship_percentage');
    if ($getcoursesdetails->price == 0) {
        $totalprice = 'FREE';
    } else {
        $discountedPrice = $getcoursesdetails->price - ($getcoursesdetails->price * $discountval->scholarship_percentage / 100);
        
        $totalprice = sprintf('%s', local_buykart_get_currency_symbol(get_config('local_buykart', 'currency')) . $discountedPrice);
   
    }
    if (!!get_config('local_buykart', 'page_catalogue_show_price')) {
        $totalprice = sprintf('%s', local_buykart_get_currency_symbol(get_config('local_buykart', 'currency')) . $discountval->discounted_price);
    }
    //course partner 
    if ($exsetting->coursepartner) {
        $partner_details = $DB->get_record('local_course_partners', array('id' => $exsetting->coursepartner));
        $pfs = get_file_storage();
        $pfiles = $pfs->get_area_files($partner_details->context_id, 'local_course_partners', 'content', $partner_details->item_id, 'id', false);
        foreach ($pfiles as $pfile) {
            $pfilename = $pfile->get_filename();
            if (!$pfilename <> '.') {
                $purl = moodle_url::make_pluginfile_url($pfile->get_contextid(), $pfile->get_component(), $pfile->get_filearea(), $partner_details->item_id, $pfile->get_filepath(), $pfilename);
            }
        }
        $partner_details->purl = $purl;
    } else {
        $partner_details = '';
    }
    //for batches 
    if ($getcoursesdetails->price == 0) {
        $templatecontext['batches'] = getBatches($courseid, 1);
    } else {
        $templatecontext['batches'] = getBatches($coursesid);
    }

    //display content control 
    $displaycontent = $DB->get_record('course_page_content_controls', array('courseid' => $coursesid));
    //print_object($displaycontent);die;

    //course instructor display 
    $instructor_array = [];
    $instructordetails = $DB->get_records('course_instructor', array('courseid' => $courseid, 'deleted' => 0));
    if (!empty($instructordetails)) {
        foreach ($instructordetails as $key => $instructordetail) {
            $instructor_name = $instructordetail->instructor_name;
            $instructor_detail = $instructordetail->about_instructor;
            $imageurl = get_course_instructor_img($courseid, $instructordetail->instructorimg);
            $instructorimg = $imageurl;
            $instructor_array[] = [
                'instructor_name' => $instructor_name,
                'instructordetail' => $instructor_detail,
                'instructor_img' => $instructorimg
            ];
        }
    }
    //student feedback
    $student_array = [];
    $feedbackdetails = $DB->get_records('course_feedback', array('courseid' => $courseid));
    if (!empty($feedbackdetails)) {
        foreach ($feedbackdetails as $key => $feedbackdetail) {
            $user = $DB->get_record('user', array('id' => $feedbackdetail->userid, 'deleted' => 0));
            $user_name = $user->firstname . '-' . $user->last_name;
            $courserating = $feedbackdetail->courserating;
            $userfeedbackdetail = $feedbackdetail->course_content_feedback;
            $userimageurl = \theme_remui\utility::get_user_picture($user, 200);
            $ratingstart = '';
            if ($courserating > 0) {
                for ($i = 1; $i <= $courserating; $i++) {
                    $ratingstart .= ' <span class="fa fa-star checked"></span>';
                }
            }
            $student_array[] = [
                'user_name' => $user_name,
                'courserating' => $ratingstart,
                'userfeedbackdetail' => $userfeedbackdetail,
                'userimageurl' => $userimageurl
            ];
        }
    }
    //faqs 
    $faqs = $DB->get_records('course_faq', ['courseid' => $courseid, 'isdeleted' => 0]);
    $count = 0;
    foreach ($faqs as $key => $value) {
        $count++;
        $array[] = ['id' => $value->id, 'count' => $count, 'question' => $value->question, 'answer' => $value->answer];
    }

    $sql = $DB->get_record_sql("SELECT * FROM {subscription_plan} WHERE deleted = 0 AND plan_period = 365");
    $planid = $sql->id;
    //print_object($instructor_array);die;
    //now display only course related data 
    $getcoursename= $DB->get_record('course', array('id' => $coursesid), 'fullname');

    //$get_user_role = $DB->get_record('role_assignments', array('userid' => $USER->id), 'roleid'); 
    //$get_user_role_id = $get_user_role->roleid;

    $get_user_type = $DB->get_record('usertype', array('userid'=>$USER->id), 'usertype');
    $offline_apply_action = 0;
    $apply_access = 0;
    $enddate_show = 1;
    $batchsize_show = 1;
    if ($get_user_role_id == 5 || $get_user_type->usertype == "Student") {
        $offline_apply_action = 1;
        $apply_access = 1;
        $enddate_show = 0;
        $batchsize_show = 0;
    }

    $mode_online = 1;
    $mode_blended = ($cmode->id == 3) ? 1 : 0;
    $wreadinescheksql = "SELECT ccat.* 
                                FROM {course_categories} ccat
                            WHERE ccat.id IN ( SELECT category FROM {course} WHERE id = $coursesid)";

    $wreadineschek = $DB->get_record_sql($wreadinescheksql);
    $wreadcheck = 0;
    if ($wreadineschek->parent == 219) {
        $wreadcheck = 1;
    }

    // $noofapplys = $DB->get_records('job_seeker_application', array('course_id' => $courseid));
    // $loguserapplyornot = $DB->get_record('job_seeker_application', array('course_id' => $courseid, 'user_id'=>$USER->id));

    if ($cmode->id > 1) {
        if (!$wreadcheck) {
            $noofcoursecenters = $DB->get_records('center_details', array('courseid' => $courseid));
            $offcofull = 0;
            $offcocount = 1;
            $loguserappliedcount = 0;
            foreach ($noofcoursecenters as $noofcoursecent) {
                $checkwaitapplied = $DB->get_records('waiting_list_offline', array('course_id' => $courseid, 'user_id' => $USER->id, 'centerid' => $noofcoursecent->id ));
                $checkwaitapplied2 = ($cmode->id == 3) ? array_values($DB->get_records('waiting_list', array('course_id' => $courseid, 'user_id' => $USER->id ))) : array_values(new stdClass());
                (!empty($checkwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
                // (!empty($checkwaitapplied) || !empty($bcheckwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
                (!empty($checkwaitapplied2)) ? $bwait_status = 1 : $bwait_status = $bwait_status;
                $noofoffapplys = $DB->get_records('job_seeker_offline_application', array('course_id' => $courseid, 'centerid' => $noofcoursecent->id, 'isremoved' => 0));    
                $loguserapplyornot = $DB->get_record('job_seeker_offline_application', array('course_id' => $courseid, 'user_id' => $USER->id, 'centerid' => $noofcoursecent->id));
                if (count($noofoffapplys) >= ($noofcoursecent->noofbatches * $noofcoursecent->batch_size)) {
                    if ($loguserapplyornot->id) {
                        $loguserappliedcount++;
                    }
                    $offcocount = $offcocount;
                } else {
                    $offcocount = 0;
                }
            }
            ($loguserappliedcount == 0 && $offcocount == 1) ? $offcofull = 1 : $offcofull = $offcofull;
        } else {
            $noofcoursecenters = $DB->get_records('center_details', array('courseid' => $courseid));
            $offcofull = 0;
            $offcocount = 1;
            $loguserappliedcount = 0;
            foreach ($noofcoursecenters as $noofcoursecent) {
                $checkwaitapplied = $DB->get_records('waiting_list_offline', array('course_id' => $courseid, 'user_id' => $USER->id, 'centerid' => $noofcoursecent->id ));
                $checkwaitapplied2 = ($cmode->id == 3) ? array_values($DB->get_records('waiting_list', array('course_id' => $courseid, 'user_id' => $USER->id ))) : array_values(new stdClass());
                (!empty($checkwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
                // (!empty($checkwaitapplied) || !empty($bcheckwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
                (!empty($checkwaitapplied2)) ? $bwait_status = 1 : $bwait_status = $bwait_status;
                $noofoffapplys = $DB->get_records('job_seeker_offline_application', array('course_id' => $courseid, 'centerid' => $noofcoursecent->id, 'isremoved' => 0));    
                $loguserapplyornot = $DB->get_record('job_seeker_offline_application', array('course_id' => $courseid, 'user_id' => $USER->id, 'centerid' => $noofcoursecent->id));
                if (count($noofoffapplys) >= ($noofcoursecent->noofbatches * $noofcoursecent->batch_size)) {
                    if ($loguserapplyornot->id) {
                        $loguserappliedcount++;
                    }
                    $offcocount = $offcocount;
                } else {
                    $offcocount = 0;
                }
            }
            ($loguserappliedcount == 0 && $offcocount == 1) ? $offcofull = 1 : $offcofull = $offcofull;
        }
    }

    $jsparray = array();
    if ($cmode->id > 1) {
        if (!$wreadcheck) {
            $mode_online = 0;
            //Form center data array
            $centerssql =   "SELECT cc.*,
                                    dt.name as dtname,
                                    con.name as ctname,
                                    c.startdate as cstartdate,
                                    c.enddate as cenddate
                                    FROM {center_creation} cc
                                    LEFT JOIN {districts} dt ON dt.id = cc.districtid
                                    LEFT JOIN {constituencies} con ON con.id = cc.constid
                                    LEFT JOIN {course} c ON c.id = cc.courseid
                                WHERE cc.courseid = '".$courseid."' ";

            $centerss = $DB->get_records_sql($centerssql);
            $courseapplied = 0;
            if (!empty($centerss)) {
                $sl = 1;
                $appliedcenterid = 0;
                foreach ($centerss as $cent) {
                    // $cenapplyObj = array_values($DB->get_records('job_seeker_offline_application', array('centerid' => $cent->id, 'user_id' => $USER->id, 'course_id' => $courseid)));
                    // $cenapplyObj2 = ($cmode->id == 3) ? array_values($DB->get_records('job_seeker_application', array('user_id' => $USER->id, 'course_id' => $courseid))) : array_values(new stdClass());
                    // $centerFullObj = $DB->get_records('job_seeker_offline_application', array('centerid' => $cent->id, 'course_id' => $courseid, 'isremoved' => 0));
                    // $centerwaitObj = $DB->get_records('waiting_list_offline', array('centerid' => $cent->id, 'user_id' => $USER->id, 'course_id' => $courseid));
                    // $centerwaitObj2 = $DB->get_records('waiting_list', array('user_id' => $USER->id, 'course_id' => $courseid));
                    // // (!empty($cenapplyObj))?$courseapplied = true:$courseapplied = $courseapplied;
                    // (!empty($cenapplyObj) || !empty($cenapplyObj2) || !empty($centerwaitObj)) ? $courseapplied = 1 : $courseapplied = $courseapplied;
                    // (!empty($cent->cstartdate)) ? $cstart = date("d-m-Y", $cent->cstartdate) : $cstart = 'N/A';
                    // (!empty($cent->cenddate)) ? $cend = date("d-m-Y", $cent->cenddate) : $cend = 'N/A';

                    // $availslots = ($cent->batch_size * $cent->noofbatches) - count($centerFullObj);
                    // $onlineapplied = (!empty($cenapplyObj2) || !empty($centerwaitObj2)) ? 1 : 0;
                    $jsparray[] = [
                                    'slno' => $sl, 
                                    'centerid' => $cent->id, 
                                    'district' => ucwords($cent->dtname), 
                                    'consti' => ucwords($cent->ctname), 
                                    'center' => $cent->center, 
                                    'batchsize' => $cent->batch_size,
                                    'cstartdate' => $cstart,
                                    // 'cenddate' => $cend,
                                    // // 'appcheck' => (!empty($cenapplyObj))?true:false,
                                    // 'appcheck' => (!empty($cenapplyObj) || !empty($cenapplyObj2) || !empty($centerwaitObj)) ? 1 : 0,
                                    // 'waitcheck' => (!empty($centerwaitObj)) ? 1 : 0,
                                    // // 'waitcheck' => (!empty($centerwaitObj) || !empty($centerwaitObj2)) ? 1 : 0,
                                    // 'centerfullcheck' => (count($centerFullObj) >= ($cent->batch_size * $cent->noofbatches) && empty($cenapplyObj)) ? 1 : 0,
                                    // 'count' => count($centerFullObj),
                                    // 'availslots' => $availslots,
                                    // 'onlineapplied' => (!empty($cenapplyObj2) || !empty($centerwaitObj2)) ? 1 : 0,
                                    // 'isreapp' => (!empty($cenapplyObj) && $cenapplyObj[count($cenapplyObj)-1]->id) ? $cenapplyObj[count($cenapplyObj)-1]->isreapplied : ((!empty($centerwaitObj) && $centerwaitObj[count($centerwaitObj)-1]->id) ? $centerwaitObj[count($centerwaitObj)-1]->isreapplied : 0),
                                    // 'noofbatches' => $cent->noofbatches
                                ];
                    $sl++;            
                }
            }
        } else {
            $mode_online = 0;
            //Form center data array
            $centerssql =   "SELECT cc.*,
                                    dt.name as dtname,
                                    con.name as ctname,
                                    
                                    -- cd.startdate as cstartdate,
                                    -- cd.starttime as cstarttime,
                                    c.enddate as cenddate,
                                    sm.name as slotname
                                    FROM {center_creation} cc
                                    LEFT JOIN {districts} dt ON dt.id = cd.districtid
                                    LEFT JOIN {constituencies} con ON con.id = cc.constid
                                    LEFT JOIN {course} c ON c.id = cd.courseid
                                    LEFT JOIN {program_slot} ps ON ps.centerid = cd.id
                                    LEFT JOIN {slot_master} sm ON sm.id = ps.slottype
                                WHERE cc.courseid = '".$courseid."' ";
            $centerss = $DB->get_records_sql($centerssql);
            
            $courseapplied = 0;
            if (!empty($centerss)) {
                $sl = 1;
                $appliedcenterid = 0;
                foreach ($centerss as $cent) {
                    // $cenapplyObj = array_values($DB->get_records('job_seeker_offline_application', array('centerid' => $cent->id, 'user_id' => $USER->id, 'course_id' => $courseid)));
                    // $cenapplyObj2 = ($cmode->id == 3) ? array_values($DB->get_records('job_seeker_application', array('user_id' => $USER->id, 'course_id' => $courseid))) : array_values(new stdClass());
                    // $centerFullObj = $DB->get_records('job_seeker_offline_application', array('centerid' => $cent->id, 'course_id' => $courseid, 'isremoved' => 0));
                    // $centerwaitObj = $DB->get_records('waiting_list_offline', array('centerid' => $cent->id, 'user_id' => $USER->id, 'course_id' => $courseid));
                    // $centerwaitObj2 = $DB->get_records('waiting_list', array('user_id' => $USER->id, 'course_id' => $courseid));
                    // (!empty($cenapplyObj))?$courseapplied = true:$courseapplied = $courseapplied;
                    // (!empty($cenapplyObj) || !empty($cenapplyObj2) || !empty($centerwaitObj)) ? $courseapplied = 1 : $courseapplied = $courseapplied;
                    // (!empty($cent->cstartdate)) ? $cstart = date("d-m-Y", $cent->cstartdate) : $cstart = 'N/A';
                    // (!empty($cent->cstarttime)) ? $cstarttime = $cent->cstarttime : $cstarttime = 'N/A';
                    // $colonscount = substr_count($cstarttime, ":");
                    // ($colonscount > 0) ? $cstartampm = explode(":", $cstarttime) : $cstartampm = "";
                    // ($colonscount > 0) ? (($cstartampm[0] <= 12) ? $campm = "AM" : $campm = "PM") : $campm = "";
                    // (!empty($cent->cenddate)) ? $cend = date("d-m-Y", $cent->cenddate) : $cend = 'N/A';

                    // $availslots = ($cent->batch_size * $cent->noofbatches) - count($centerFullObj);
                    // $onlineapplied = (!empty($cenapplyObj2) || !empty($centerwaitObj2)) ? 1 : 0;
                    $jsparray[] = [
                                    'slno' => $sl, 
                                    'centerid' => $cent->id, 
                                    'district' => ucwords($cent->dtname), 
                                    'consti' => ucwords($cent->ctname), 
                                    'center' => $cent->center, 
                                    // 'batchsize' => $cent->batch_size,
                                    // 'cstartdate' => $cstart,
                                    // 'cstarttime' => $cstarttime." ".$campm,
                                    // 'cenddate' => $cend,
                                    // 'slotname' => ucwords($cent->slotname),
                                    // 'weektype' => ($cent->weektype == 1) ? "Weekday" : "Weekend",
                                    // 'noofbatches' => $cent->noofbatches,
                                    // // 'appcheck' => (!empty($cenapplyObj))?true:false,
                                    // 'appcheck' => (!empty($cenapplyObj) || !empty($cenapplyObj2) || !empty($centerwaitObj)) ? 1 : 0,
                                    // 'waitcheck' => (!empty($centerwaitObj)) ? 1 : 0,
                                    // // 'waitcheck' => (!empty($centerwaitObj) || !empty($centerwaitObj2)) ? 1 : 0,
                                    // 'centerfullcheck' => (count($centerFullObj) >= ($cent->batch_size * $cent->noofbatches) && empty($cenapplyObj)) ? 1 : 0,
                                    // 'count' => count($centerFullObj),
                                    'availslots' => $availslots,
                                    // 'onlineapplied' => (!empty($cenapplyObj2) || !empty($centerwaitObj2)) ? 1 : 0,
                                    // // 'isreapp' => (!empty($cenapplyObj) && $cenapplyObj[count($cenapplyObj)-1]->id)?$cenapplyObj[count($cenapplyObj)-1]->isreapplied:0
                                    // 'isreapp' => (!empty($cenapplyObj) && $cenapplyObj[count($cenapplyObj)-1]->id) ? $cenapplyObj[count($cenapplyObj)-1]->isreapplied : ((!empty($centerwaitObj) && $centerwaitObj[count($centerwaitObj)-1]->id) ? $centerwaitObj[count($centerwaitObj)-1]->isreapplied : 0)
                                ];
                    $sl++;
                }
            }
        }   
    }

    //Certification Partner Logo
    $certUrlObj = $DB->get_record('course_certificate_partners', array('product_id' => $productid));
    if ($certUrlObj->id && $certUrlObj->filename) {
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($certUrlObj->itemid);
        if ($file) {
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), 0, $file->get_filepath(), $file->get_filename(), false);
            $certurl = $url->out();
        } else {
            $certurl = '';
        }
        $certimageurl = "<img class = 'ml-80' src = $certurl style = 'width:100px;'/>";
        $certFlag = 1;   
    } else {
        // $certurl = 'images/purple_default.png';
        $certurl = '';
        $certimageurl = "<img class='ml-80' src = $certurl style = 'width:100px;'/>";
        $certFlag = 0;
    }
    
    (!empty($cssetting->startdate)) ? $cstartshow = date("d/m/Y", $cssetting->startdate) : $cstartshow = "";
    $cstartshow = (!empty($getcoursesdetails->cdispdate)) ? date("d/m/Y", $getcoursesdetails->cdispdate) : $cstartshow;
    (!empty($cssetting->enddate)) ? $cendshow = date("d/m/Y", $cssetting->enddate) : $cendshow = "";

    $templatecontext['product_name'] =  $getcoursename->fullname;
    //course summery from course edit page 
    $templatecontext['en_section1'] = $displaycontent->en_section1;
    $templatecontext['en_coursename'] = $displaycontent->coursename;
    $templatecontext['en_course_desc'] = $displaycontent->course_desc;
    $templatecontext['en_course_vurl'] = $displaycontent->course_vurl;
    $templatecontext['en_applynow'] = $displaycontent->applynow;
    $templatecontext['en_course_overview'] = $displaycontent->course_overview;
    $templatecontext['en_section2'] = $displaycontent->en_section2;
    $templatecontext['en_learning_outcome'] = $displaycontent->learning_outcome;
    $templatecontext['en_pricebox'] = $displaycontent->pricebox;
    $templatecontext['en_syllabus'] = $displaycontent->syllabus;
    $templatecontext['en_download_syllabus'] = $displaycontent->download_syllabus;
    $templatecontext['en_section3'] = $displaycontent->en_section3;
    $templatecontext['en_course_desc_s3'] = $displaycontent->course_desc_s3;
    $templatecontext['en_course_objective'] = $displaycontent->course_objective;
    $templatecontext['en_course_skill'] = $displaycontent->course_skill;
    $templatecontext['en_course_content'] = $displaycontent->course_content;
    $templatecontext['en_section4'] = $displaycontent->en_section4;
    $templatecontext['en_partner'] = $displaycontent->partner;
    $templatecontext['en_certificate1'] = $displaycontent->certificate1;
    $templatecontext['en_content_partner'] = $displaycontent->content_partner;
    $templatecontext['en_section5'] = $displaycontent->en_section5;
    $templatecontext['en_section6'] = $displaycontent->en_section6;
    $templatecontext['en_section7'] = $displaycontent->en_section7;
    $templatecontext['en_section8'] = $displaycontent->en_section8;
    $templatecontext['summary'] = substr($cssetting->summary, 0, 300);
    $templatecontext['summary11'] = substr($cssetting->summary, 300);
    $templatecontext['summary12'] = strip_tags(substr($cssetting->summary, 0, 300));
    $templatecontext['summary13'] = strip_tags(substr($cssetting->summary, 300));
    $templatecontext['summary1'] = substr($cssetting->summary, 0, 134);
    $templatecontext['summary2'] = substr($cssetting->summary, 134);
    //end of display content
    $templatecontext['satisfied_learners'] = '';
    $templatecontext['description'] = $u_learn;
    //to get data from course extrasettings
    $templatecontext['learning_outcome'] = $exsetting->audience;
    $templatecontext['enrolled_instructors'] = get_enrolled_instructors($coursesid);
    $templatecontext['price'] = $getcoursesdetails->price;
    $templatecontext['discount'] = $discountval->percentage;
    $templatecontext['discounted_price'] = $totalprice;
    $templatecontext['enable_discount'] = $discountval->enable_discount;
    $templatecontext['enable_emi'] = $discountval->enable_emi;
    $templatecontext['emi_company'] = '';
    $templatecontext['url'] = '';
    $templatecontext['rating'] = '';
    $templatecontext['certurl'] = $certurl;
    
    $ratesql = array_values($DB->get_records('feedskillpartner', array('courseid' => $courseid)));
    $totrate = 0;
    $finalstatus = 0;
    $usrcount = 0;
    if (!empty($ratesql) && $ratesql[0]->id) {
        foreach ($ratesql as $ratess) {
            $totrate += $ratess->que1 + $ratess->que2 + $ratess->que3 + $ratess->que4;
            $usrcount++;
        }
        $finalstatus = round($totrate / ($usrcount * 4));
    }

    switch ($finalstatus) {
        case 1:
        $finalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 2:
        $finalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 3:
        $finalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 4:
        $finalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 5:
        $finalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>';
        break;
        default:
        $finalstatus = '<span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
    }

    $templatecontext['stardata'] = $finalstatus;

    $ownrating = $DB->get_record_sql("SELECT courserating FROM {course_feedback} WHERE courseid = '".$courseid."' AND userid = '".$USER->id."'");

    $ownrating = $ownrating->courserating;
    $ratingown = $ownrating;
    if ($ratingown == 5 ) {
        $ownfinalstatus = 5;
    } else if ($ratingown <= 4 & $ratingown>3) {
        $ownfinalstatus = 4;
    } else if ($ratingown <= 3 & $ratingown>2) {
        $ownfinalstatus = 3;
    } else if ($ratingown <=2 & $ratingown>1) {
        $ownfinalstatus = 2;
    } else if ($ratingown == 1) {
        $ownfinalstatus = 1;
    } else {
        $ownfinalstatus = 0;
    }
    switch ($ownfinalstatus) {
        case 1:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 2:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 3:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 4:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star"></span>';
        break;
        case 5:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>';
        break;
        default:
        $ownfinalstatus = '<span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>
                        <span class="fa fa-star"></span>';
    }
    $templatecontext['ownstardata'] = $ownfinalstatus;
    //print_object($templatecontext);die;
    //course content from course extra settings page 

    //section 3
    //course objective from course extra setting page 
    $templatecontext['objective'] = substr($exsetting->whatsinside, 0, 134);
    $templatecontext['objective1'] = substr($exsetting->whatsinside, 134);
    $templatecontext['tableofcontent'] = substr($exsetting->tableofcontent, 0, 134);
    $templatecontext['tableofcontent1'] = substr($exsetting->tableofcontent, 134);
    $templatecontext['skillset'] = substr(rtrim(trim($skillset), ','), 0, 380);
    $templatecontext['skillset1'] = substr(rtrim(trim($skillset), ','), 381);
    //section 4
    $templatecontext['partner_details'] = $partner_details;
    $templatecontext['partner_url'] = $partner_details->purl;
    $templatecontext['partner_name'] = $partner_details->partner_name;
    $templatecontext['partner_description'] = $partner_details->description;


    //$templatecontext['partner_description_ribbon'] = substr($partner_details->description,0,40);
    $templatecontext['partner_description_ribbon'] = implode(' ', array_slice(explode(' ', $partner_details->description), 0, 12)) . ".";
    $templatecontext['product_certificate'] = get_product_certificatekdisc($coursesid);
    $templatecontext['student_reviews'] = '';
    $templatecontext['faq'] = $faqs;
    $templatecontext['faqs'] = $array;
    $templatecontext['course_id'] = $courseid;

    $templatecontext['coursesid'] = $coursesid;
    $templatecontext['course_creator_id'] = $jskUsrId; //$course_creator_id;
    $templatecontext['checkapply'] = $checkapply;
    $templatecontext['bcheckapply'] = $bcheckapply;
    $templatecontext['capply'] = $capply;
    $templatecontext['bcapply'] = $bcapply;

    $templatecontext['virtual_class_count'] = '';
    $templatecontext['quiz_count'] = '';
    $templatecontext['ppt_count'] = '';
    $templatecontext['course_includes'] = '';
    $templatecontext['course_list'] = '';
    $templatecontext['productid'] = $productid;
    $templatecontext['planid'] = $planid;
    $templatecontext['in_cartt'] = '';
    $templatecontext['cfg'] = $CFG->wwwroot;
    $templatecontext['isEnrolled'] = $isEnrolled;
    $templatecontext['normalCourse'] = $normalCourse;
    $templatecontext['syllbus_content'] = get_product_syllbus_contentkdisc($coursesid);
    $templatecontext['course_banner'] = get_course_bannerkdisc($coursesid);
    $templatecontext['videourl'] = $exsetting->videourl;
    $templatecontext['apply_access'] = $apply_access;
    $templatecontext['cour_criteria'] = $co_criteria;
    $templatecontext['c_criteria'] = $c_criteria;
    $templatecontext['mode_online'] = $mode_online;
    $templatecontext['mode_blended'] = $mode_blended;
    $templatecontext['course_centers'] = $jsparray;
    $templatecontext['wreadcheck'] = $wreadcheck;
    $templatecontext['cofull'] = $cofull;
    $templatecontext['bcofull'] = $bcofull;
    $templatecontext['offline_apply_action'] = $offline_apply_action;
    $templatecontext['enddate_show'] = $enddate_show;
    $templatecontext['cstartshow'] = (!$wreadcheck) ? $cstartshow = $cstartshow : $cstartshow = "";
    $templatecontext['cendshow'] = $cendshow;
    $templatecontext['courseapplied'] = $courseapplied;
    $templatecontext['batchsize_show'] = $batchsize_show;
    $templatecontext['offcofull'] = $offcofull;
    $templatecontext['special_status'] = $special_status;
    $templatecontext['bspecial_status'] = $bspecial_status;
    $templatecontext['reappliedstatus'] = $reappliedstatus;
    $templatecontext['breappliedstatus'] = $breappliedstatus;
    $templatecontext['wait_status'] = $wait_status;
    $templatecontext['bwait_status'] = $bwait_status;

    //free and enrolled courses 
    $templatecontext['isfree'] =  $isfree;
    $templatecontext['isfreeenrolled'] = $isfreeenrolled;
    $templatecontext['onlineapplied'] = $onlineapplied;
    //check user is loggedin or not
    if (isloggedin()) {
        $templatecontext['isloggedin'] = 1;
    } else {
        $templatecontext['isloggedin'] = 0;
    }
    //emi code is added by prashant
    $product = $DB->get_record('local_buykart_variation', array('product_id' => $productid));
    $leadid = '';
    $emistoreleadid = $DB->get_record('local_emi_commonstatus', array('meta_data' => $USER->email));
    if (!empty($emistoreleadid->lead_id)) {
        $leadid = $emistoreleadid->lead_id;
    }

    if (isloggedin()) {
        $record = $DB->get_record('local_emiguestuser', array('email' => $USER->email));
        $templatecontext['userid'] = $record->id;
        $templatecontext['loan_amount'] = $product->price;
        $templatecontext['email_id'] = $USER->email;
        $templatecontext['phone'] = $USER->phone1;
        $templatecontext['last_name'] = $USER->lastname;
        $templatecontext['firstname'] = $USER->firstname;
        $templatecontext['logintype'] = 'itrack';
    } else {
        //if(!empty($emiuser)){
        $emiuser = $DB->get_record('local_emiguestuser', array('email' => $_SESSION['emiuseremail']));
        // $templatecontext['userid'] =   $_SESSION['userid'];
        // $templatecontext['loan_amount'] =$_SESSION['emiuserloanamount'];
        // $templatecontext['email_id'] = $_SESSION['emiuseremail'];  
        // $templatecontext['phone'] = $_SESSION['emiuserphone'];
        // $templatecontext['last_name'] = $_SESSION['emiuserlastname']; 
        $templatecontext['logintype'] = 'guest';
        //}
        //session_unset();
    }
    //instructor details 
    $templatecontext['instructor'] = $instructor_array;
    $templatecontext['studentreview'] = $student_array;
    $templatecontext['fpgehilite'] = $fpgehilite;
    $templatecontext['lpgehilite'] = $lpgehilite;
    $templatecontext['pagemsg'] = $pagemsg;
} else {
    $co_critObj = $DB->get_record('course_criteria_approved', array('product_id' => $productid), 'elligi');
    $co_criteria = $co_critObj->elligi;
    if (!empty($co_criteria)) {
        $c_criteria = 1;
    } else {
        $c_criteria = 0;
    }
    $coursedeatilsql =  "SELECT c.*,
                                ceg.*,
                                lbp.*,
                                lbv.*,
                                lcp.partner_name as coursepartner,
                                cmap.datetodisplay as cdispdate
                                FROM {course_approved} c
                                LEFT JOIN {course_extrasettings_general_approved} ceg ON ceg.courseid = c.old_id
                                LEFT JOIN {local_buykart_product_approved} lbp ON lbp.course_id = c.old_id
                                LEFT JOIN {local_buykart_variation_approved} lbv on lbv.product_id = lbp.old_id
                                LEFT JOIN {local_course_partners} lcp ON ceg.coursepartner = lcp.id
                                LEFT JOIN {course_mapped} cmap ON cmap.courseid = c.old_id
                            WHERE lbp.old_id = '".$productid."' ";

    $getcoursesdetails = $DB->get_record_sql($coursedeatilsql);
    $coursesid = $getcoursesdetails->course_id;

    // $get_course_creator = $DB->get_record('logstore_standard_log', array('courseid' => $coursesid, 'action' => 'created'), 'userid'); 
    // $course_creator_id = $get_course_creator->userid;
    $get_course_creator = $DB->get_record('local_course_creator', array('courseid' => $coursesid), 'userid');

    $course_creator_id = ($get_course_creator->userid) ? $get_course_creator->userid : 0;

    // $get_user_role = $DB->get_record('role_assignments', array('userid' => $USER->id), 'roleid'); 
    // $get_user_role_id = $get_user_role->roleid;

    if ($course_creator_id == 2) {
        $getcprecord = $DB->get_record('course_extrasettings_general_approved', array('courseid' => $coursesid), 'coursepartner');
        $cpId = $getcprecord->coursepartner; 

        $cpNameRec = $DB->get_record('local_course_partners', array('id' => $cpId), 'partner_name');
        $cpName = strtoupper(trim($cpNameRec->partner_name));
        $cpName = str_replace(" ", "_", $cpName);
        
        $cpuserrecsql = "SELECT id,
                                username
                                FROM {user}
                            WHERE UPPER(username) = '$cpName'";
        $cpuserrec = $DB->get_record_sql($cpuserrecsql);
        $cpuserID = $cpuserrec->id;
        $jskUsrId = $cpuserID;
    } else {
        $jskUsrId = $course_creator_id;    
    }

    $productsid = $productid;
    
    //to get courseid from product id 
    $courseid =  $coursesid;

    $cmodesql = "SELECT cd.mode_id 
                    FROM {center_details} cd
                WHERE cd.courseid = '".$courseid."' ";
    $cmode = $DB->get_record_sql($cmodesql);
    
    $cssetting = $DB->get_record('course_approved', array('old_id' => $coursesid));

    //to get course extra settings from courseid
    $exsetting = $DB->get_record('course_extrasettings_general_approved', array('courseid' => $cssetting->old_id));
    // print_object($exsetting);
    // die();
    //check enrolled course 
    $context = context_course::instance($coursesid);
    $isEnrolled = is_enrolled($context, $USER, '', true);
    $normalCourse = 0;

    if ($getcoursesdetails->price == 0) {
        $price = 'Free';
    } else {
        $price = sprintf('%s', local_buykart_get_currency_symbol(get_config('local_buykart', 'currency')) . $getcoursesdetails->price);
    }

    if ($getcoursesdetails->price == 0) {
        if ($isEnrolled == 0) {
            $isfreeenrolled = 0;
        } else {
            $isfreeenrolled = 1;
        }
        $isfree = 1;
    } else {
        $isfree = 0;
        $isfreeenrolled = 0;
    }

    if ($exsetting) {
        if ($exsetting->coursemode == 0 || $exsetting->coursemode == 2) {
            $normalCourse = 1;
        } else {
            $batchdetails = $DB->get_records_sql("SELECT * FROM {mpbatch_details} WHERE isdisplayed = 1 AND  courseid = " . $courseid . " ORDER BY batchnumber ASC");

            if (empty($batchdetails)) {
                $normalCourse = 1;
            } else {
                foreach ($batchdetails as $bkey => $bvalue) {
                    if ($bvalue->batchnumber > 1 && $bvalue->isdisplayed == 0) {
                        $normalCourse = 1;
                    }
                }
            }
        }
    }

    //course skills 
    $skills = $exsetting->courseskill;
    if ($skills) {
        $extSkills = $DB->get_records_sql(
                                            "SELECT s.id,
                                                    s.skill_required
                                                    FROM {industry_skill_required} s
                                                WHERE s.id IN (" . $skills . ")"
                                );
    } else {
        $extSkills = '';
    }
    $skillset = '';
    if ($extSkills) {
        foreach ($extSkills as $skey => $svalue) {
            $skillset .= '<span class="span-technology">' . $svalue->skill_required . '</span>';
        }
    } else {
        $skillset .= "No Skills Found";
    }

    //product price details 
    $discountval = $DB->get_record('schemedata', array('schemeid' => "$schemeid"), 'scholarship_percentage');
    if ($getcoursesdetails->price == 0) {
        $totalprice = 'FREE';
    } else {
        $discountedPrice = $getcoursesdetails->price - ($getcoursesdetails->price * $discountval->scholarship_percentage / 100);
        
        $totalprice = sprintf('%s', local_buykart_get_currency_symbol(get_config('local_buykart', 'currency')) . $discountedPrice);
   
    } // print_object($totalprice);die();
    // print_object($discountval);
    // die();
    // if (!!get_config('local_buykart', 'page_catalogue_show_price')) {
    //     $totalprice = sprintf('%s', local_buykart_get_currency_symbol(get_config('local_buykart', 'currency')) . $discountval->scholarship_percentage);
    // }
    // if ($discountval) {
    //     $totalprice = 
    // }

    //course partner 
    if ($exsetting->coursepartner) {
        $partner_details = $DB->get_record('local_course_partners', array('id' => $exsetting->coursepartner));
        $pfs = get_file_storage();
        $pfiles = $pfs->get_area_files($partner_details->context_id, 'local_course_partners', 'content', $partner_details->item_id, 'id', false);
        foreach ($pfiles as $pfile) {
            $pfilename = $pfile->get_filename();
            if (!$pfilename <> '.') {
                $purl = moodle_url::make_pluginfile_url($pfile->get_contextid(), $pfile->get_component(), $pfile->get_filearea(), $partner_details->item_id, $pfile->get_filepath(), $pfilename);
            }
        }
        $partner_details->purl = $purl;
    } else {
        $partner_details = '';
    }
    //for batches 
    if ($getcoursesdetails->price == 0) {
        $templatecontext['batches'] = getBatches($courseid, 1);
    } else {
        $templatecontext['batches'] = getBatches($coursesid);
    }

    //display content control 
    $displaycontent = $DB->get_record('course_page_content_controls_approved', array('courseid' => $coursesid));
    //print_object($displaycontent);die;

    //course instructor display 
    $instructor_array = [];
    $instructordetails = $DB->get_records('course_instructor', array('courseid' => $courseid, 'deleted' => 0));
    if (!empty($instructordetails)) {
        foreach ($instructordetails as $key => $instructordetail) {
            $instructor_name = $instructordetail->instructor_name;
            $instructor_detail = $instructordetail->about_instructor;
            $imageurl = get_course_instructor_img($courseid, $instructordetail->instructorimg);
            $instructorimg = $imageurl;
            $instructor_array[] = [
                'instructor_name' => $instructor_name,
                'instructordetail' => $instructor_detail,
                'instructor_img' => $instructorimg
            ];
        }
    }
    //student feedback
    $student_array = [];
    $feedbackdetails = $DB->get_records('course_feedback', array('courseid' => $courseid));
    if (!empty($feedbackdetails)) {
        foreach ($feedbackdetails as $key => $feedbackdetail) {
            $user = $DB->get_record('user', array('id' => $feedbackdetail->userid, 'deleted' => 0));
            $user_name = $user->firstname . '-' . $user->last_name;
            $courserating = $feedbackdetail->courserating;
            $userfeedbackdetail = $feedbackdetail->course_content_feedback;
            $userimageurl = \theme_remui\utility::get_user_picture($user, 200);
            $ratingstart = '';
            if ($courserating > 0) {
                for ($i = 1; $i <= $courserating; $i++) {
                    $ratingstart .= ' <span class="fa fa-star checked"></span>';
                }
            }
            $student_array[] = [
                'user_name' => $user_name,
                'courserating' => $ratingstart,
                'userfeedbackdetail' => $userfeedbackdetail,
                'userimageurl' => $userimageurl
            ];
        }
    }
    //faqs 
    $faqs = $DB->get_records('course_faq', ['courseid' => $courseid, 'isdeleted' => 0]);
    $count = 0;
    foreach ($faqs as $key => $value) {
        $count++;
        $array[] = ['id' => $value->id, 'count' => $count, 'question' => $value->question, 'answer' => $value->answer];
    }

    $sql = $DB->get_record_sql("SELECT * FROM {subscription_plan} WHERE deleted = 0 AND plan_period = 365");
    $planid = $sql->id;
    //print_object($instructor_array);die;
    //now display only course related data 
    $getcoursename= $DB->get_record('course_approved', array('old_id' => $coursesid), 'fullname');

    //$get_user_role = $DB->get_record('role_assignments', array('userid' => $USER->id), 'roleid'); 
    //$get_user_role_id = $get_user_role->roleid;

    $normapplied = getnormappstatus($coursesid);
    $get_user_type = $DB->get_record('usertype', array('userid' => $USER->id), 'usertype');
    $offline_apply_action = 0;
    $apply_access = 0;
    $enddate_show = 1;
    $batchsize_show = 1;
    if ($get_user_role_id == 5  || $get_user_type->usertype == 'Student') {
        $offline_apply_action = 1;
        $apply_access = 1;
        $enddate_show = 0;
        $batchsize_show = 0;
    }

    // $mode_online = 1;
    // $mode_blended = ($cmode->id == 3) ? 1 : 0;
    $wreadinescheksql = "SELECT ccat.* 
                                FROM {course_categories} ccat
                            WHERE ccat.id IN ( SELECT category FROM {course_approved} WHERE old_id = $coursesid)";

    $wreadineschek = $DB->get_record_sql($wreadinescheksql);
    $wreadcheck = 0;
    if ($wreadineschek->parent == 219) {
        $wreadcheck = 1;
    }

    // $noofapplys = $DB->get_records('job_seeker_application', array('course_id' => $courseid));
    // $loguserapplyornot = $DB->get_record('job_seeker_application', array('course_id' => $courseid, 'user_id'=>$USER->id));

    // if ($cmode->id > 1) {
    //     if (!$wreadcheck) {
    //         $noofcoursecenters = $DB->get_records('center_details', array('courseid' => $courseid));
    //         $offcofull = 0;
    //         $offcocount = 1;
    //         $loguserappliedcount = 0;
    //         foreach ($noofcoursecenters as $noofcoursecent) {
    //             $checkwaitapplied = $DB->get_records('waiting_list_offline', array('course_id' => $courseid, 'user_id' => $USER->id, 'centerid' => $noofcoursecent->id ));
    //             $checkwaitapplied2 = ($cmode->id == 3) ? array_values($DB->get_records('waiting_list', array('course_id' => $courseid, 'user_id' => $USER->id ))) : array_values(new stdClass());
    //             (!empty($checkwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
    //             // (!empty($checkwaitapplied) || !empty($bcheckwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
    //             (!empty($checkwaitapplied2)) ? $bwait_status = 1 : $bwait_status = $bwait_status;
    //             $noofoffapplys = $DB->get_records('job_seeker_offline_application', array('course_id' => $courseid, 'centerid' => $noofcoursecent->id, 'isremoved' => 0));    
    //             $loguserapplyornot = $DB->get_record('job_seeker_offline_application', array('course_id' => $courseid, 'user_id'=>$USER->id, 'centerid' => $noofcoursecent->id));
    //             if (count($noofoffapplys) >= ($noofcoursecent->noofbatches * $noofcoursecent->batch_size)) {
    //                 if ($loguserapplyornot->id) {
    //                     $loguserappliedcount++;
    //                 }
    //                 $offcocount = $offcocount;
    //             } else {
    //                 $offcocount = 0;
    //             }
    //         }
    //         ($loguserappliedcount == 0 && $offcocount == 1) ? $offcofull = 1 : $offcofull = $offcofull;
    //     } else {
    //         $noofcoursecenters = $DB->get_records('center_details', array('courseid' => $courseid));
    //         $offcofull = 0;
    //         $offcocount = 1;
    //         $loguserappliedcount = 0;
    //         foreach ($noofcoursecenters as $noofcoursecent) {
    //             $checkwaitapplied = $DB->get_records('waiting_list_offline', array('course_id' => $courseid, 'user_id' => $USER->id, 'centerid' => $noofcoursecent->id ));
    //             $checkwaitapplied2 = ($cmode->id == 3) ? array_values($DB->get_records('waiting_list', array('course_id' => $courseid, 'user_id' => $USER->id ))) : array_values(new stdClass());
    //             (!empty($checkwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
    //             // (!empty($checkwaitapplied) || !empty($bcheckwaitapplied)) ? $wait_status = 1 : $wait_status = $wait_status;
    //             (!empty($checkwaitapplied2)) ? $bwait_status = 1 : $bwait_status = $bwait_status;
    //             $noofoffapplys = $DB->get_records('job_seeker_offline_application', array('course_id' => $courseid, 'centerid' => $noofcoursecent->id, 'isremoved' => 0));    
    //             $loguserapplyornot = $DB->get_record('job_seeker_offline_application', array('course_id' => $courseid, 'user_id'=>$USER->id, 'centerid' => $noofcoursecent->id));
    //             if (count($noofoffapplys) >= ($noofcoursecent->noofbatches * $noofcoursecent->batch_size)) {
    //                 if ($loguserapplyornot->id) {
    //                     $loguserappliedcount++;
    //                 }
    //                 $offcocount = $offcocount;
    //             } else {
    //                 $offcocount = 0;
    //             }
    //         }
    //         ($loguserappliedcount == 0 && $offcocount == 1) ? $offcofull = 1 : $offcofull = $offcofull;
    //     }
    // }
    $mode_online_array = array();
    $jsparray = array();
    $course_applied = "";  
    $scheme_status = 0;
    $reapp_stts = 0; 
    $chckcour = 0;
    $courseapplied = $DB->get_record('job_seeker_scheme_application', array('userid' => $USER->id));
    if (!$normapplied) {
        if ($courseapplied) {
            $course_applied = 1;
            if ($courseapplied->courseid == $courseid) {
                $chckcour = 1;
            } else {
                $chckcour = 0;
            }
            if ($courseapplied->isremoved == 1) {
                $scheme_status = 1;
            } else {
                $scheme_status = 0;
            }
            if ($courseapplied->isreapplied == 1) {
                $reapp_stts = 1;
            } else {
                $reapp_stts = 0;
            }
        } else { 
            $course_applied = 0;
        }
    }
        
    if ($cmode->mode_id == 1) {
        $mode_online = 1;
        $schemedata = $DB->get_record('schemedata', array('schemeid' => "$schemeid"), 'startdate');
        $totalslot = $DB->get_record('schememangement_mapping', array('courseid' => $courseid, 'schemeid' => "$schemeid"), 'batchsize');
        $record = $DB->count_records('job_seeker_scheme_application', array('courseid' => $courseid, 'schemeid' => "$schemeid"));
        $availableslots = $totalslot->batchsize-$record;
        if ($availableslots === 0) {
            $batchfull = 1;
            $availableslots = 0;
        } else {
                $batchfull = 0; 
        }
        $mode_online_array[] = [
            'startdate' => $schemedata->startdate,
            'available_slots' => $availableslots,
            'batchfull' => $batchfull,

        ];

    } else {

        if ($cmode->mode_id == 3) {
            $mode_online = 0;
            $mode_blended = 1;
            $schemedata = $DB->get_record('schemedata', array('schemeid' => "$schemeid"), 'startdate');
            $totalslot = $DB->get_record('schememangement_mapping', array('courseid' => $courseid, 'schemeid' => "$schemeid"), 'batchsize');
            $record = $DB->count_records('job_seeker_scheme_application', array('courseid' => $courseid, 'schemeid' => "$schemeid"));
            $availableslots = $totalslot->batchsize-$record;
            if ($availableslots === 0) {
                $batchfull = 1;
                $availableslots = 0;
            } else {
                    $batchfull = 0; 
            }
            $mode_online_array[] = [
                'startdate' => $schemedata->startdate,
                'available_slots' => $availableslots,
                'batchfull' => $batchfull,
                'centerid' => 0
            ] ;

        }
        if ($cmode->mode_id == 2) {
            $mode_online = 0;
            $mode_blended = 0;
        }
        
        $srchfilter = ($srch) ? " AND ( d.name LIKE '%$srch%' OR  c.name LIKE '%$srch%' OR cc.center LIKE '%$srch%' )" : "";

        //Form center data array
        $centerstotsql =    "SELECT cc.*,
                                    d.name as dtname,
                                    c.name as ctname,
                                    sm.batchsize,
                                    s.startdate
                                    FROM {center_creation} cc 
                                    LEFT JOIN {districts} d ON d.id = cc.districtid 
                                    LEFT JOIN {constituencies} c ON c.id = cc.constid
                                    LEFT JOIN {schememangement_mapping} sm ON sm.schemeid = cc.schemeid
                                    LEFT JOIN {schemedata} s ON s.schemeid = sm.schemeid
                                WHERE cc.courseid = $courseid
                                AND cc.status = 1 
                                AND cc.schemeid = '$schemeid'
                                $srchfilter";
        
        $totcentsql = $DB->get_records_sql($centerstotsql); // $_SESSION['totalwrprecs'];
        $totcententries = count($totcentsql);
        $totcentpages = ceil($totcententries / $centplim);
        $centpdata = get_page_numbers($totcentpages, $pge);

        $fpgehilite = ($pge == 1) ? 'fpgehilite' : '';
        $lpgehilite = ($pge == $totcentpages) ? 'lpgehilite' : '';
        $lastentri = ( ($pge * $centplim) > $totcententries ) ? $totcententries : ($pge * $centplim);
        $pagemsg = ((($pge - 1) * $centplim ) + 1)." to ".$lastentri." Entries ";
        
        $totcentcomma = implode(",", array_keys($totcentsql));
        
        if ($totcentcomma) {
            $capplychecksql =   "SELECT *
                                        FROM (  
                                                SELECT `jsa`.`id`,
                                                        `jsa`.`timecreated`,
                                                        `jsa`.`timemodified`,
                                                        `jsa`.`isremoved`,
                                                        `jsa`.`isreapplied`,
                                                        `jsa`.`centerid`
                                                        FROM {job_seeker_scheme_application} as `jsa`
                                                    WHERE `jsa`.`centerid` IN ($totcentcomma)
                                                    AND   `jsa`.`userid` = $USER->id
                                                ) AS `tot` ORDER BY `timecreated`,`timemodified` DESC LIMIT 0,1";
            
            $capplychs = array_values($DB->get_records_sql($capplychecksql));
            
            // Applied Center Details Start
            $appliedcentarr = array();
            if (!empty($capplychs) && $capplychs[0]->id && (!$capplychs[0]->isremoved || $capplychs[0]->isreapplied)) {
                $appliedcentsql =   "SELECT cc.*,
                                            d.name as dtname,
                                            c.name as ctname,
                                            sm.batchsize,
                                            s.startdate
                                            FROM {center_creation} cc
                                            LEFT JOIN {districts} d ON d.id = cc.districtid
                                            LEFT JOIN {constituencies} c ON c.id = cc.constid
                                            -- LEFT JOIN {course_approved} c ON c.old_id = cc.courseid
                                            LEFT JOIN {schememangement_mapping} sm ON sm.schemeid = cc.schemeid
                                            LEFT JOIN {schemedata} s ON s.schemeid = sm.schemeid
                                        WHERE cc.courseid = $courseid
                                        AND cc.status = 1 
                                        AND cc.schemeid = '$schemeid'
                                        AND cc.id = '".$capplychs[0]->centerid."'";            

                $appliedcents = $DB->get_record_sql($appliedcentsql);
                if (!empty($appliedcents) && $appliedcents->id) {
                    $appliedtotalslot = $DB->get_record('schememangement_mapping', array('courseid' => $courseid, 'schemeid' => "$schemeid"), 'batchsize');
                    $applieddrecord = $DB->count_records('job_seeker_scheme_application', array('courseid' => $courseid, 'schemeid' => "$schemeid"));
                    $appliedavailableslots = $appliedtotalslot->batchsize - $applieddrecord;
                    if ($appliedavailableslots === 0) {
                        $appliedbatchfull = 1;
                        $appliedavailableslots = 0;
                    } else {
                        $appliedbatchfull = 0; 
                    }
                    $sl = 1;
                    $appliedothercenter = $DB->get_record('job_seeker_scheme_application', array('userid' => $USER->id, 'courseid' => $courseid, 'schemeid' => $schemeid, 'centerid' => $appliedcents->id));
                    $appliedreothercenter = $DB->get_record('job_seeker_scheme_application', array('userid' => $USER->id, 'courseid' => $courseid, 'schemeid' => $schemeid, 'centerid' => $appliedcents->id, 'isreapplied' => 1));
                    $appliedcentarr[] = [
                                    'slno' => $sl, 
                                    'centerid' => $appliedcents->id, 
                                    'district' => ucwords($appliedcents->dtname), 
                                    'consti' => ucwords($appliedcents->ctname), 
                                    'center' => $appliedcents->center, 
                                    'batchsize' => $appliedcents->batchsize,
                                    'cstartdate' => $appliedcents->startdate,
                                    'course_applied' => $course_applied,
                                    'othercenter' => $othercenter_applied,
                                    'othercenter' => (empty($appliedothercenter)) ? ($courseapplied->centerid == 0 ? 'Online Applied' : 'Other Center Applied') : 'Already Applied',
                                    'availslots' => $appliedavailableslots,
                                    'batchfull' => $appliedbatchfull,
                                    'scheme_status' => $scheme_status,
                                    'reapp_stts' => $reapp_stts,
                                    'reothercenter' => (empty($appliedreothercenter)) ? ($courseapplied->centerid == 0 ? 'Online Reapplied' : 'Other Center Reapplied') : 'Reapplied',
                                    'chckcour' => $chckcour,
                                ];
                }
            }

            // Applied Center Details End
        }

        $centerssql = "SELECT cc.*,
                              d.name as dtname,
                              c.name as ctname,
                              sm.batchsize,
                              s.startdate
                              FROM {center_creation} cc 
                              LEFT JOIN {districts} d ON d.id = cc.districtid 
                              LEFT JOIN {constituencies} c ON c.id = cc.constid
                              LEFT JOIN {schememangement_mapping} sm ON sm.schemeid = cc.schemeid AND sm.courseid = cc.courseid
                              LEFT JOIN {schemedata} s ON s.schemeid = sm.schemeid
                        WHERE cc.courseid = $courseid
                        AND cc.status = 1 
                        AND cc.schemeid = '$schemeid'
                        $srchfilter
                        LIMIT $start, $limit";

        $centerss = $DB->get_records_sql($centerssql);
        if (empty($centerss)) {
            $nocenter = 1;
        } else {
            $nocenter = 0; 
            $totalslot = $DB->get_record('schememangement_mapping', array('courseid' => $courseid, 'schemeid' => "$schemeid"), 'batchsize');
            $record = $DB->count_records('job_seeker_scheme_application', array('courseid' => $courseid, 'schemeid' => "$schemeid"));
            $availableslots = $totalslot->batchsize - $record;
            if ($availableslots === 0) {
                $batchfull = 1;
                $availableslots = 0;
            } else {
                $batchfull = 0; 
            }
            $sl = ($start + 1);
            $appliedcenterid = 0;
            foreach ($centerss as $cent) {
                $othercenter = $DB->get_record('job_seeker_scheme_application', array('userid' => $USER->id, 'courseid' => $courseid, 'schemeid'=> $schemeid, 'centerid'=>$cent->id));
                $reothercenter = $DB->get_record('job_seeker_scheme_application', array('userid' => $USER->id, 'courseid' => $courseid, 'schemeid'=> $schemeid, 'centerid'=>$cent->id, 'isreapplied'=>1));
                
                $jsparray[] = [
                                'slno' => $sl, 
                                'centerid' => $cent->id, 
                                'district' => ucwords($cent->dtname), 
                                'consti' => ucwords($cent->ctname), 
                                'center' => $cent->center, 
                                'batchsize' => $cent->batchsize,
                                'cstartdate' => $cent->startdate,
                                'course_applied' => $course_applied,
                                'othercenter' => $othercenter_applied,
                                'othercenter' => (empty($othercenter)) ? ($courseapplied->centerid == 0 ? 'Online Applied' : 'Other Center Applied') : 'Already Applied',
                                'availslots' => $availableslots,
                                'batchfull' => $batchfull,
                                'scheme_status' => $scheme_status,
                                'reapp_stts' => $reapp_stts,
                                'reothercenter' => (empty($reothercenter)) ? ($courseapplied->centerid == 0 ? 'Online Reapplied' : 'Other Center Reapplied') : 'Reapplied',
                                'chckcour' => $chckcour,
                            ];
                $sl++;            
            }
        }
    }   
}
    $cpshortname = $partner_details->partner_name;
    $cpshortname = str_replace(" ", "_", $cpshortname);
    $cpshortname = str_replace("-", "_", $cpshortname);
    $cpshortname = strtolower($cpshortname);
    $cpfullIds = $DB->get_record('user', array('username' => $cpshortname));
    $cpfullname = ucwords($cpfullIds->firstname." ".$cpfullIds->lastname);

    (!empty($cssetting->startdate)) ? $cstartshow = date("d/m/Y", $cssetting->startdate) : $cstartshow = "";
    $cstartshow = (!empty($getcoursesdetails->cdispdate)) ? date("d/m/Y", $getcoursesdetails->cdispdate) : $cstartshow;
    (!empty($cssetting->enddate)) ? $cendshow = date("d/m/Y", $cssetting->enddate) : $cendshow = "";

    $templatecontext['product_name'] =  $getcoursename->fullname;
    //course summery from course edit page 
    $templatecontext['en_section1'] = $displaycontent->en_section1;
    $templatecontext['en_coursename'] = $displaycontent->coursename;
    $templatecontext['en_course_desc'] = $displaycontent->course_desc;
    $templatecontext['en_course_vurl'] = $displaycontent->course_vurl;
    $templatecontext['en_applynow'] = $displaycontent->applynow;
    $templatecontext['en_course_overview'] = $displaycontent->course_overview;
    $templatecontext['en_section2'] = $displaycontent->en_section2;
    $templatecontext['en_learning_outcome'] = $displaycontent->learning_outcome;
    $templatecontext['en_pricebox'] = $displaycontent->pricebox;
    $templatecontext['en_syllabus'] = $displaycontent->syllabus;
    $templatecontext['en_download_syllabus'] = $displaycontent->download_syllabus;
    $templatecontext['en_section3'] = $displaycontent->en_section3;
    $templatecontext['en_course_desc_s3'] = $displaycontent->course_desc_s3;
    $templatecontext['en_course_objective'] = $displaycontent->course_objective;
    $templatecontext['en_course_skill'] = $displaycontent->course_skill;
    $templatecontext['en_course_content'] = $displaycontent->course_content;
    $templatecontext['en_section4'] = $displaycontent->en_section4;
    $templatecontext['en_partner'] = $displaycontent->partner;
    $templatecontext['en_certificate1'] = $displaycontent->certificate1;
    $templatecontext['en_content_partner'] = $displaycontent->content_partner;
    $templatecontext['en_section5'] = $displaycontent->en_section5;
    $templatecontext['en_section6'] = $displaycontent->en_section6;
    $templatecontext['en_section7'] = $displaycontent->en_section7;
    $templatecontext['en_section8'] = $displaycontent->en_section8;
    $templatecontext['summary'] = substr($cssetting->summary, 0, 300);
    $templatecontext['summary11'] = substr($cssetting->summary, 300);
    $templatecontext['summary12'] = strip_tags(substr($cssetting->summary, 0, 300));
    $templatecontext['summary13'] = strip_tags(substr($cssetting->summary, 300));
    $templatecontext['summary1'] = substr($cssetting->summary, 0, 134);
    $templatecontext['summary2'] = substr($cssetting->summary, 134);
    //end of display content
    $templatecontext['satisfied_learners'] = '';
    $templatecontext['description'] = $u_learn;
    //to get data from course extrasettings
    $templatecontext['learning_outcome'] = $exsetting->audience;
    $templatecontext['enrolled_instructors'] = get_enrolled_instructors($coursesid);
    $templatecontext['price'] = $getcoursesdetails->price;
    $templatecontext['discount'] = $discountval->scholarship_percentage;
    $templatecontext['discounted_price'] = $totalprice;
    $templatecontext['enable_discount'] = $discountval->scholarship_percentage;
    $templatecontext['enable_emi'] = $discountval->enable_emi;
    $templatecontext['emi_company'] = '';
    $templatecontext['url'] = '';
    $templatecontext['rating'] = '';
    $templatecontext['onlinedata'] = $mode_online_array;
    $ratesql = array_values($DB->get_records('feedskillpartner', array('courseid' => $courseid)));
    $totrate = 0;
    $finalstatus = 0;
    $usrcount = 0;
if (!empty($ratesql) && $ratesql[0]->id) {
    foreach ($ratesql as $ratess) {
        $totrate += $ratess->que1 + $ratess->que2 + $ratess->que3 + $ratess->que4;
        $usrcount++;
    }
    $finalstatus = round($totrate / ($usrcount * 4));
}

    // $ownfinalstatus
switch ($finalstatus) {
    case 1:
        $finalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 2:
        $finalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 3:
        $finalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 4:
        $finalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 5:
        $finalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>';
        break;
    default:
        $finalstatus = '<span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
    }

    $templatecontext['stardata'] = $finalstatus;

    $ownrating = $DB->get_record_sql(
                    "SELECT fp.* 
                            FROM {feedskillpartner} fp
                            JOIN {partner_feedskill_completed} pfc ON pfc.id = fp.feedback_id
                                WHERE fp.courseid = $courseid 
                                AND pfc.userid = '".$USER->id."'"
                    );

    $totrate = 0;
    if (!empty($ownrating) && $ownrating->id) {
        $totrate += $ownrating->que1 + $ownrating->que2 + $ownrating->que3 + $ownrating->que4;
        $ratingown = round($totrate / 4);
    }
                
    if ($ratingown == 5 ) {
        $ownfinalstatus = 5;
    } else if ($ratingown <= 4 & $ratingown > 3) {
        $ownfinalstatus = 4;
    } else if ($ratingown <= 3 & $ratingown > 2) {
        $ownfinalstatus = 3;
    } else if ($ratingown <=2 & $ratingown > 1) {
        $ownfinalstatus = 2;
    } else if ($ratingown == 1) {
        $ownfinalstatus = 1;
    } else {
        $ownfinalstatus = 0;
    }
    switch ($ownfinalstatus) {
    case 1:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 2:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 3:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 4:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star"></span>';
        break;
    case 5:
        $ownfinalstatus = '<span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>
                    <span class="fa fa-star checked"></span>';
        break;
    default:
        $ownfinalstatus = '<span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>
                    <span class="fa fa-star"></span>';
    }
    $templatecontext['ownstardata'] = $ownfinalstatus;
    //course content from course extra settings page 

    //section 3
    //course objective from course extra setting page 
    $templatecontext['objective'] = substr($exsetting->whatsinside, 0, 134);
    $templatecontext['objective1'] = substr($exsetting->whatsinside, 134);
    $templatecontext['tableofcontent'] = substr($exsetting->tableofcontent, 0, 134);
    $templatecontext['tableofcontent1'] = substr($exsetting->tableofcontent, 134);
    $templatecontext['skillset'] = substr(rtrim(trim($skillset), ','), 0, 380);
    $templatecontext['skillset1'] = substr(rtrim(trim($skillset), ','), 381);
    //section 4
    $templatecontext['partner_details'] = $partner_details;
    $templatecontext['partner_url'] = $partner_details->purl;
    //$templatecontext['partner_name'] = $partner_details->partner_name;
    $templatecontext['partner_name'] = $cpfullname;
    $templatecontext['partner_description'] = $partner_details->description;

    //$templatecontext['partner_description_ribbon'] = substr($partner_details->description,0,40);
    $templatecontext['partner_description_ribbon'] = implode(' ', array_slice(explode(' ', $partner_details->description), 0, 12)) . ".";
    $templatecontext['product_certificate'] = get_product_certificatekdisc($coursesid);
    $templatecontext['student_reviews'] = '';
    $templatecontext['faq'] = $faqs;
    $templatecontext['faqs'] = $array;
    $templatecontext['course_id'] = $courseid;

    $templatecontext['coursesid'] = $coursesid;
    $templatecontext['course_creator_id'] = $jskUsrId; //$course_creator_id;
    $templatecontext['checkapply'] = $checkapply;
    $templatecontext['bcheckapply'] = $bcheckapply;
    $templatecontext['capply'] = $capply;
    $templatecontext['bcapply'] = $bcapply;

    $templatecontext['virtual_class_count'] = '';
    $templatecontext['quiz_count'] = '';
    $templatecontext['ppt_count'] = '';
    $templatecontext['course_includes'] = '';
    $templatecontext['course_list'] = '';
    $templatecontext['productid'] = $productid;
    $templatecontext['planid'] = $planid;
    $templatecontext['in_cartt'] = '';
    $templatecontext['cfg'] = $CFG->wwwroot;
    $templatecontext['isEnrolled'] = $isEnrolled;
    $templatecontext['normalCourse'] = $normalCourse;
    $templatecontext['syllbus_content'] = get_product_syllbus_contentkdisc($coursesid);
    $templatecontext['course_banner'] = get_course_bannerkdisc($coursesid);
    $templatecontext['videourl'] = $exsetting->videourl;
    $templatecontext['apply_access'] = $apply_access;
    $templatecontext['cour_criteria'] = $co_criteria;
    $templatecontext['c_criteria'] = $c_criteria;
    $templatecontext['mode_online'] = $mode_online;
    $templatecontext['mode_blended'] = $mode_blended;
    $templatecontext['course_centers'] = $jsparray;
    $templatecontext['wreadcheck'] = $wreadcheck;
    $templatecontext['cofull'] = $cofull;
    $templatecontext['bcofull'] = $bcofull;
    $templatecontext['offline_apply_action'] = $offline_apply_action;
    $templatecontext['enddate_show'] = $enddate_show;
    $templatecontext['cstartshow'] = (!$wreadcheck) ? $cstartshow = $cstartshow : $cstartshow = "";
    $templatecontext['cendshow'] = $cendshow;
    $templatecontext['courseapplied'] = $courseapplied;
    $templatecontext['batchsize_show'] = $batchsize_show;
    $templatecontext['offcofull'] = $offcofull;
    $templatecontext['special_status'] = $special_status;
    $templatecontext['bspecial_status'] = $bspecial_status;
    $templatecontext['reappliedstatus'] = $reappliedstatus;
    $templatecontext['breappliedstatus'] = $breappliedstatus;
    $templatecontext['cpfullname'] = $cpfullname;
    $templatecontext['wait_status'] = $wait_status;
    $templatecontext['bwait_status'] = $bwait_status;

    //free and enrolled courses 
    $templatecontext['isfree'] =  $isfree;
    $templatecontext['isfreeenrolled'] = $isfreeenrolled;
    $templatecontext['onlineapplied'] = $onlineapplied;
    $templatecontext['applied'] = $course_applied;
    $templatecontext['nocenter'] = $nocenter;
    $templatecontext['normapplied'] = $normapplied;    
    $templatecontext['scheme_status'] = $scheme_status;
    $templatecontext['reapp_stts'] = $reapp_stts;
    $templatecontext['chckcour'] = $chckcour;
    $templatecontext['pge'] = $pge;
    $templatecontext['pdata'] = $centpdata;
    $templatecontext['totpages'] = $totcentpages;
    $templatecontext['pageurl'] = $pagepass;
    $templatecontext['srch'] = $srch;
    $templatecontext['appliedcentarr'] = $appliedcentarr;
    $templatecontext['appliedcentshow'] = (!empty($appliedcentarr) && $appliedcentarr[0]['centerid']) ? 1 : 0;

    //check user is loggedin or not
    if (isloggedin()) {
        $templatecontext['isloggedin'] = 1;
    } else {
        $templatecontext['isloggedin'] = 0;
    }
    //emi code is added by prashant
    $product = $DB->get_record('local_buykart_variation', array('product_id' => $productid));
    $leadid = '';
    $emistoreleadid = $DB->get_record('local_emi_commonstatus', array('meta_data' => $USER->email));
    if (!empty($emistoreleadid->lead_id)) {
        $leadid = $emistoreleadid->lead_id;
    }


    if (isloggedin()) {
        $record = $DB->get_record('local_emiguestuser', array('email' => $USER->email));
        $templatecontext['userid'] = $USER->id; // $record->id;
        $templatecontext['loan_amount'] = $product->price;
        $templatecontext['email_id'] = $USER->email;
        $templatecontext['phone'] = $USER->phone1;
        $templatecontext['last_name'] = $USER->lastname;
        $templatecontext['firstname'] = $USER->firstname;
        $templatecontext['logintype'] = 'itrack';
    } else {
        $emiuser = $DB->get_record('local_emiguestuser', array('email' => $_SESSION['emiuseremail']));
        $templatecontext['logintype'] = 'guest';
    }
    //instructor details 
    $templatecontext['instructor'] = $instructor_array;
    $templatecontext['studentreview'] = $student_array;
    $templatecontext['courseid'] = $courseid;
    $templatecontext['schemeid'] = $schemeid;
    $templatecontext['fpgehilite'] = $fpgehilite;
    $templatecontext['lpgehilite'] = $lpgehilite;
    $templatecontext['pagemsg'] = $pagemsg;
// }
// print_object($templatecontext);
// die();
echo $OUTPUT->render_from_template('local_schememanagement/course-new', $templatecontext);

// $PAGE->requires->js_call_amd('local_schememanagement/schemeapply', 'init', ['courseid' => $courseid, 'userid' => $USER->id, 'schemeid' => $schemeid]);
if (isloggedin()) {
    // $PAGE->requires->js_call_amd('local_schememanagement/schemeapply', 'init', ['courseid' => $courseid, 'userid' => $USER->id]);
}
echo $OUTPUT->render_from_template('theme_remui/footer', $templatecontext);
// echo $OUTPUT->render_from_template('theme_remui/modal_backdrop', $templatecontext);
// Commented below line to eliminate multiple redirections to load this page
// echo $OUTPUT->footer();

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<style type="text/css">
    .site-menubar-unfold .page,
    .site-menubar-unfold .site-footer {
        margin-left: 0px !important;
    }
    #exampleModalLabel {
        margin-left: 129px;
    }

    #exampleModalLabel {
        margin-left: 0px !important;
    }

    .dropdown {
        position: relative
    }

    .dropdown-toggle:focus {
        outline: 0
    }
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        display: none;
        float: left;
        min-width: 160px;
        padding: 5px 0;
        margin: 2px 0 0;
        list-style: none;
        font-size: 14px;
        background-color: #fff;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, .15);
        border-radius: 4px;
        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        background-clip: padding-box
    }

    .dropdown-menu.pull-right {
        right: 0;
        left: auto
    }

    .dropdown-menu .divider {
        height: 1px;
        margin: 9px 0;
        overflow: hidden;
        background-color: #e5e5e5
    }

    .dropdown-menu>li>a {
        display: block;
        padding: 3px 20px;
        clear: both;
        font-weight: 400;
        line-height: 1.42857143;
        color: #333;
        white-space: nowrap
    }

    .dropdown-menu>li>a:hover,
    .dropdown-menu>li>a:focus {
        text-decoration: none;
        color: #262626;
        background-color: #f5f5f5
    }

    .dropdown-menu>.active>a,
    .dropdown-menu>.active>a:hover,
    .dropdown-menu>.active>a:focus {
        color: #fff;
        text-decoration: none;
        outline: 0;
        background-color: #428bca
    }

    .dropdown-menu>.disabled>a,
    .dropdown-menu>.disabled>a:hover,
    .dropdown-menu>.disabled>a:focus {
        color: #999
    }

    .dropdown-menu>.disabled>a:hover,
    .dropdown-menu>.disabled>a:focus {
        text-decoration: none;
        background-color: transparent;
        background-image: none;
        filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
        cursor: not-allowed
    }

    .open>.dropdown-menu {
        display: block
    }

    .open>a {
        outline: 0
    }

    .dropdown-menu-right {
        left: auto;
        right: 0
    }

    .dropdown-menu-left {
        left: 0;
        right: auto
    }

    .dropdown-header {
        display: block;
        padding: 3px 20px;
        font-size: 12px;
        line-height: 1.42857143;
        color: #999
    }

    .dropdown-backdrop {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        z-index: 990
    }

    .pull-right>.dropdown-menu {
        right: 0;
        left: auto
    }

    .dropup .caret,
    .navbar-fixed-bottom .dropdown .caret {
        border-top: 0;
        border-bottom: 4px solid;
        content: ""
    }

    .dropup .dropdown-menu,
    .navbar-fixed-bottom .dropdown .dropdown-menu {
        top: auto;
        bottom: 100%;
        margin-bottom: 1px
    }

    .nav-tabs .nav-link.active,
    .nav-tabs .nav-link.active:focus,
    .nav-tabs .nav-link.active:hover {
        background: transparent !important;
    }

    .cfull {
        letter-spacing:1.75px;
        text-transform:uppercase;
        color:#FFF;
        font-weight:600;
        font-size:18px;
        padding : 14px 60.5px;
        border-radius : 3px;
        background-color:#f5a623;
        cursor:pointer;
        display: block;
    }    
</style>

<script type="text/javascript">
    function emicreatelead() {
        //this functionality coded is need to change 
        /*
        @product id is leadid 
        @user email address   is metadata
        @companyid is firstname
        */
        //for first time we need to leadid null for creating new 
        var leadid = $('#userid').val();
        var productid = $('#productid').val();
        var loan_amount = $('#loan_amount').val();
        var email_id = $('#email_id').val();
        var comapnyid = $('#company_check').val();
        var phn = $('#phone').val();
        var last_name1 = $('#last_name').val();
        var type = $('#type').val();
        //var phn = '';
        if ($('#company_check').prop('checked')) {
            if (phn == '') {
                var e = 'Please Update your phone number, then you can apply for EMI';
                Swal.fire({
                    icon: "error",
                    title: e
                })
                return false;
            }
            var meta_data = email_id;
            //user id in lead_id
            //for creating emi here 
            //var lead_id= "";
            //var userName= 'TransneuronUAT';
            //var password= 'fa41edf16159128ef33a63392d2fb5ea';

            var userName = 'TransneuronProd';
            var password = 'd5f10d45a11ced0bc20cbb8daaeec579';
            var redirect_url = 'https://transneuron.com/';
            //var redirect_url= 'https://www.itrackglobal.com/';
            var client_institute_id = '1';
            var client_course_id = productid;
            //var client_course_id= '511';
            var client_location_id = '1';
            var loan_amount = loan_amount;
            var first_name = comapnyid;
            var last_name = last_name1;
            var gender_id = '1';
            var dob = '19-09-1988';
            var marital_status = '1';
            //var mobile_number = '8073543449';
            var mobile_number = phn;
            var email_id = email_id;
            //var actionurl='https://staging.eduvanz.com/quickemi/login';
            var actionurl = 'https://eduvanz.com/quickemi/login';

            //alert(loan_amount);
            // var actionurl='https://staging.eduvanz.com/login';
            //applicant key is added
            var fr = '<form action=\"' + actionurl + '\" method=\"post\">' +
                '<input type=\"hidden\" name=\"meta_data\" id=\"meta_data\"  value=\"' + meta_data + '\" />' +
                '<input type=\"hidden\" name=\"lead_id\" id=\"lead_id\"   value=\"' + leadid + '\" />' +
                '<input type=\"hidden\" name=\"userName\" id=\"userName\"   value=\"' + userName + '\" />' +
                '<input type=\"hidden\" name=\"password\" id=\"password\"   value=\"' + password + '\" />' +
                '<input type=\"hidden\" name=\"redirect_url\" id=\"redirect_url\"   value=\"' + redirect_url + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[client_institute_id]\" id=\"requestParam[client_institute_id]\" value=\"' + client_location_id + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[client_course_id]\" id=\"requestParam[client_course_id]\"   value=\"' + client_course_id + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[client_location_id]\" id=\"requestParam[client_location_id]\"   value=\"' + client_location_id + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[loan_amount]\" id=\"requestParam[loan_amount]\"   value=\"' + loan_amount + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[applicant][first_name]\" id=\"requestParam[applicant][first_name]\"   value=\"' + first_name + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[applicant][last_name]\" id=\"requestParam[applicant][last_name]\"   value=\"' + last_name + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[applicant][gender_id]\" id=\"requestParam[applicant][gender_id]\"   value=\"' + gender_id + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[applicant][dob]\" id=\"requestParam[applicant][dob]\"   value=\"' + dob + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[applicant][marital_status]\" id=\"requestParam[applicant][marital_status]\"   value=\"' + marital_status + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[applicant][email_id]\" id=\"requestParam[applicant][email_id]\"   value=\"' + email_id + '\" />' +
                '<input type=\"hidden\" name=\"requestParam[applicant][mobile_number]\" id=\"requestParam[applicant][mobile_number]\"   value=\"' + mobile_number + '\" />' +
                '</form>';
            var form = jQuery(fr);
            jQuery('body').append(form);
            form.submit();
            //update record in table
            $.ajax({
                url: M.cfg.wwwroot + "/local/emiapi/updateuser.php",
                type: "GET",
                data: {
                    productid: productid,
                    emicompayid: comapnyid,
                    email: email_id,
                    usertype: type
                },
                success: function(response) {
                    if (response.success == 1) {
                        setTimeout(function() {}, 1500);
                    }
                }
            }); //send email for info mail id 
        } else {
            var e = 'Please select Company';
            Swal.fire({
                icon: "error",
                title: e
            })
        }
    }

    function lightbox_open() {
        var lightBoxVideo = document.getElementById("VisaChipCardVideo");
        document.getElementById('light').style.display = 'block';
        document.getElementById('fade').style.display = 'block';
    }

    function lightbox_close() {
        var ysrc = document.getElementById("VisaChipCardVideo").src;
        var newsrc = ysrc.replace("?autoplay=1", "?autoplay=0");
        document.getElementById("VisaChipCardVideo").src = '';
        document.getElementById('light').style.display = 'none';
        document.getElementById('fade').style.display = 'none';
        document.getElementById("VisaChipCardVideo").src = newsrc;
    }

    $('#download').click(function() {
        var firstname = $('#firstnamedwn').val();
        var username = $('#usernamedwn').val();
        var phone = $('#phonedwn').val();
        var courseid = $('#courseiddwn').val();
        var pdffileurl = $('#pdffileurl').val();
        var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
        var valid = emailRegex.test(username);

        if (firstname == "") {
            var e = 'Enter User First Name';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        if (username == "") {
            var e = 'Enter e-mail address';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        if (phone == "") {
            var e = 'Enter user phone number';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        if (!valid) {
            var e = 'Invalid e-mail address';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        //alert(pdffileurl);
        //return false;
        var url = $('#siteurldwn').val() + '/local/marketplace/downloadsyllabusentry.php';
        $.ajax({
            url: url,
            dataType: 'json',
            type: 'get',
            data: {
                firstname: firstname,
                username: username,
                phone: phone,
                courseid: courseid
            },
            success: function(response) {
                if (response.success == 1) {
                    Swal.fire({
                        icon: "success",
                        position: 'center',
                        title: 'Thanks for your Interest!, We will be in touch Soon',
                        timer: 5000
                    })
                    setTimeout(function() {
                        window.open(pdffileurl, '_blank', 'fullscreen=yes');
                    }, 1500);
                }
                if (response.success == 2) {
                    Swal.fire({
                        icon: "error",
                        position: 'center',
                        title: 'Email is already exist for this course.',
                        timer: 2500
                    })
                    setTimeout(function() {}, 1500);

                }
            }
        });

    });

    //emiuser register 

    $('#emiuser').click(function() {
        var firstname = $('#firstname').val();
        var lastname = $('#lastname').val();
        var email = $('#email').val();
        var phone = $('#phoneemi').val();
        var productid = $('#productid').val();
        var loanamount = $('#loanamount').val();
        var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
        var valid = emailRegex.test(email);
        //alert(loanamount);return false;
        if (firstname == "") {
            var e = 'Enter User First Name';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        if (lastname == "") {
            var e = 'Enter User Last Name';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        if (email == "") {
            var e = 'Enter e-mail address';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        if (!valid) {
            var e = 'Invalid e-mail address';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        if (phone == "") {
            var e = 'Enter user phone number';
            Swal.fire({
                icon: "error",
                title: e
            })
            return false;
        }
        var url = $('#siteurldwn').val() + '/local/emiapi/guestemiuser.php';
        $.ajax({
            url: url,
            dataType: 'json',
            type: 'get',
            data: {
                firstname: firstname,
                lastname: lastname,
                email: email,
                phone: phone,
                productid: productid,
                loanamount: loanamount
            },
            success: function(response) {
                //setTimeout(function(){}, 100);
                $('#userid').val(response.userid);
                $('#firstname').val(response.firstname);
                $('#last_name').val(response.lastname);
                $('#email_id').val(response.emiuseremail);
                $('#phone').val(response.emiuserphone);
                $('#loan_amount').val(response.loanamount);
                $('#productid').val(response.emiuserproductid);
                if (response.success == 1) {
                    Swal.fire({
                        icon: "success",
                        position: 'center',
                        title: 'Thanks for your Interest!',
                        timer: 5000
                    })
                    //setTimeout(function(){}, 2000);
                    //$('#emiguestuser').removeClass("show");
                    $('#emiguestuser').modal("hide");
                    //$('#emiguestuser').addClass("hide");

                    var pid = $('#productid').val();
                    $.ajax({
                       // url: M.cfg.wwwroot + "/local/marketplace/newdesign/emi_company.php",
                        type: "GET",
                        data: {
                            productid: pid,
                        },
                        success: function(response) {

                            var responses = $.parseJSON(response);
                            var companyname = responses.companyname;
                            var companyid = responses.companyid;
                            var tempid = [];
                            var tempcompany = [];
                            for (i = 0; i < companyname.length; i++) {
                                var tempcompany = companyname[i];
                                var tempid = companyid[i];
                                $(".emi_partner").append("<div class='company_list'><input type=\"checkbox\" id = \"company_check\" value = " + tempid + ">&nbsp;&nbsp;" + tempcompany + "</div>");
                            }
                            $('#emiModal').modal('show');
                        }
                    });
                }
                if (response.success == 2) {
                    Swal.fire({
                        icon: "error",
                        position: 'center',
                        title: 'Email is already exist for this course.',
                        timer: 2500
                    })
                    setTimeout(function() {}, 1500);

                }
            }
        });

    });

    //save emiuser
    function saveemiuser() {
        var firstname = $('#first_name').val();
        var lastname = $('#last_name').val();
        var email = $('#email_id').val();
        var phone = $('#phone').val();
        var productid = $('#productid').val();
        var loanamount = $('#loan_amount').val();
        var url = $('#siteurldwn').val() + '/local/emiapi/guestemiuser.php';
        var url = M.cfg.wwwroot + "/local/emiapi/guestemiuser.php";
        $.ajax({
            url: url,
            dataType: 'json',
            type: 'get',
            data: {
                firstname: firstname,
                lastname: lastname,
                email: email,
                phone: phone,
                productid: productid,
                loanamount: loanamount
            },
            success: function(response) {
                if (response.success == 1) {
                    setTimeout(function() {}, 1500);
                }
                if (response.success == 2) {
                    $("#emiModal").modal('show');
                }
            }
        });
    }
    $('#phonedwn').keypress(function(e) {
        var arr = [];
        var kk = e.which;
        for (i = 48; i < 58; i++)
            arr.push(i);
        if (!(arr.indexOf(kk) >= 0))
            e.preventDefault();
    });
    $('#phoneemi').keypress(function(e) {
        var arr = [];
        var kk = e.which;
        for (i = 48; i < 58; i++)
            arr.push(i);
        if (!(arr.indexOf(kk) >= 0))
            e.preventDefault();
    });

    // Below Scripts are shifted from .js to here to avoif multiple redirections on single page load

    var center_id;
    var courseid = $("#courseid").val();
    var userid = $("#userid").val(); 
    var schemeid = $("#schemeid").val();

    $(".apply_now").on('click', function(e) {
        // console.log($(e.currentTarget).attr('data-centerid'));
        center_id = $("#centerid").val($(e.currentTarget).attr('data-centerid'));
        $("#centerid").val($(this).data("centerid"));
    });

    $(".apply_online").on('click', function(e) {
        // console.log($(e.currentTarget).attr('data-centerid'));
        center_id = $("#centerid").val($(e.currentTarget).attr('data-centerid'));
    });

    $(".apply_course").on('click', function(e){
        var batchsel = $('#batchs5').val();
        if (batchsel == null || batchsel == "") {

            e.preventDefault();
            $('.bcode_online_error').show();
            return false;

        } else {
        
            $('.bcode_online_error').hide();
            // var promises = ajax.call([
            //     {
            //         methodname: 'local_schememanagement_schemeapply',
            //         args: {
            //         courseid : courseid,
            //             userid: userid,
            //             schemeid: schemeid,
            //             centerid: center_id.val(),     
            //         }
            //     }
            //     ]);
            //     promises[0].done(function(result) {
            //     $("#apply_offline_course_modal").hide();
            //     // $(e.currentTarget).prop("disabled", true);
                
            //         Swal.fire({
            //         position: 'center',
            //         icon: 'success',
            //         title: 'Applied Successfully',
            //         showConfirmButton: false,
            //         timer: 1500
            //         })
            //         setTimeout(function(){ location.reload(); }, 1500);
            //     }).fail(function(result) {
            //         Swal.fire({
            //             position: 'center',
            //             icon: 'error',
            //             title: 'Something went wrong',
            //             showConfirmButton: false,
            //             timer: 1500
            //         })                    
            //     });

            // New Logic

            var url = 'save_scheme_course_applicant.php';
            $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'GET',
                    data: {
                        courseid : courseid,
                        userid: userid,
                        schemeid: schemeid,
                        centerid: center_id.val()
                    },
                    success: function(response) {
                        $("#apply_offline_course_modal").hide();
                        if (response.success == 1) {
                            Swal.fire({
                                icon: "success",
                                position: 'center',
                                title: 'Applied Successfully',
                                //timer: 5000
                            })
                            setTimeout(function() {
                                location.reload(true);
                            }, 1500);
                        }
                        if (response.success == 2) {
                            Swal.fire({
                                icon: "error",
                                position: 'center',
                                title: 'Application failed',
                                //timer: 2500
                            })
                            setTimeout(function() {}, 20000);
                        } 
                        if (response.success == 3) {
                            Swal.fire({
                                icon: "success",
                                position: 'center',
                                title: 'Re-applied Successfully',
                                //timer: 2500
                            })
                            setTimeout(function(){ location.reload(); }, 1500);
                        }  
                        if (response.success == 4) {
                            Swal.fire({
                                icon: "error",
                                position: 'center',
                                title: 'You have already applied for this Course',
                                //timer: 2500
                            })
                            setTimeout(function(){ location.reload(); }, 1500);
                        }  
                        if (response.success == 5) {
                            Swal.fire({
                                icon: "error",
                                position: 'center',
                                title: 'You have already applied for this Course (Partner LMS)',
                                //timer: 2500
                            })
                            setTimeout(function(){ location.reload(); }, 1500);
                        }                        
                    }
            });            
        }
    });

    // $(document).on("click", ".navbar-avatar", function(e) {
    //     // $(".dropdown-menu").addClass('show');
    //     $(".cart-popup-bg").hide();
    //     $(".dropdown-menu").toggleClass("show");
    //     // $(".dropdown-menu").slideToggle("show");
    //     // $(".navbar-right").css("box-shadow", "#FFFFFF");
    // });

    $(document).on('click','.training-tab', function(e) {
        $("#trainingupskill").addClass('active');
        $("#mentornship").removeClass('active');
    });

    $(document).on('click','.mentornship', function(e) {
        $("#trainingupskill").removeClass('active');
        $("#mentornship").addClass('active');
    });

    $(document).on('click','.moreless-button', function(e) {    
      $('.moretext').slideToggle();
      if ($('.moreless-button').text() == "Read more") {
         $(this).text("Read less");
         $('.dot').hide();
      } else {
         $(this).text("Read more");
         $('.dot').show();
      }
   });

   $(document).on('click','.moreless-button1', function(e) { 
      $('.moretext1').slideToggle();
      if ($('.moreless-button1').text() == "Read more") {
         $(this).text("Read less");
         $('.dot1').hide();
      } else {
         $(this).text("Read more");
         $('.dot1').show();
      }
   });

   $(document).on('click','.moreless-button2', function(e) { 
      $('.moretext2').slideToggle();
      if ($('.moreless-button2').text() == "Read more") {
         $(this).text("Read less");
         $('.dot2').hide();
      } else {
         $(this).text("Read more");
         $('.dot2').show();

      }
   });

   $(document).on('click','.moreless-button3', function(e) { 
      $('.moretext3').slideToggle();
      if ($('.moreless-button3').text() == "Read more") {
         $(this).text("Read less");
         $('.dot3').hide();
         $('#dot3').hide();
      } else {
         $(this).text("Read more");
         $('.dot3').show();
      }
   });
   
   $(document).on('click','.pprev', function(e) {
        var pageurl = $('#pageurl').val();
        var cenname = $('#cenname1').val();
        var cpge = $("#pge").val();
        var totpages = $("#totpages").val();
        var nwpge = $(this).data("id");
        var schemeid = $("#schemeid").val();
        var newurl = pageurl;
        (cenname) ? newurl += "&srch="+cenname : newurl = newurl;
        (schemeid) ? newurl += "&schemeid="+schemeid : newurl = newurl;
        (cpge && (parseInt(cpge) > 1)) ? newurl += "&pge="+(cpge-1) : newurl += "&pge="+cpge;
        // newurl += '#avail_course_centers';
        window.history.pushState({path:newurl}, '', newurl);
                setTimeout(function() {
                    location.reload(true);
                    }, 500);
    });

    $(document).on('click','.pind', function(e) {
        var pageurl = $('#pageurl').val();
        var cenname = $('#cenname1').val();
        var cpge = $("#pge").val();
        var totpages = $("#totpages").val();
        var nwpge = $(this).data("id");
        var schemeid = $("#schemeid").val();
        var newurl = pageurl;
        (cenname) ? newurl += "&srch="+cenname : newurl = newurl;
        (schemeid) ? newurl += "&schemeid="+schemeid : newurl = newurl;
        (nwpge) ? newurl += "&pge="+nwpge : newurl += "&pge="+cpge;
        // newurl += '#avail_course_centers';
        window.history.pushState({path:newurl}, '', newurl);
        setTimeout(function() {
                    location.reload(true);
                    }, 500);
    });

    $(document).on('click','.pnext', function(e) {
        var pageurl = $('#pageurl').val();
        var cenname = $('#cenname1').val();
        var cpge = $("#pge").val();        
        var totpages = $("#totpages").val();
        var nwpge = $(this).data("id");
        var schemeid = $("#schemeid").val();
        var newurl = pageurl;
        (cenname) ? newurl += "&srch="+cenname : newurl = newurl;
        (schemeid) ? newurl += "&schemeid="+schemeid : newurl = newurl;
        if (cpge && (parseInt(cpge) < parseInt(totpages))) {
            newurl += "&pge="+(parseInt(cpge) + parseInt(1));
        } else {
            newurl += "&pge="+cpge;
        }
        // newurl += '#avail_course_centers';
        window.history.pushState({path:newurl}, '', newurl);
        setTimeout(function() {
                    location.reload(true);
                    }, 500);
    });

    $(document).on('click', '.page-first', function(e) {
        var pageurl = $('#pageurl').val();
        var cenname = $('#cenname1').val();
        var cpge = $("#pge").val();        
        var totpages = $("#totpages").val();
        var nwpge = $(this).data("id");
        var schemeid = $("#schemeid").val();
        var newurl = pageurl;
        (cenname) ? newurl += "&srch="+cenname : newurl = newurl;
        (schemeid) ? newurl += "&schemeid="+schemeid : newurl = newurl;
        newurl += "&pge=1";
        // if (cpge && (parseInt(cpge) < parseInt(totpages))) {
        //     newurl += "&pge="+(parseInt(cpge) + parseInt(1));
        // } else {
        //     newurl += "&pge="+cpge;
        // }
        // newurl += '#avail_course_centers';
        window.history.pushState({path:newurl}, '', newurl);
        setTimeout(function() {
                    location.reload(true);
                    }, 500);
    });

    $(document).on('click', '.page-last', function(e) {
        var pageurl = $('#pageurl').val();
        var cenname = $('#cenname1').val();
        var cpge = $("#pge").val();        
        var totpages = $("#totpages").val();
        var nwpge = $(this).data("id");
        var schemeid = $("#schemeid").val();
        var newurl = pageurl;
        (cenname) ? newurl += "&srch="+cenname : newurl = newurl;
        (schemeid) ? newurl += "&schemeid="+schemeid : newurl = newurl;
        newurl += "&pge="+totpages;
        // if (cpge && (parseInt(cpge) < parseInt(totpages))) {
        //     newurl += "&pge="+(parseInt(cpge) + parseInt(1));
        // } else {
        //     newurl += "&pge="+cpge;
        // }
        // newurl += '#avail_course_centers';
        window.history.pushState({path:newurl}, '', newurl);
        setTimeout(function() {
                    location.reload(true);
                    }, 500);
    });

    $(document).on('click','#censearch', function(e) {
        var pageurl = $('#pageurl').val();
        var cenname = $('#cenname1').val();
        var cpge = $("#pge").val();        
        var totpages = $("#totpages").val();
        var nwpge = $(this).data("id");
        var schemeid = $("#schemeid").val();
        var newurl = pageurl;
        (cenname) ? newurl += "&srch="+cenname : newurl = newurl;
        (schemeid) ? newurl += "&schemeid="+schemeid : newurl = newurl;
        // newurl += (cenname) ? "&pge=1" : "&pge="+cpge;
        newurl += "&pge=1";
        // newurl += '#avail_course_centers';
        window.history.pushState({path:newurl}, '', newurl);
        setTimeout(function() {
            location.reload(true);
            }, 500);
    });

   $('html, body').animate({
    scrollTop: $('#avail_course_centers').offset().top
   }, 'slow');  

</script>

