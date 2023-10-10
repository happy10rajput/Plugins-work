
define(['jquery', 'core/ajax', 'core/templates', 'select2-js'], function($, ajax, templates) {
	return {
        init: function(courseid, userid, schemeid) {
          var center_id;
          $(".apply_now").on('click', function(e) {
            // console.log($(e.currentTarget).attr('data-centerid'));
            center_id = $("#centerid").val($(e.currentTarget).attr('data-centerid'));
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
                var promises = ajax.call([
                    {
                      methodname: 'local_schememanagement_schemeapply',
                      args: {
                        courseid : courseid,
                            userid: userid,
                            schemeid: schemeid,
                            centerid: center_id.val(),     
                      }
                    }
                  ]);
                  promises[0].done(function(result) {
                    $("#apply_offline_course_modal").hide();
                    // $(e.currentTarget).prop("disabled", true);
                    if (result) {
                      Swal.fire({
                        icon: "success",
                        position: 'center',
                        title: 'Applied Successfully',
                        //timer: 5000
                      })
                      setTimeout(function() {
                          window.location.replace(result);
                      }, 1500);
                    }
                    if (result == 'updated') {
                        Swal.fire({
                          position: 'center',
                          icon: 'success',
                          title: 'Re-applied Successfully',
                          showConfirmButton: false,
                          timer: 1500
                        })
                    } else if (result == 'success') {
                      Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Applied Successfully',
                        showConfirmButton: false,
                        timer: 1500
                      })
                    } else if (result == 'errordata') {
                      Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'You have already applied for this course (Partner LMS)',
                        showConfirmButton: true,
                        timer: 1500
                      })
                    } else if (result == 'applied') {
                      Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'You have already applied for this Course',
                        showConfirmButton: true,
                        timer: 1500
                      })
                    } else if (result == 'errordata1') {
                      Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'ERROR in updating Partner LMS',
                        showConfirmButton: true,
                        timer: 1500
                      })
                    }
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

            // $("#batchs5").on('click',function(e){
            //   console.log("hii");
            //   var promises = ajax.call([
            //     {
            //       methodname: 'local_schememanagement_scheme_dropdown',
            //       args: {
            //         courseid : courseid,
            //             // userid: userid         
            //       }
            //     }
            //   ]);
            //   promises[0].done(function(result) {
            //     // $('#batchs5').empty();
            //     $('#batchs5').append(result);
            //     // $(e.currentTarget).prop("disabled", true);
                
            //       // Swal.fire({
            //       //   position: 'center',
            //       //   icon: 'success',
            //       //   title: 'Applied Successfully',
            //       //   showConfirmButton: false,
            //       //   timer: 1500
            //       // })
            //       // setTimeout(function(){ location.reload(); }, 1500);
            //     }).fail(function(result) {
            //     // Swal.fire({
            //     //   position: 'center',
            //     //   icon: 'error',
            //     //   title: 'Something went wrong',
            //     //   showConfirmButton: false,
            //     //   timer: 1500
            //     })
                  
            //       // });
            //   // $('#edit_course').html('<option value=""></option>').trigger('change');
            // });
        
        }
    };
});