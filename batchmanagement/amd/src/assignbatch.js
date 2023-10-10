define(["jquery", "core/ajax", "core/notification",
    "datatables.net",
    "datatables.net-bs4",
    "datatables.net-buttons",
    "datatables.net-buttons-bs4",
    "datatables.net-buttons-colvis",
    "datatables.net-buttons-print",
    "datatables.net-buttons-html",
    "datatables.net-buttons-flash",
    "datatables.net-responsive",
    "datatables.net-responsive-bs4"
    ], function($, ajax, notification) {
        return {
            init: function() {

                $('.bLists').DataTable();
                $('#blist_tbl').DataTable();
                var sTable = $("#users").DataTable({
                    "pageLength": 30,
                    "lengthChange": false,
                    "paging":   false,
                });
                $('#programme').change(function () {
                    var programme = $(this).find(':selected').val();
                    
                    var promises = ajax.call([
                    {
                        methodname: 'local_batchmanagement_fetch_stream',
                        args: {
                            selectedval : $('#programme').val(),
                        }
                    }
                    ]);
                    promises[0].done(function(response) {
                        $(".stream").remove();
                        for (var i = 0; i < response.length; i++) {
                            $('#stream').append('<option class="stream" value=' + response[i].id + '>' + response[i].fullname + '</option>');
                        }  
                    }).fail(function(response) {
                        Swal.fire({
                            icon: "error",
                            title: response
                        });
                    });
                });

                $('#stream').change(function () {
                    var stream = $(this).find(':selected').val();
                    var promises = ajax.call([
                    {
                        methodname: 'local_batchmanagement_fetch_semester',
                        args: {
                            selectedval : $('#programme').val(),
                        }
                    }
                    ]);
                    promises[0].done(function(response) {
                        $(".semester").remove();
                        for (var i = 0; i < response.length; i++) {
                            $('#semester').append('<option class="semester" value=' + response[i].id + '>' + response[i].semester + '</option>');
                        }
                    }).fail(function(response) {
                        Swal.fire({
                            icon: "error",
                            title: response
                        });
                    });
                });

                $('#semester').change(function () {
                    var semester = $(this).find(':selected').val();
                    var promises = ajax.call([
                    {
                        methodname: 'local_batchmanagement_fetch_semester_year',
                        args: {
                            selectedval : $('#programme').val(),
                        }
                    }
                    ]);
                    promises[0].done(function(response) {
                        $(".semesteryear").remove();
                        for (var i = 0; i < response.length; i++) {
                            $('#semesteryear').append('<option class="semesteryear" value=' + response[i].id + '>' + response[i].semester + '</option>');
                        } 
                    }).fail(function(response) {
                        Swal.fire({
                            icon: "error",
                            title: response
                        });
                    });
                });

                $('#semesteryear').change(function () {
                    var semesteryear = $(this).find(':selected').val();
                    var stream = $('#stream').val();
                    var programme = $('#programme').val();
                    var semester = $('#semester').val();
                    var promises = ajax.call([
                    {
                        methodname: 'local_batchmanagement_fetch_batch',
                        args: {
                            selectedval : semesteryear,
                            stream : stream,
                            programme : programme,
                            semester : semester,
                        }
                    }
                    ]);
                    promises[0].done(function(response) {
                        $(".batch").remove();
                        if (response.length == 0) {
                            Swal.fire({
                                icon: "error",
                                title: 'Batch Not Found'
                            });

                        } else {
                            for (var i = 0; i < response.length; i++) {
                                $('#batch').append('<option class="batch" value=' + response[i].cohortid + '>' + response[i].batchname + '</option>');
                            }
                            
                        }
                    }).fail(function(response) {
                        Swal.fire({
                            icon: "error",
                            title: response
                        });
                    });
                });

                $('#batch').change(function () {
                    var batch = $(this).find(':selected').val();
                    var semesteryear = $('#semesteryear').val();
                    var stream = $('#stream').val();
                    var programme = $('#programme').val();
                    var semester = $('#semester').val();
                    var promises = ajax.call([
                    {
                        methodname: 'local_batchmanagement_fetch_users',
                        args: {
                            selectedval : batch,
                            stream : stream,
                            programme : programme,
                            semester : semester,
                            semesteryear : semesteryear
                        }
                    }
                    ]);
                    promises[0].done(function(response) {
                        $("#tbl").remove();
                        $("#tbl tbody").remove();
                        $("#tbl_wrapper").remove();
                        $('.batchTab').append('<table id="tbl" class="table batchTab">'+
                            '<thead>'+
                            '<tr>'+
                            '<th scope="col">First Name</th>'+
                            '<th scope="col">Last Name</th>'+
                            '<th scope="col">Email</th>'+
                            '<th scope="col"><input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">Select All</th>'+
                            '</tr>'+
                            '</thead>'+
                            '<tbody>'+
                            '</tbody>'+
                            '</table>');

                        if (response) {
                            $('#tbl tbody').html(response);
                            if (response != '<tr><td></td><td></td><td>NO RECORDS FOUND</td><td></td></tr>') {
                                var tabletr = $('#tbl tbody tr').length;
                                var tabletrcls = $('#tbl tbody tr.Assigned').length;
                                if (tabletrcls === tabletr) {
                                    $('.form-group input:submit').attr('disabled', true);
                                }
                            }
                        }

                        $('#inlineCheckbox1').on('click', function(e) {
                            $('tbody td input:checkbox').not(":disabled").prop("checked", this.checked);
                        });

                        $('#tbl').DataTable();
                    }).fail(function(response) {
                        $("#tbl").remove();
                        $("#tbl tbody").remove();
                        $("#tbl_wrapper").remove();
                        Swal.fire({
                            icon: "error",
                            title: response
                        });
                    });
                });

                // Put whatever you like here. $ is available
                // to you as normal.
                $('#formsubmit').click(function(e) {
                // $('#form').on('submit', function(e) {
                    if ($("#programme").val() == '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: "error",
                            title: "Select Programme"
                        });
                    } else if ($("#stream").val() == '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: "error",
                            title: "Select Stream"
                        });
                    } else if ($("#semester").val() == '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: "error",
                            title: "Select Semester"
                        });
                    } else if ($("#semesteryear").val() == '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: "error",
                            title: "Select Semester Year"
                        });
                    } else if ($("#batch").val() == '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: "error",
                            title: "Enter Batch Name"
                        });
                    } else if ($("input[type='checkbox'][name='user']:checked").length == 0) {
                        e.preventDefault();
                        Swal.fire({
                            icon: "error",
                            title: "Select Users"
                        });
                    } else {
                        e.preventDefault();
                        var userIDs = $("input[type='checkbox'][name='user']:checked").map(function(){
                            return $(this).val();
                        }).get(); 
                        var promises = ajax.call([
                        {
                            methodname: 'local_batchmanagement_assign_users',
                            args: {
                                batch : $('#batch').val(),
                                users : JSON.stringify(userIDs)
                            }
                        }
                        ]);
                        promises[0].done(function(result) {
                            Swal.fire({
                                icon: "success",
                                title: result
                            });
                            setTimeout(function(){
                                location.reload(true); 
                            }, 2000); 
                        }).fail(function(result) {
                            Swal.fire({
                                icon: "error",
                                title: result
                            });
                        });
                    }
                });

                // Remove user from batch.
                $(document).on("click",'.unAssign',function(e) {
                    var userid = $(this).attr('id');
                    var cohortid = $(this).attr('data-value');
                    var thiscls = $(this).attr('class');
                    var promises2 = ajax.call([
                    {
                        methodname: 'local_batchmanagement_unassign_users_frombatch',
                        args: {
                            userid : userid,
                            cohortid : cohortid
                        }
                    }
                    ]);
                    promises2[0].done(function(result) {
                        if (result == true) {
                            $('#modalMsg').removeClass('hidden');
                            setTimeout(function(){
                                location.reload(true); 
                            }, 2000); 
                        }
                    });
                });

                // Assign User Modal.
                $(document).on("click",'#viewaUsers',function(e) {
                    var cohortid = $(this).attr('data-value');
                    var promises3 = ajax.call([
                    {
                        methodname: 'local_batchmanagement_view_assigned_user',
                        args: {
                            cohortid : cohortid
                        }
                    }
                    ]);
                    promises3[0].done(function(result) {
                        $('#assignUsers').modal();
                        $('#userModal').html(result);
                    });
                });

            }
        };
    });
