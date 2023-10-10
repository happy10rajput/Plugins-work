define(["jquery", "core/ajax", "core/templates"], function ($, ajax, templates) {
    var scroll = {
        variables: {
        limit: 8,
        start: 0,
        action: "inactive",
        activesupertab: 1
      },
      init: function () {
        $("#bundlesector").on("change", function () {
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
  
        $("#bundlecategory").on("change", function () {
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
  
        $("#jobrole").on("change", function () {
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
  
        $("#coursepartner").on("change", function () {
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
  
        $("#delivmode").on("change", function () {
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
  
        $("#courlevel").on("change", function () {
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
  
        // $('#coursename').keyup(function() {
        //   // scroll.resetRange();
        //   // scroll.load_course(scroll.variables.limit, scroll.variables.start);
        // });
      },
      resetRange: function () {
        scroll.variables.limit = 4;
        scroll.variables.start = 0;
      },
    };
    return {
      init: scroll.init
    };
  });
  