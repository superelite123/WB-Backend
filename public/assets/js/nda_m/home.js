var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
let logTable
let deleteID = (id) => {
    swal({
        title: 'Delete ID Photo',
        text: "You are going to delete ID Photo?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false
        },
        function () {
            $.get({
                url:'_delete_id/' + id,
                success:(res) => {
                    $.growl.notice({ message: "Deleted ID" });
                },
                error:(e) => {
                    swal(e.statusText, e.responseJSON.message, "error")
                }
            })
    })
}
let CreateLogTable = () => {
    let date_range = $("#reservation").val()
    $('#tbl_log').dataTable().fnDestroy()
    logTable = $('#tbl_log').DataTable({
        "ajax": {
            url: "get_ndaLogs",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range
            },
            dataSrc: function ( json ) {
                return json
            }
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "columns":
        [

            { "data": "no" },
            { "data": "name" },
            { "data": "email" },
            { "data": "ndaEmail" },
            { "data": "date" },
        ],
    })
}
$(() => {
    $('#tbl_nda').DataTable()
    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        CreateLogTable()
    })
    CreateLogTable()
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
