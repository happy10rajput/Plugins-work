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
// defined('MOODLE_INTERNAL') || die();
global $DB, $USER, $CFG;
use cache;
require_once('lib.php');
unset($_SESSION['bundlelogin']);
//\core\session\manager::write_close(); // Unlock session during file serving.

$context = context_system::instance();
$page = optional_param('page', 0, PARAM_INT);   // which page to show

$PAGE->set_context($context);
$PAGE->set_pagelayout('marketplace');
/*$pageloadurl = $_SERVER['PHP_SELF'];
$pagepass = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
$PAGE->set_url($pagepass);*/
$pagepass = $CFG->wwwroot.'/local/schememanagement/schemecatalogue.php';
$PAGE->set_url($pagepass);

$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/theme/remui/style/homepage.css'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/local/schememanagement/style/schemecatalog.css'));

$PAGE->set_title(get_string('pluginname', 'local_marketplace'));
$PAGE->set_heading(get_string('pluginname', 'local_marketplace'));
$PAGE->navbar->add(get_string('pluginname', 'local_marketplace'));
$starttime = microtime(true); // Top of page
// Code

$secid = optional_param('secid', null, PARAM_INT);
$catid = optional_param('catid', null, PARAM_INT);
$jroleid = optional_param('jrole', null, PARAM_INT);
$cocrid = optional_param('cocrid', null, PARAM_INT);
$modeid = optional_param('modeid', null, PARAM_INT);
$clvlid = optional_param('clvlid', null, PARAM_INT);
$cname = optional_param('cname', null, PARAM_RAW);
$pge = optional_param('pge', null, PARAM_INT);

$pge = ($pge) ? $pge : 1;
echo $OUTPUT->header();
$pluginlink = $CFG->wwwroot . '/theme/remui';
$cache = cache::make('local_marketplace', 'sccatalog');
$cache->set('cplist', schcpartners($cocrid));
$cache->set('scstreams', schsecstreams($secid));
$cache->set('sccategories', schemecats($catid));
$cache->set('schemejobroles', schemejobroles($jroleid));
$cache->set('coursemodes', schscmodes($modeid, 0));
$cache->set('courselevels', schclevels($clvlid));

$getfilterby = "";
$coursemode = null;
$bundlesector = ($secid) ? $secid : null; // null;
$bundlesubcategory = null;
$pindex = 0;
$bundlecategory = ($catid) ? $catid : 0; // 0
// ($pindex == 1) ? (($bundlecategory == 0) ? $bundlecategory = 219 : $bundlecategory = $bundlecategory) : $bundlecategory = $bundlecategory;
$limit = 8;
$start = ($pge - 1) * $limit;
$duration = "";
$price = "";
$courselevel = "";
$rating = "";
$coursepartner = ($cocrid) ? $cocrid : 0;
$searchstring = "";
$getbyids = 0;
$orderby = 1;
$mode = ($modeid) ? $modeid : 0;
$courlevel = ($clvlid) ? $clvlid : 0;
$jobrole = ($jroleid) ? $jroleid : 0;
$coursename = ($cname) ? $cname : "";

// $getsccourses['coursecount'] = getfullprods($getfilterby, $coursemode, $bundlesector, $bundlecategory, $bundlesubcategory = null, "", "", $duration, $price, $courselevel, $rating, $coursepartner, $searchstring, $getbyids, $orderby, $mode, $pindex, $courlevel, $jobrole, $coursename, 0);
$getsccourses['coursedata'] = getschfullprods($getfilterby, $coursemode, $bundlesector, $bundlecategory, $bundlesubcategory = null, $limit, $start, $duration, $price, $courselevel, $rating, $coursepartner, $searchstring, $getbyids, $orderby, $mode, $pindex, $courlevel, $jobrole, $coursename, 0);
// print_object($getsccourses['coursedata']);
// die();
// $totentries = $cache->get('totalschemerecs');
$totentries = $_SESSION['totalschemerecs'];
$totpages = ceil($totentries / $limit);
$fpgehilite = ($pge == 1) ? 'fpgehilite' : '';
$lpgehilite = ($pge == $totpages) ? 'lpgehilite' : '';
$getsccourses['pdata'] = get_schpage_numbers($totpages, $pge);
($pge == $totpages) ? $getsccourses['plast'] = 1 : $getsccourses['plast'] = 0;
($pge == 1) ? $getsccourses['pfirst'] = 1 : $getsccourses['pfirst'] = 0;
$lastentri = ( ($pge * $limit) > $totentries ) ? $totentries : ($pge * $limit);
$pagemsg = ((($pge - 1) * $limit ) + 1)." to ".$lastentri." Entries ";
$getsccourses['pge'] = $pge;
$getsccourses['totpages'] = $totpages;
$getsccourses['site_url'] = $CFG->wwwroot;
$getsccourses['fpgehilite'] = $fpgehilite;
$getsccourses['lpgehilite'] = $lpgehilite;
$getsccourses['pagemsg'] = $pagemsg;
$hash = [
         'pluginlink' => $pluginlink,
         'site_url' => $CFG->wwwroot,
         'coursepartners' => $cache->get('cplist'),
         'categoryarray' => $cache->get('sccategories'),
         // 'subcategoriesarray' => $subcategoriesarray,
         'sectorarray' => $cache->get('scstreams'),
         'jobsarray' => $cache->get('schemejobroles'),
         'coursemodes' => $cache->get('coursemodes'),
         'courselevels' => $cache->get('courselevels'),
         'pageurl' => $pagepass,
         'secfilter' => $secid,
         'catfilter' => $catid,
         'jrolefilter' -> $jroleid,
         'cpfilter' => $coursepartner,
         'modefilter' => $modeid,
         'cname' => $cname
      ];

echo $OUTPUT->render_from_template('local_schememanagement/schemecatalogue', $hash);
echo $OUTPUT->render_from_template('local_schememanagement/course-full', $getsccourses);
// $PAGE->requires->js_call_amd('local_schememanagement/newcoursecard', 'init');
// $PAGE->requires->js_call_amd('local_marketplace/index', 'init');
// $PAGE->requires->js_call_amd('local_marketplace/scroll', 'init');
// echo $OUTPUT->footer();

$endtime = microtime(true); // Bottom of page

printf("Page loaded in %f seconds", $endtime - $starttime );

?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>
   function redirectToRegistration(url) {
      var pid = $(".pidpopup").val();
      window.location.href = url + '/local/user_auth/userRegistration.php?plan=2&ut=2&pid=' + pid;
   }

   $(document).on("click", ".shopping-bag", function(e) {
      e.preventDefault();
      var productid = $(this).data('productid');
      if ($(this).data('id') == 1 || $(this).data('normal') == 1) {
         $(".pidpopup").val(productid);
         $.post("setsession.php", {
            "productid": productid + '|1'
         });
      } else {

         if ($('input[name="optradio"]:checked').length == 0) {
            swal.fire({
               icon: 'error',
               title: 'Select a batch to proceed',
               timer: 1500
            })
         } else {
            $("#myBatchModal").modal('hide');
            $("#myfreeBatch").modal('hide');
            $("#exampleModal").modal('show');
            $('#exampleModal').modal({
               backdrop: 'static',
               keyboard: false
            });
            var batch = $('input[name="optradio"]:checked').val();
            $(".pidpopup").val(productid + '|' + batch);
            $.post("setsession.php", {
               "productid": productid + '|' + batch
            });
         }
      }

   });

   $(".closemodal").click(function() {
      $.post("destroysession.php", {
         "destroy": 1
      });
   });

   function selfregister(courseid){
      var url = 'selfenroluserajax.php';
      $.ajax({
         url: url,
         dataType: 'json',
         type: 'get',
         data: {courseid:courseid},
         success:function(response){
               if(response.success == 1){
                  Swal.fire({
                  position: "center",
                  title: "Enrolled Successfully",
                  timer: 2500
                });
                  location.reload()
               }
         }
      });
   }


   $(document).on('click', '.show_password .showicon', function() {
      var password = $(".show_password #password");
      var fieldtype = password.attr('type');
      if (fieldtype == 'password') {
         $('.show_password #password').attr('type', 'text');
         $(this).text(" "); // hide
         $(this).append('<i class="fa fa-eye fa-sm" aria-hidden="true"></i>');
      } else {
         $('.show_password #password').attr('type', 'password');
         $(this).text(" ");
         $(this).append('<i class="fa fa-eye-slash fa-sm" aria-hidden="true"></i>');
      }
   });

   // JS Events shifted from .js file to current file
   $(document).on('change','#bundlesector', function(e) {   
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      // (cpge) ? newurl += "&pge="+cpge : newurl = newurl;
      newurl += "&pge=1";
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   $(document).on('change','#bundlecategory', function(e) {   
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      // (cpge) ? newurl += "&pge="+cpge : newurl = newurl;
      newurl += "&pge=1";
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   $(document).on('change','#jobrole', function(e) {   
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      // (cpge) ? newurl += "&pge="+cpge : newurl = newurl;
      newurl += "&pge=1";
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   $(document).on('change','#coursepartner', function(e) {   
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      // (cpge) ? newurl += "&pge="+cpge : newurl = newurl;
      newurl += "&pge=1";
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   $(document).on('change','#delivmode', function(e) {   
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      // (cpge) ? newurl += "&pge="+cpge : newurl = newurl;
      newurl += "&pge=1";
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   $(document).on('change','#courlevel', function(e) {   
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      // (cpge) ? newurl += "&pge="+cpge : newurl = newurl;
      newurl += "&pge=1";
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   $(document).on('click','.pprev', function(e) {
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var totpages = $("#totpages").val();
      var nwpge = $(this).data("id");
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      (cpge && (parseInt(cpge) > 1)) ? newurl += "&pge="+(cpge-1) : newurl += "&pge="+cpge;
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   $(document).on('click','.pind', function(e) {
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();
      var totpages = $("#totpages").val();
      var nwpge = $(this).data("id");
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      (nwpge) ? newurl += "&pge="+nwpge : newurl += "&pge="+cpge;
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
               }, 500);
   });

   $(document).on('click','.pnext', function(e) {
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();        
      var totpages = $("#totpages").val();
      var nwpge = $(this).data("id");
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      if (cpge && (parseInt(cpge) < parseInt(totpages))) {
      newurl += "&pge="+(parseInt(cpge) + parseInt(1));
      } else {
      newurl += "&pge="+cpge;
      }
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });
    
   $(document).on('click','.page-first', function(e) {
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();        
      var totpages = $("#totpages").val();
      var nwpge = $(this).data("id");
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      newurl += "&pge=1";
      // if (cpge && (parseInt(cpge) < parseInt(totpages))) {
      //    newurl += "&pge="+(parseInt(cpge) + parseInt(1));
      // } else {
      //    newurl += "&pge="+cpge;
      // }
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });
  

   $(document).on('click','.page-last', function(e) {
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();        
      var totpages = $("#totpages").val();
      var nwpge = $(this).data("id");
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      newurl += "&pge="+totpages;
      // if (cpge && (parseInt(cpge) < parseInt(totpages))) {
      //    newurl += "&pge="+(parseInt(cpge) + parseInt(1));
      // } else {
      //    newurl += "&pge="+cpge;
      // }
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });
  
   $(document).on('click','#cnamesearch', function(e) {
      var pageurl = $('#pageurl').val();
      var secid = $("#bundlesector").val();
      var catid = $("#bundlecategory").val();
      var jrole = $("#jobrole").val();
      var cocrid = $("#coursepartner").val();
      var modeid = $("#delivmode").val();
      var clevel = $("#courlevel").val();
      var cname = $("#coursename").val();
      var cpge = $("#pge").val();        
      var totpages = $("#totpages").val();
      var nwpge = $(this).data("id");
      var newurl = pageurl+"?pindex=0";
      (secid) ? newurl += "&secid="+secid : newurl = newurl;
      (catid) ? newurl += "&catid="+catid : newurl = newurl;
      (jrole) ? newurl += "&jrole="+jrole : newurl = newurl;
      (cocrid) ? newurl += "&cocrid="+cocrid : newurl = newurl;
      (modeid) ? newurl += "&modeid="+modeid : newurl = newurl;
      (clevel) ? newurl += "&clvlid="+clevel : newurl = newurl;
      (cname) ? newurl += "&cname="+cname : newurl = newurl;
      newurl += "&pge=1";
      window.history.pushState({path:newurl}, '', newurl);
            setTimeout(function() {
                  location.reload(true);
                  }, 500);
   });

   // $(document).on("click", ".navbar-avatar", function(e) {
   //    // $(".dropdown-menu").addClass('show');
   //    $(".cart-popup-bg").hide();
   //    $(".dropdown-menu").toggleClass("show");
   //    // $(".dropdown-menu").slideToggle("show");
   //    // $(".navbar-right").css("box-shadow", "#FFFFFF");
   // });

</script>

