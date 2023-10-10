define(["jquery", "core/ajax", "core/notification", "datatables.net", "datatables.net-bs4", "datatables.net-buttons", "datatables.net-buttons-bs4", "datatables.net-buttons-colvis", "datatables.net-buttons-print", "datatables.net-buttons-html", "datatables.net-buttons-flash", "datatables.net-responsive", "datatables.net-responsive-bs4"], function(e, t, a) {
    return {
        init: function() {
            e(".bLists").DataTable(), e("#users").DataTable({
                pageLength: 30,
                lengthChange: !1,
                paging: !1
            }), e("#programme").change(function() {
                // if ($(this).val() == 'all') {
                //     location.reload();
                // }
                var n = (e(this).find(":selected").val(), t.call([{
                        methodname: "local_batchmanagement_fetch_stream",
                        args: {
                            selectedval: e("#programme").val()
                        }
                    }])),
                    o = $("#programme option:selected").text();
                $("#selectedprogram").val(o), n[0].done(function(t) {
                    e(".stream").remove();
                    for (var a = 0; a < t.length; a++) e("#stream").append('<option class="stream" value=' + t[a].id + ">" + t[a].fullname + "</option>")
                })
                // .fail(function(e) {
                //     Swal.fire({
                //         icon: "error",
                //         title: e
                //     })
                // })
            }), e("#stream").change(function() {
                var r = (e(this).find(":selected").val(), t.call([{
                        methodname: "local_batchmanagement_fetch_semester",
                        args: {
                            selectedval: e("#programme").val()
                        }
                    }])),
                    n = $("#stream option:selected").text();
                $("#selectedstream").val(n), r[0].done(function(t) {
                    e(".semester").remove();
                    for (var a = 0; a < t.length; a++) e("#semester").append('<option class="semester" value=' + t[a].id + ">" + t[a].semester + "</option>")
                })
                // .fail(function(e) {
                //     Swal.fire({
                //         icon: "error",
                //         title: e
                //     })
                // })
            }), e("#semester").change(function() {
                    (e(this).find(":selected").val(), t.call([{
                    methodname: "local_batchmanagement_fetch_semester_year",
                    args: {
                        selectedval: e("#programme").val()
                    }
                }]))[0].done(function(t) {
                    e(".semesteryear").remove();
                    for (var a = 0; a < t.length; a++) e("#semesteryear").append('<option class="semesteryear" value=' + t[a].id + ">" + t[a].semester + "</option>");
                    for (a = 0; a < t.length; a++) e("#selectedsemesteryear").append('<option class="semesteryear" value=' + t[a].id + ">" + t[a].semester + "</option>")
                })
                // .fail(function(e) {
                //     Swal.fire({
                //         icon: "error",
                //         title: e
                //     })
                // })
            })
        }
    }
});