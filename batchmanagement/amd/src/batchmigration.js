define(["jquery", "core/ajax", "core/notification", "datatables.net", "datatables.net-bs4", "datatables.net-buttons", "datatables.net-buttons-bs4", "datatables.net-buttons-colvis", "datatables.net-buttons-print", "datatables.net-buttons-html", "datatables.net-buttons-flash", "datatables.net-responsive", "datatables.net-responsive-bs4"], function(t, a, r) {
    return {
        init: function() {
            t(".bLists").DataTable(), t("#users").DataTable({
                pageLength: 30,
                lengthChange: !1,
                paging: !1
            }), t("#programme").change(function() {
                $("#selectedprogram").val();
                var e = $("#selectedstream").val(),
                    r = $("#selectedbatch").val(),
                    l = $("#selectedsem").val(),
                    s = $("#selectedsemesteryear").val();
                (e || r || l || s) && ($("#selectedstream").val(null), $("#selectedbatch").val(null), $("#selectedsem").val(null), $("#selectedsemesteryear").val(null));
                var n = (t(this).find(":selected").val(), a.call([{
                        methodname: "local_batchmanagement_fetch_stream",
                        args: {
                            selectedval: t("#programme").val()
                        }
                    }])),
                    o = $("#programme option:selected").text();
                $("#selectedprogram").val(o), n[0].done(function(e) {
                    t(".stream").remove();
                    for (var a = 0; a < e.length; a++) t("#stream").append('<option class="stream" value=' + e[a].id + ">" + e[a].fullname + "</option>")
                }).fail(function(e) {
                    Swal.fire({
                        icon: "error",
                        title: e
                    })
                })
            }), t("#stream").change(function() {
                var e = (t(this).find(":selected").val(), a.call([{
                        methodname: "local_batchmanagement_fetch_semester",
                        args: {
                            selectedval: t("#programme").val()
                        }
                    }])),
                    r = $("#stream option:selected").text();
                $("#selectedstream").val(r), e[0].done(function(e) {
                    t(".semester").remove();
                    for (var a = 0; a < e.length; a++) t("#semester").append('<option class="semester" value=' + e[a].id + ">" + e[a].semester + "</option>")
                }).fail(function(e) {
                    Swal.fire({
                        icon: "error",
                        title: e
                    })
                })
            }), t("#semester").change(function() {
                var e = $("#semester option").length,
                    r = t("#stream").val(),
                    l = t("#semester").val();
                (t(this).find(":selected").val(), a.call([{
                    methodname: "local_batchmanagement_fetch_semester_year_new",
                    args: {
                        selectedval: t("#programme").val(),
                        stream: r,
                        semester: l
                    }
                }]))[0].done(function(a) {
                    t(".semesteryear").remove();
                    var r = e - 1;
                    if (parseInt($("#semester option:selected").text()), (l = parseInt($("#semester option:selected").text())) < r) {
                        var l = l + 1;
                        $("#selectedsem").val(l)
                    } else Swal.fire({
                        icon: "error",
                        title: "Semester " + e + " does not exists for this Program"
                    }), $("#form")[0].reset();
                    for (var s = 0; s < a.length; s++) t("#semesteryear").append('<option class="semesteryear" value=' + a[s].id + ">" + a[s].semester + "</option>");
                    for (s = 0; s < a.length; s++) t("#selectedsemesteryear").append('<option class="semesteryear" value=' + a[s].id + ">" + a[s].semester + "</option>")
                }).fail(function(e) {
                    Swal.fire({
                        icon: "error",
                        title: "Year does not exist for the selected Program"
                    }), $("#form")[0].reset()
                })
            }), t("#semesteryear").change(function() {
                var e = t(this).find(":selected").val(),
                    r = t("#stream").val(),
                    l = t("#programme").val(),
                    s = t("#semester").val();
                a.call([{
                    methodname: "local_batchmanagement_fetch_batchall",
                    args: {
                        selectedval: e,
                        stream: r,
                        programme: l,
                        semester: s
                    }
                }])[0].done(function(e) {
                    if (t(".batch").remove(), e.length > 0)
                        for (var a = 0; a < e.length; a++) t("#batch").append('<option class="batch" value=' + e[a].cohortid + ">" + e[a].batchname + "</option>");
                    else Swal.fire({
                        icon: "error",
                        title: "Batch does not exists for the selected Program,Stream,Semster and Year"
                    }), $("#form")[0].reset()
                }).fail(function(e) {
                    Swal.fire({
                        icon: "error",
                        title: e
                    })
                })
            }), t("#batch").change(function() {
                var e = $("#batch option:selected").text();
                $("#selectedbatch").val(e)
            }), t("#selectedsemesteryear").change(function() {
                $("#selectedsem").val()
            }), t("#formsubmit").click(function(r) {
                null == $("#programme").val() ? (Swal.fire({
                    icon: "error",
                    title: "Select Program"
                }), r.preventDefault()) : null == t("#stream").val() ? (r.preventDefault(), Swal.fire({
                    icon: "error",
                    title: "Select Stream"
                })) : null == t("#semester").val() ? (r.preventDefault(), Swal.fire({
                    icon: "error",
                    title: "Select Semester"
                })) : null == t("#semesteryear").val() ? (r.preventDefault(), Swal.fire({
                    icon: "error",
                    title: "Select Semester Year"
                })) : null == t("#batch").val() ? (r.preventDefault(), Swal.fire({
                    icon: "error",
                    title: "Enter Batch Name"
                })) : null == t("#selectedsemesteryear").val() ? (r.preventDefault(), Swal.fire({
                    icon: "error",
                    title: "Enter Batch Name"
                })) : confirm("The Migration process cannot be reverted!Are you sure want to continue!.") ? (r.preventDefault(), e = a.call([{
                    methodname: "local_batchmanagement_batch_migration",
                    args: {
                        stream: t("#stream").val(),
                        programme: t("#programme").val(),
                        semester: $("#selectedsem").val(),
                        year: t("#selectedsemesteryear").val(),
                        batch: t("#batch").val()
                    }
                }]), e[0].done(function(e) {
                    if(e==1){
                        var icon1 = 'success';
                        var txt  = 'Batch Migration is done Successfully';
                    }else{
                        var icon1 = 'error';
                        var txt  = 'Failed! Migration of this batch was done earlier.';
                    }
                    Swal.fire({
                        icon:icon1,
                        title: txt
                    }), setTimeout(function() {
                        location.reload(!0)
                    }, 2e3)
                }).fail(function(e) {
                    Swal.fire({
                        icon: "error",
                        title: "Error"
                    })
                })) : location.reload(!0)
            })
        }
    }
});