<?php
// function get_users_listing_custom($page=0, $recordsperpage=0,$childid = array(), $extraselect='',
//  array $extraparams=null, $extracontext = null) {
//   global $DB, $CFG;

//     // $fullname  = $DB->sql_fullname();

//     // $select = "deleted <> 1 AND id <> :guestid";
//   $params = array('guestid' => $CFG->siteguest);
//   $sort='firstname';
//   $dir='ASC';

//   if ($extraselect) {
//     $select .= " AND $extraselect";
//     $params = $params + (array)$extraparams;
//   }

//   if ($sort) {
//     $sort = " ORDER BY $sort $dir";
//   }

//   $extrafields = '';
//   if ($extracontext) {
//     $extrafields = get_extra_user_fields_sql($extracontext, '', '',
//       array('u.id', 'u.username', 'u.email', 'u.firstname', 'u.lastname', 'u.city', 'u.country',
//         'u.lastaccess', 'u.confirmed', 'u.mnethostid'));
//   }
//   $namefields = get_all_user_name_fields(true);
//   $extrafields = "$extrafields, $namefields";
//     // $namefields = get_all_user_name_fields(true);

//     // warning: will return UNCONFIRMED USERS
//   return $DB->get_records_sql("SELECT u.id, u.username, u.email, u.city, u.country, u.lastaccess, u.confirmed, u.mnethostid, u.suspended, us.createdby, u.timecreated $extrafields
//    FROM {user} u
//    JOIN {usertype} us ON u.id = us.userid
//    WHERE us.usertype ='Student' AND u.deleted=0 AND us.deleted=0 AND us.createdby IN ($childid)
//    $sort",$params , $page, $recordsperpage);

// }

// function get_users_listing_custom_record_count($childid = array()) {
//   global $DB, $CFG;

//   $record = $DB->get_records_sql("SELECT u.id, u.username, u.email, u.city, u.country, u.lastaccess, u.confirmed, u.mnethostid, u.suspended, us.createdby, u.timecreated
//    FROM {user} u
//    JOIN {usertype} us ON u.id = us.userid
//    WHERE us.usertype ='Student' AND u.deleted=0 AND us.deleted=0 AND us.createdby IN ($childid)");
//   return count($record);

// }
function get_batch_listing_custom_college($page=0, $recordsperpage=0,$childid = array(), $extraselect='',
  array $extraparams=null, $extracontext = null) {
  global $DB, $USER, $CFG;

    // $fullname  = $DB->sql_fullname();

    // $select = "deleted <> 1 AND id <> :guestid";
  $params = array('guestid' => $CFG->siteguest);
  $sort='bd.timecreated';
  $dir='ASC';

  if ($extraselect) {
    $select .= " AND $extraselect";
    $params = $params + (array)$extraparams;
  }

  if ($sort) {
    $sort = " ORDER BY $sort $dir";
  }
  $extrafields = '';
  if ($extracontext) {
    $extrafields = get_extra_user_fields_sql($extracontext, '', '',
      array('bd.id', 'bd.batchname', 'bd.batchcode', 'sp.fullname', 'ss.fullname', 'sse.semester', 'sy.semester', 'bd.createdby', 'bd.timecreated'));
  }
  $namefields = get_all_user_name_fields(true);
  $extrafields = "$extrafields, $namefields";
  if (is_siteadmin()) {

    return $DB->get_records_sql("SELECT bd.id, bd.batchname, bd.batchcode, sp.fullname, ss.fullname, sse.semester, sy.semester, bd.createdby, bd.timecreated
      FROM {batch_details} as bd 
      JOIN {student_programme} as sp ON bd.programme = sp.id
      JOIN {student_stream} as ss ON bd.stream = ss.id
      JOIN {student_semester} as sse ON bd.semester = sse.id
      JOIN {student_sem_year} as sy ON bd.semyear = sy.id
      WHERE bd.createdby=".$USER->id." $sort",$params , $page, $recordsperpage);

  } else {    

    return $DB->get_records_sql("SELECT  bd.id, bd.batchname as batchname, bd.batchcode as batchcode, sp.fullname as program, ss.fullname as stream, sse.semester as semester, sy.semester as year, bd.cohortid as cohortid, bd.createdby, bd.timecreated
      FROM {batch_details} as bd 
      JOIN {student_programme} as sp ON bd.programme = sp.id
      JOIN {student_stream} as ss ON bd.stream = ss.id
      JOIN {student_semester} as sse ON bd.semester = sse.id
      JOIN {student_sem_year} as sy ON bd.semyear = sy.id
      WHERE bd.createdby=".$USER->id." $sort ",$params , $page, $recordsperpage);
  }
}

function get_batch_listing_custom_record_count_college() {
  global $DB, $CFG, $USER;
  if (is_siteadmin()) {

    $record =  $DB->get_records_sql("SELECT u.id, u.username, u.email, u.city, u.country, u.lastaccess, u.confirmed, u.mnethostid, u.suspended, us.createdby, u.timecreated
      FROM {user} as u 
      JOIN {usertype} as us ON u.id = us.userid 
      WHERE u.deleted=0 AND us.deleted=0 AND us.usertype='Student' AND us.deleted = 0");
    return count($record); 
  } else {

    $record = $DB->get_records_sql("SELECT bd.id, bd.batchname as batchname, bd.batchcode as batchcode, sp.fullname as program, ss.fullname as stream, sse.semester as semester, sy.semester as year, bd.cohortid as cohortid, bd.createdby, bd.timecreated
      FROM {batch_details} as bd 
      JOIN {student_programme} as sp ON bd.programme = sp.id
      JOIN {student_stream} as ss ON bd.stream = ss.id
      JOIN {student_semester} as sse ON bd.semester = sse.id
      JOIN {student_sem_year} as sy ON bd.semyear = sy.id
      WHERE bd.createdby=".$USER->id);
    return count($record);
  }


}