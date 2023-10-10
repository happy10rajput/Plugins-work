define(['jquery', 'core/ajax', 'core/templates', 'select2-js'], function($, ajax, templates) {
	return {
        init: function(schemeid, schadmn) {
          $(document).ready(function() {
            var promises = ajax.call([
              {
                methodname: 'local_schememanagement_coursebatchsize',
                args: {
                  schemeid: schemeid,
                }
              }
            ]);
            promises[0].done(function(result) {
              $("#batchsize").attr('data-value', result.batch_size);
              $("#batch").html(result.batch_size);
              $("#minimum").val(result.minbatch);
              $("#minimum").html(result.minbatch);
              if((result.batch_size) == 0) {
                $("#course_partner,#course,#batchsize,#allocate").attr('disabled', true);
                $("#min-batch,#minimum").hide();
              }
            }).fail(function(result) {
              Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Something went wrong',
                  showConfirmButton: false,
                  timer: 1500
                });
              });
          });
          
          $('#batchsize').on('input', function() {
            var inputValue = $(this).val();
            var dataValue = $(this).data('value');
            if (inputValue > dataValue) {
              $('#batch-error').html('Batch size cannot be greater than ' + dataValue);

            }
          });
          $('#course_partner').select2({
            placeholder: 'Select Course Partner'
          });
          $("#course").select2({

            placeholder: "Select Course",
            allowClear: true,
            ajax: {
                delay: 1000,
                data: function(params) {

                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        cpid: $('#course_partner').find(":selected").val(),
                        schemeid: schemeid,
                    };
                    return query;
                },

                transport: function(params, success, failure) {
                  if($('#course_partner').find(":selected").val() != ""){
                    var methodname = 'local_schememanagement_coursevalues';
                    var promises = ajax.call([{
                        args: params,
                        methodname: methodname
                    }]);
                    promises[0].done(success).fail(failure);
                }
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            },
          }
        });

          
            $('#allocate').on('click',function(e) {
              console.log(schadmn);
              if ($('#course_partner').val() === null) {
                Swal.fire({
                  position: 'center',
                  icon: 'info',
                  title: 'Please choose course partner',
                  showConfirmButton: false,
                  timer: 1500
                });
              } else if ($('#course').val() === null) {
                Swal.fire({
                  position: 'center',
                  icon: 'info',
                  title: 'Please choose course',
                  showConfirmButton: false,
                  timer: 1500
                });
              } else if(parseInt($("#batchsize").val())>parseInt($("#batchsize").attr("data-value"))){
                Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Batch size cannot be greater than '+$("#batchsize").attr("data-value"),
                  showConfirmButton: false,
                  timer: 1500
                });
              } else if ($("#batchsize").val() == 0) {
                Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Batch size cannot be 0',
                  showConfirmButton: false,
                  timer: 1500
                });
              } else if (parseInt($("#batchsize").val())<parseInt($("#minimum").val())) {
                Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Batch size cannot be less than '+$("#minimum").val(),
                  showConfirmButton: false,
                  timer: 1500
                });
              } else {
                var cpid = $('#course_partner').find(":selected").val();
                var courseid = $('#course').val();
                var promises = ajax.call([
                    {
                      methodname: 'local_schememanagement_mapping',
                      args: {
                        cpid: cpid,
                        courseid: courseid,
                        schemeid: schemeid,
                        batchsize:$("#batchsize").val(),
                        schadmn: schadmn !== 0 ? 1 : 0 ,
                      }
                    }
                    
                  ]);
                  promises[0].done(function(result) {
                    setTimeout(function() {
                      location.reload(true);
                      }, 500);
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Mapped Successfully',
                        showConfirmButton: false,
                        timer: 1500
                      });
                  }).fail(function(result) {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Something went wrong',
                        showConfirmButton: false,
                        timer: 1500
                      });
                    });
              }     
        });
        var cp_id,course_id;
        $('.delete').on('click', function(e){
            var cpid = $(e.currentTarget).attr("data-cpid");
            var courseid = $(e.currentTarget).attr("data-courseid");
            cp_id = $('#cpid').val(cpid);
            course_id = $('#courseid').val(courseid);
        });
        $('#delete_course').on('click', function(e){
          if($("#deletefield").val() == '') {
            $("#response").animate({height: '+=72px'}, 300);
         $('<div class="alert alert-danger">' +
                  '<button type="button" class="close" data-dismiss="alert">' +
                  '&times;</button>Reason cannot be empty</div>').hide().appendTo('#response').fadeIn(100);
        
        $(".alert").delay(1000).fadeOut("normal", function(){
          $(this).remove();
        });
        
         $("#response").delay(1000).animate({
              height: '-=72px'
          }, 300);
          } else {
          var cpId = cp_id.val();
          var courseId = course_id.val();
          e.preventDefault();   
          var promises = ajax.call([
            {
              methodname: 'local_schememanagement_deletecourse',
              args: {
                cpid : cpId,
                courseid : courseId,

              }
            }
          ]);
          promises[0].done(function(result) {
            $('#delete_modal').modal("hide");
              Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Deleted Successfully',
                showConfirmButton: false,
                timer: 1500
              })
            setTimeout(function(){ location.reload(); }, 1500);
            }).fail(function(result) {
            Swal.fire({
              position: 'center',
              icon: 'error',
              title: 'Something went wrong',
              showConfirmButton: false,
              timer: 1500
            })
              
              });
            }
        });
        $('#edit_course_partner').on('change',function(e){
          $('#edit_course').html('<option value=""></option>').trigger('change');
        });
        var sm_id;
        $('.edit').on('click', function(e){
          var cpid = $(e.currentTarget).attr("data-cpid");
          var courseid = $(e.currentTarget).attr("data-courseid");
          var smid = $(e.currentTarget).attr("data-id");
          var cname = $(e.currentTarget).attr("data-cname");
          sm_id = $("#smid").val(smid);
          $("#edit_course_partner").val(cpid).trigger('change');
          $("#edit_course").html('<option value="'+courseid+'" selected>'+cname+'</option>').trigger('change');
      });
        $('#edit_course_partner').select2({
          placeholder: 'Select Course Partner',
          allowClear: true,
        });
        $('#edit_course').select2({
          placeholder: 'Select Course',
          allowClear: true,
            ajax: {
                delay: 1000,
                data: function(params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        cpid: $('#edit_course_partner').val(),
                    };
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                transport: function(params, success, failure) {
                  if($('#course_partner').val() != ""){
                    var methodname = 'local_schememanagement_coursevalues';
                    var promises = ajax.call([{
                        args: params,
                        methodname: methodname
                    }]);
                    promises[0].done(success).fail(failure);
                }
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            },
          }
        
        });
        
      $('#editcourse').on('click',function(e){
        var cpid = $('#edit_course_partner').val();
        var courseid = $('#edit_course').val();
        e.preventDefault();
        var promises = ajax.call([
            {
              methodname: 'local_schememanagement_edit_course',
              args: {
                editid: sm_id.val(),
                cpid: cpid,
                courseid: courseid,
                
              }
            } 
          ]);
          promises[0].done(function(result) {
            $('#edit_modal').modal("hide");
            setTimeout(function() {
              location.reload(true);
              }, 500);
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Updated Successfully',
                showConfirmButton: false,
                timer: 1500
              })
          }).fail(function(result) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Something went wrong',
                showConfirmButton: false,
                timer: 1500
              })
            });
});


        }
    };
});