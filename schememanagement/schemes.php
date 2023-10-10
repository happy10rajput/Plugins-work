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
require_once $CFG->dirroot . '/local/schememanagement/locallib.php';
require_login();
global $DB, $USER, $CFG;
$schemeid = optional_param('scheme', null, PARAM_INT);
$scheme_id = optional_param('schemeid', null, PARAM_RAW);
$kkemapproval = optional_param('kkemapproval', '', PARAM_BOOL);
$usrole = optional_param('usrole', '', PARAM_TEXT);
$usrrole = optional_param('usrrole', '', PARAM_TEXT);
$pageurl = new moodle_url('/local/schememanagement/schemes.php');

if (!empty($schemeid)) {
    $pageurl->param('scheme', $schemeid);
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('custom');
$PAGE->set_url($pageurl);
$scheme_head = get_string('schemepage', 'local_schememanagement');

$PAGE->navbar->add($scheme_head);
$PAGE->set_title($scheme_head);
$PAGE->set_heading($scheme_head);
$response_schemedata = apiCall::get_allschemes();
// print_object($response_schemedata);die();
echo $OUTPUT->header();

if (is_siteadmin()) {
} else if (has_capability('local/schememanagement:collegesuperadmin', $context)) {
} else if (has_capability('local/schememanagement:college', $context)) {
}


if (is_siteadmin()) {
} else if (has_capability('local/schememanagement:college', $context, $USER->id) || has_capability('local/schememanagement:collegesuperadmin', $context, $USER->id)) {
    if ($role == 'industryadmin') {
    } else {
    }
}

if ($response_schemedata) {
    $tabledata = true;
} else {
    $tabledata = false;
}
$viewMappingLink = "";
$role = $DB->get_record('role_assignments', array('userid' => $USER->id), 'roleid');
foreach ($response_schemedata as $key => $scheme) {
    $datum = new stdClass();
    $datum->name = $scheme['name'];
    $datum->scheme_owner = $scheme['scheme_owner_name'];
    $datum->startdate = date('d/m/Y', strtotime($scheme['scheme_start_date']));
    $datum->enddate = date('d/m/Y', strtotime($scheme['scheme_end_date']));
    $datum->minbatchsize = !empty($scheme['min_batch_size']) ? $scheme['min_batch_size'] : 'NA';
    $datum->batchsize = $scheme['no_of_trainees_trained'];
    $datum->totalfunds = $scheme['total_funds'] ? $scheme['total_funds'] : 0;
    $datum->scholarship = $scheme['scholarship_percentage'] ? $scheme['scholarship_percentage'] : 0;
    $schemedata = new stdClass();
    $recordexist = $DB->get_record('schemedata', array('schemeid' => $scheme['_id']), 'id');
    if (!$recordexist) {
        $schemedata->schemeid = $scheme['_id'];
        $schemedata->name = $scheme['name'];
        $schemedata->scholarship_percentage = $scheme['scholarship_percentage'] ? $scheme['scholarship_percentage'] : 0;
        $schemedata->batchsize = $scheme['no_of_trainees_trained'] ? $scheme['no_of_trainees_trained'] : 0;
        $schemedata->startdate = $scheme['scheme_start_date'];
        $schemedata->enddate = $scheme['scheme_end_date'];
        $schemedata->min_batch_size = $scheme['min_batch_size'];
        $schemedata->total_funds = $scheme['total_funds'] ? $scheme['total_funds'] : 0;
        $schemedata->gender = implode(",", $scheme['gender']);
        $schemedata->category = implode(",", $scheme['category']);
        $schemedata->sub_scheme = $scheme['sub_scheme'];
        $schemedata->scheme_owner_name = $scheme['scheme_owner_name'];
        $schemedata->description = $scheme['description'];
        try {
            $DB->insert_record('schemedata', $schemedata);
        } catch (Exception $e) {
            print_object($e);
        }
        
    } else {
        $schemedata->id = $recordexist->id;
        $schemedata->name = $scheme['name'];
        $schemedata->scholarship_percentage = $scheme['scholarship_percentage'] ? $scheme['scholarship_percentage'] : 0;
        $schemedata->batchsize = $scheme['no_of_trainees_trained'] ? $scheme['no_of_trainees_trained'] : 0;
        $schemedata->startdate = $scheme['scheme_start_date'];
        $schemedata->enddate = $scheme['scheme_end_date'];
        $schemedata->min_batch_size = $scheme['min_batch_size'];
        $schemedata->total_funds = $scheme['total_funds'] ? $scheme['total_funds'] : 0;
        $gender_array = implode(",", $scheme['gender']);
        $category_array = implode(",", $scheme['category']);
        $schemedata->sub_scheme = $scheme['sub_scheme'];
        $schemedata->scheme_owner_name = $scheme['scheme_owner_name'];
        $schemedata->description = $scheme['description'];
        try {
            $DB->update_record('schemedata', $schemedata);
        } catch (Exception $e) {
            print_object($e);
        }
    }
 
    if (($usrole == "sch_own" && $kkemapproval == 1 &&  $scheme['_id'] == $scheme_id) || ($usrole == "sch_adm" && $scheme['_id'] == $scheme_id) || ($usrrole == "schadmn")) {    
            $viewMappingLink = 'courses_and_partners.php?schemeid=' . $scheme['_id'].'&schadmn='."1";
            $actionbtn = '<a href="' . $viewMappingLink . '">
            <button class="btn btn-success" data-toggle="modal" data-target="" style="margin-left: 5px;" value="" target="">
            View Mapping   
            </button>
            </a>
            <button class="btn btn-success inforationschema" id="inforationschema" data-toggle="modal" data-target="#schememodal"
            data-subscheme="'.$scheme["sub_scheme"].'" data-title="'.$scheme["name"].'" data-batchsize="'.$scheme["no_of_trainees_trained"].'"
            data-desc="'.$scheme["description"].'"  data-schemeowner="'.$scheme["scheme_owner_name"].'" data-funds="'.$scheme["total_funds"].'"
            data-gender="'.htmlspecialchars(json_encode($scheme["gender"])).'" data-category="'.htmlspecialchars(json_encode($scheme["category"])).'"
            style="margin-left: 5px;">
            View Scheme   
            </button>'; 
            $datum->actiontn = $actionbtn; 
            $scheme_arr[] = $datum; 
    } else if (($usrole == "sch_own" && $scheme_id && $kkemapproval == 0 && $scheme['_id'] == $scheme_id)) {
            $viewMappingLink = 'courses_and_partners.php?schemeid=' . $scheme['_id'].'&schadmn='."0";
            $actionbtn = '<a href="' . $viewMappingLink . '">
            <button class="btn btn-success" data-toggle="modal" data-target="" style="margin-left: 5px;" value="" target="">
            View Mapping   
            </button>
            </a>
            <button class="btn btn-success inforationschema" id="inforationschema" data-toggle="modal" data-target="#schememodal"
            data-subscheme="'.$scheme["sub_scheme"].'" data-title="'.$scheme["name"].'" data-batchsize="'.$scheme["no_of_trainees_trained"].'"
            data-desc="'.$scheme["description"].'"  data-schemeowner="'.$scheme["scheme_owner_name"].'" data-funds="'.$scheme["total_funds"].'"
            data-gender="'.htmlspecialchars(json_encode($scheme["gender"])).'" data-category="'.htmlspecialchars(json_encode($scheme["category"])).'"
            style="margin-left: 5px;">
            View Scheme   
            </button>';
            $datum->actiontn = $actionbtn; 
            $scheme_arr[] = $datum; 
    } else if ($USER->username == "isadmin_kkem") {
        if ($scheme['kkem_approval_scheme_required'] == "Yes") {
            $viewMappingLink = 'courses_and_partners.php?schemeid='.$scheme['_id'];
            $actionbtn = '<a href="' . $viewMappingLink . '">
            <button class="btn btn-success" data-toggle="modal" data-target="" style="margin-left: 5px;" value="" target="">
            View Mapping   
            </button>
            </a>
            <button class="btn btn-success inforationschema" id="inforationschema" data-toggle="modal" data-target="#schememodal"
            data-subscheme="'.$scheme["sub_scheme"].'" data-title="'.$scheme["name"].'" data-batchsize="'.$scheme["no_of_trainees_trained"].'"
            data-desc="'.$scheme["description"].'"  data-schemeowner="'.$scheme["scheme_owner_name"].'" data-funds="'.$scheme["total_funds"].'"
            data-gender="'.htmlspecialchars(json_encode($scheme["gender"])).'" data-category="'.htmlspecialchars(json_encode($scheme["category"])).'"
            style="margin-left: 5px;">
            View Scheme   
            </button>';
        } 
        if ($scheme['kkem_approval_scheme_required'] == "No") {
            $viewMappingLink = 'courses_and_partners.php?schemeid='.$scheme['_id'].'&schadmn='."1";
            $actionbtn = '<a href="' . $viewMappingLink . '">
            <button class="btn btn-success" data-toggle="modal" data-target="" style="margin-left: 5px;" value="" target="">
            View Mapping   
            </button>
            </a>
            <button class="btn btn-success inforationschema" id="inforationschema" data-toggle="modal" data-target="#schememodal"
            data-subscheme="'.$scheme["sub_scheme"].'" data-title="'.$scheme["name"].'" data-batchsize="'.$scheme["no_of_trainees_trained"].'"
            data-desc="'.$scheme["description"].'"  data-schemeowner="'.$scheme["scheme_owner_name"].'" data-funds="'.$scheme["total_funds"].'"
            data-gender="'.htmlspecialchars(json_encode($scheme["gender"])).'" data-category="'.htmlspecialchars(json_encode($scheme["category"])).'"
            style="margin-left: 5px;">
            View Scheme   
            </button>';
        }
        $datum->actiontn = $actionbtn; 
        $scheme_arr[] = $datum; 
    } else if ($usrrole == "schown" && $scheme['scheme_owner_email'] == $USER->email) {
        if ($scheme['kkem_approval_scheme_required'] == "No") {
            $viewMappingLink = 'courses_and_partners.php?schemeid=' . $scheme['_id'].'&schadmn='."0";
            $actionbtn = '<a href="' . $viewMappingLink . '">
            <button class="btn btn-success" data-toggle="modal" data-target="" style="margin-left: 5px;" value="" target="">
            View Mapping   
            </button>
            </a>
            <button class="btn btn-success inforationschema" id="inforationschema" data-toggle="modal" data-target="#schememodal"
            data-subscheme="'.$scheme["sub_scheme"].'" data-title="'.$scheme["name"].'" data-batchsize="'.$scheme["no_of_trainees_trained"].'"
            data-desc="'.$scheme["description"].'"  data-schemeowner="'.$scheme["scheme_owner_name"].'" data-funds="'.$scheme["total_funds"].'"
            data-gender="'.htmlspecialchars(json_encode($scheme["gender"])).'" data-category="'.htmlspecialchars(json_encode($scheme["category"])).'"
            style="margin-left: 5px;">
            View Scheme   
            </button>';
        } 
        if ($scheme['kkem_approval_scheme_required'] == "Yes") {
            $viewMappingLink = 'courses_and_partners.php?schemeid='.$scheme['_id'].'&schadmn='."1";
            $actionbtn = '<a href="' . $viewMappingLink . '">
            <button class="btn btn-success" data-toggle="modal" data-target="" style="margin-left: 5px;" value="" target="">
            View Mapping   
            </button>
            </a>
            <button class="btn btn-success inforationschema" id="inforationschema" data-toggle="modal" data-target="#schememodal"
            data-subscheme="'.$scheme["sub_scheme"].'" data-title="'.$scheme["name"].'" data-batchsize="'.$scheme["no_of_trainees_trained"].'"
            data-desc="'.$scheme["description"].'"  data-schemeowner="'.$scheme["scheme_owner_name"].'" data-funds="'.$scheme["total_funds"].'"
            data-gender="'.htmlspecialchars(json_encode($scheme["gender"])).'" data-category="'.htmlspecialchars(json_encode($scheme["category"])).'"
            style="margin-left: 5px;">
            View Scheme   
            </button>';
        }
        
        
        $datum->actiontn = $actionbtn; 
        $scheme_arr[] = $datum; 

    } else if ($role->roleid == 12) {
       
        $viewMappingLink = 'view_mapping.php?schemeid=' . $scheme['_id'];
        $sql = "SELECT sm.id,sm.cpid,lc.partner_name FROM {schememangement_mapping} sm JOIN
        {local_course_partners} lc ON lc.id=sm.cpid WHERE 
        (lc.partner_name='$USER->username' 
        OR  lc.partner_name = '" . $USER->firstname . " " . $USER->lastname . "')
        AND sm.schemeid='$scheme[_id]'";
        $mappingexist = $DB->record_exists_sql($sql);
        // print_object($sql);
        // die();
        // if (!$mappingexist) { 
        //     continue;
        // }
        if ($mappingexist) {
            $query = "SELECT SUM(sm.batchsize) as total FROM {schememangement_mapping} sm JOIN {local_course_partners} lc 
            ON lc.id=sm.cpid WHERE 
            (lc.partner_name='$USER->username' 
            OR  lc.partner_name = '" . $USER->firstname . " " . $USER->lastname . "')
            AND schemeid='$scheme[_id]'";
            $totalbatchsize = array_values($DB->get_records_sql($query));
            $datum->batchsize = $totalbatchsize[0]->total;
            $actionbtn = '<a href="' . $viewMappingLink . '">
            <button class="btn btn-success" data-toggle="modal" data-target="" style="margin-left: 5px;" value="" target="">
            View Mapping   
            </button>
            </a>
            <button class="btn btn-success inforationschema" id="inforationschema" data-toggle="modal" data-target="#schememodal"
            data-subscheme="'.$scheme["sub_scheme"].'" data-title="'.$scheme["name"].'" data-batchsize="'.$scheme["no_of_trainees_trained"].'"
            data-desc="'.$scheme["description"].'"  data-schemeowner="'.$scheme["scheme_owner_name"].'" data-funds="'.$scheme["total_funds"].'"
            data-gender="'.htmlspecialchars(json_encode($scheme["gender"])).'" data-category="'.htmlspecialchars(json_encode($scheme["category"])).'"
            style="margin-left: 5px;">
            View Scheme   
            </button>';
            $datum->actiontn = $actionbtn; 
            $scheme_arr[] = $datum; 

        } 
    } 
}
if (empty($scheme_arr)) {
    $tabledata = false;
}
$hash = array(
    'cfg' => $CFG->wwwroot,
    'pageurl' => $pageurl,
    'tabledata' => $tabledata,
    'schemedata' => $scheme_arr,
);
echo $OUTPUT->render_from_template('local_schememanagement/schemelist', $hash);
$PAGE->requires->js_call_amd('local_schememanagement/userLists', 'init');
$PAGE->requires->js_call_amd('local_schememanagement/viewscheme', 'init');
echo $OUTPUT->footer();
?>
<style type="text/css">
#users_deletion {
    margin-top: 15px;
}

.modal-header.header-color {
    background: #d7e9d7;
}

.modal-footer {
    border-top: 1px solid #e4eaec !important;
}

.fifty {
    width: 42%;
    display: block;
    float: left;
    margin: 5px 1px 5px 1px;
}

.hundred {
    width: 100%;
    display: block;
    margin: 5px 1px 5px 1px;
}
</style>