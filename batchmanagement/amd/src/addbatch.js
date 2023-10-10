define(["jquery", "core/ajax", "core/notification", "datatables.net", "datatables.net-bs4", "datatables.net-buttons", "datatables.net-buttons-bs4", "datatables.net-buttons-colvis", "datatables.net-buttons-print", "datatables.net-buttons-html", "datatables.net-buttons-flash", "datatables.net-responsive", "datatables.net-responsive-bs4"], function(a, b, c) {
    return {
        init: function() {
            var c = a("body").find(".generaltable").length;
            0 != c && a(".generaltable").DataTable(), a("#programme").change(function() {
                var c = (a(this).find(":selected").val(), b.call([{
                    methodname: "local_batchmanagement_fetch_stream",
                    args: {
                        selectedval: a("#programme").val()
                    }
                }]));
                c[0].done(function(b) {
                    a(".stream").remove();
                    for (var c = 0; c < b.length; c++) a("#stream").append('<option class="stream" value=' + b[c].id + ">" + b[c].fullname + "</option>")
                }).fail(function(a) {
                    Swal.fire({
                        icon: "error",
                        title: a
                    })
                })
        }), a("#stream").change(function() {
            var c = (a(this).find(":selected").val(), b.call([{
                methodname: "local_batchmanagement_fetch_semester",
                args: {
                    selectedval: a("#programme").val()
                }
            }]));
            c[0].done(function(b) {
                a(".semester").remove();
                for (var c = 0; c < b.length; c++) a("#semester").append('<option class="semester" value=' + b[c].id + ">" + b[c].semester + "</option>")
            }).fail(function(a) {
                Swal.fire({
                    icon: "error",
                    title: a
                })
            })
    }), a("#semester").change(function() {
        var c = (a(this).find(":selected").val(), b.call([{
            methodname: "local_batchmanagement_fetch_semester_year",
            args: {
                selectedval: a("#programme").val()
            }
        }]));
        c[0].done(function(b) {
            a(".semesteryear").remove();
            for (var c = 0; c < b.length; c++) a("#semesteryear").append('<option class="semesteryear" value=' + b[c].id + ">" + b[c].semester + "</option>")
        }).fail(function(a) {
            Swal.fire({
                icon: "error",
                title: a
            })
        })
}), a("#form").on("submit", function(c) {
    if ("" == a("#programme").val()) c.preventDefault(), Swal.fire({
        icon: "error",
        title: "Select Programme"
    });
        else if ("" == a("#stream").val()) c.preventDefault(), Swal.fire({
            icon: "error",
            title: "Select Stream"
        });
            else if ("" == a("#semester").val()) c.preventDefault(), Swal.fire({
                icon: "error",
                title: "Select Semester"
            });
                else if ("" == a("#semesteryear").val()) c.preventDefault(), Swal.fire({
                    icon: "error",
                    title: "Select Semester Year"
                });
                    else if ("" == a("#batchname").val()) c.preventDefault(), Swal.fire({
                        icon: "error",
                        title: "Enter Batch Name"
                    });
                        else {
                            c.preventDefault();
                            var d = b.call([{
                                methodname: "local_batchmanagement_add_batch",
                                args: {
                                    programme: a("#programme").val(),
                                    stream: a("#stream").val(),
                                    semester: a("#semester").val(),
                                    semesteryear: a("#semesteryear").val(),
                                    batchname: a("#batchname").val(),
                                    batchcode: a("#batchcode").val()
                                }
                            }]);
                            d[0].done(function(a) {
                                if(a==1){
                                    Swal.fire({
                                        icon: "success",
                                        title: "Batch Created Successfully"
                                    })
                                }else{
                                    Swal.fire({
                                        icon: "error",
                                        title: "Batch name already exists. Please add a New Name"
                                    })
                                }
                                setTimeout(function() {
                                    location.reload(!0)
                                }, 2e3)
                            }).fail(function(a) {
                                Swal.fire({
                                    icon: "error",
                                    title: a
                                })
                            })
                        }
                    })
}
}
});