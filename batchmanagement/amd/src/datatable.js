define(["jquery", "core/ajax", "core/notification", "core/url", "datatables.net", "datatables.net-bs4", "datatables.net-buttons", "datatables.net-buttons-bs4", "datatables.net-buttons-colvis", "datatables.net-buttons-print", "datatables.net-buttons-html", "datatables.net-buttons-flash", "datatables.net-responsive", "datatables.net-responsive-bs4"], function(t, a, e, n) {
  return {
    init: function() {
      $("#example1").DataTable({
        dom: 'Bfrtip',
        buttons: [{
           extend : 'csv',
           text : 'Export to excel'
         }]
      });
      $("#freeexample1").DataTable({
        dom: 'Bfrtip',
        buttons: [{
           extend : 'csv',
           text : 'Download webinars List'
         }]
      });
      
      $("#guestexample1").DataTable({
        dom: 'Bfrtip',
        buttons: [{
           extend : 'csv',
           text : 'Download User List'
         }]
      });
      
      $("#b_coursedata").DataTable();
      
      $("#search").click(function() {
        var pgrm = $("#programme").val();
        var stm = $("#stream").val();     
        var smtr = $("#semester").val();
        var smyr = $("#semesteryear").val();
        $.ajax({        
          url: "fetch_batch.php",
          method: "POST",         
          data : 'program='+pgrm+'&stream='+stm+'&semester='+smtr+'&semesteryear='+smyr,
          success: function(data) {

            $('#pagedata').html(data);
            $("#example1").DataTable();

          }
        })
      });
      // $('.quickediticon').click(function () {
        $(document).on('click','.quickediticon', function(e) {
            // alert('hiiiiiiiiiiiiii');
            var batch_d = $(this).attr('data-value');
            var res = batch_d.split("--");
            console.log(res);
            // alert(res[0])
            $('#emty_sec'+res[1]).empty();
            $('#emty_sec'+res[1]).append('<input type="text" id="save_batchname'+res[1]+'" value="'+res[0]+'"/><a class="batchname_save" data-value="'+res[0]+'--'+res[1]+'"><i class="icon fa fa-floppy-o fa-fw " title="Save changes" aria-label="Save changes"></i></a><a class="cancel" data-value="'+res[0]+'--'+res[1]+'"><i class="icon fa fa-times fa-fw" title="Cancel" aria-label="Cancel"></i></a>');

          });
      // $('.cancel').click(function () {
        $(document).on('click','.cancel', function(e) {

          var batch_d = $(this).attr('data-value');
          var res = batch_d.split("--");
          $('#emty_sec'+res[1]).empty();
          $('#emty_sec'+res[1]).append('<span id="emty_sec'+res[1]+'">'+res[0]+'<a class="quickediticon" data-value="'+res[0]+'--'+res[1]+'"><span class="visibleifjs"><i class="icon fa fa-pencil fa-fw"></i></span></a></span>');

        });

        $(document).on('click','.batchname_save', function(e) {
          var batch_d = $(this).attr('data-value');
          var res = batch_d.split("--");
          var new_batchname = $("#save_batchname"+res[1]+"").val();
          var promises = a.call([
          {
            methodname: 'local_custom_service_all_update_query',
            args: {
              type : 'update_batchname',
              value : new_batchname,
              id : res[1]
            }
          }
          ]);
          promises[0].done(function(response) {
           if (response == 'already_exist') {
            Swal.fire({
              icon: "error",
              title: 'Batch name already exists. Please add a New Name'
            });
          } else {

           $('#emty_sec'+res[1]).empty();
           $('#emty_sec'+res[1]).append('<span id="emty_sec'+res[1]+'">'+response+'<a class="quickediticon" data-value="'+response+'--'+res[1]+'"><span class="visibleifjs"><i class="icon fa fa-pencil fa-fw"></i></span></a></span>');
         }
       }).fail(function(response) {
        Swal.fire({
          icon: "error",
          title: response
        });
      });
     });
      }
    }
  });