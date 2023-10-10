define(['jquery', 'core/ajax', 'core/templates', 'select2-js'], function($, ajax, templates) {
	return {
        init: function(schemeid) {
            $(".accept").on('click', function(e){
                var course_id = $(e.currentTarget).val();
                var promises = ajax.call([
                    {
                      methodname: 'local_schememanagement_acceptcourse',
                      args: {
                        courseid : course_id,
                        schemeid: schemeid
        
                      }
                    }
                  ]);
                  promises[0].done(function(result) {
                    $(e.currentTarget).prop("disabled", true);
                    
                      Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Accepted Successfully',
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
            });
            
            $('#choosecourse').select2({
              placeholder: 'Select Course',
              allowClear: true,
            });
            $('#choosecourse').on('change',function(e){
              var promises1 = ajax.call([
                {
                  methodname: 'local_schememanagement_batchsize',
                  args: {
                    courseid: $("#choosecourse").val(),
                    
                  }
                }
                
              ]);
              promises1[0].done(function(result) {
                console.log(result);
                $("#id_batch").val(result);
                $("#id_batch").attr('readonly', 'readonly');
              }).fail(function(result) {
                Swal.fire({
                    position: 'center',
                    icon: 'fail',
                    title: 'Something went wrong',
                    showConfirmButton: false,
                    timer: 1500
                  })
            });
          });



          $('#choosedistrict').on('change',function(e){
            var promises1 = ajax.call([
              {
                methodname: 'local_schememanagement_const_dropdown',
                args: {
                  districtid: $("#choosedistrict").val(),
                  
                }
              }
              
            ]);
            promises1[0].done(function(result) {
              $('#chooseconst').empty();
              $('#chooseconst').append(result);
            
            }).fail(function(result) {
              Swal.fire({
                  position: 'center',
                  icon: 'fail',
                  title: 'Something went wrong',
                  showConfirmButton: false,
                  timer: 1500
                })
          });
        });


            $('#choosedistrict').select2({
              placeholder: 'Select District',
              allowClear: true,
            });
         
          
            $('#chooseconst').select2({
              placeholder: 'Select Constituency',
              allowClear: true,
            });
         
          $('#choosecenter').select2({
            placeholder: 'Select Center',
            allowClear: true,
        });  
         
        $('.centeraccept').on('click', function(e){
          var promises = ajax.call([
            {
              methodname: 'local_schememanagement_centercreation',
              args: {
               
                courseid : $(e.currentTarget).val(),
                districtid: '',
                batchsize: '',
                center: '',
                constid:'',
                type: $(e.currentTarget).attr("data-center"),
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
                title: 'Accepted Successfully',
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
        var delete_id;
        $('.centerreject').on('click', function(e){
           var id = $(e.currentTarget).val();
           delete_id = $("#deleteid").val(id);
        });
        $('.delete_coursecenter').on('click', function(e){
          if($("#deletefield").val() == '') {
              $("#error_response").animate({height: '+=72px'}, 300);
              $('<div class="alert alert-danger">' +
                        '<button type="button" class="close" data-dismiss="alert">' +
                        '&times;</button>Reason cannot be empty</div>').hide().appendTo('#error_response').fadeIn(100);
              
              $(".alert").delay(1000).fadeOut("normal", function(){
                $(this).remove();
              });
              
              $("#error_response").delay(1000).animate({
                    height: '-=72px'
                }, 300);
          } else {
          e.preventDefault();   
          var promises = ajax.call([
            {
              methodname: 'local_schememanagement_centercreation',
              args: {
               
                courseid : delete_id.val(),
                districtid: '',
                batchsize: '',
                center: '',
                constid:'',
                type: $(e.currentTarget).attr("data-center"),
              }
            }
          ]);
          promises[0].done(function(result) {
            $('#delete_modal').modal("hide");
            setTimeout(function() {
              location.reload(true);
              }, 500);
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Rejected Successfully',
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
          }
        }); 
        $('.reapply').on('click', function(e){
          var promises = ajax.call([
            {
              methodname: 'local_schememanagement_reapply',
              args: {
               id: $("#reapply").val(),
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
                title: 'Reapplied Successfully',
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