$(function(){
    $("body").addClass('fixed');
    $('.select2').select2();
})

$("#rooms").change(() => {
    console.log($("#rooms").val())
    $('#harvest_table1').dataTable().fnDestroy()
    createTable1($("#rooms").val())
})

$("#btnSearch").click(function() {

    if($("#batch_id").val() == null)
    {
        swal("Warning!", "Enter the Keyword", "info")
        return
    }
    $('#harvest_table').dataTable().fnDestroy()
    createTable($("#batch_id").val())
})

$("#batch_id").keyup(function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        $("#btnSearch").click();
    }
})

let createTable = (barcode) => {
   
    harvest_table = $('#harvest_table').DataTable({
        "ajax": {
            url: "get_transfer_history_table_data",
            type: 'POST',
            "data": function ( d ) {
                
                d.barcode=barcode;
                d.type='0';
            },
            dataSrc: function ( json ) {
                
                for ( let i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1;
                    json[i].handle_user_name = json[i].handle_user.name
                    if(json[i].status == 0)
                    json[i].location_name = '<font style="color:#00acd6;font-weight:bold;font-size:120%">' + json[i].location.name + "</font>"
                    else
                        json[i].location_name = '<font style="color:#00a65a;">' + json[i].location.name + "</font>"
                    
                }
                return json;
                }
        },
        "columns": 
        [
            { "data": "no" }, 
            { "data": "barcode" }, 
            { "data": "type" }, 
            { "data": "handle_user_name" }, 
            { "data": "location_name" }, 
            { "data": "created_at" }, 
        ],
        "scrollX": true,
        'responsive':true,
        "order": [[0, 'asc']]
    });   
}

let createTable1 = (room_id) => {
   
    harvest_table = $('#harvest_table1').DataTable({
        "ajax": {
            url: "get_transfer_history_table_data",
            type: 'POST',
            "data": function ( d ) {
                
                d.room_id=room_id;
                d.type="1"
            },
            dataSrc: function ( json ) {
                
                for ( let i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1;
                }
                return json;
                }
        },
        "columns": 
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs btn-edit"><i class="fas fa-plus"></i></button>'
            },
            { "data": "no" }, 
            { "data": "barcode" }, 
            { "data": "type" }, 
            { "data": "handler" }, 
            { "data": "created_at" }, 
        ],
        "scrollX": true,
        "order": [[0, 'asc']]
    });   
}

$('#harvest_table1 tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr')
    var row = harvest_table.row( tr )

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide()
        tr.removeClass('shown')
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show()
        tr.addClass('shown')
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
    }
});
var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.child_history;
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<tr>';
    html += '<td>Room</td><td>User</td><td>Moved Date</td></tr>';

    for(let i = 0; i < data.length; i ++)
    {
        html += "<tr>"
        html += "<td>" + data[i].location.name + "</td>"
        html += "<td>" + data[i].handle_user.name + "</td>"
        html += "<td>" + data[i].created_at + "</td>"
        html += "</tr>"
    }

    html += "</table>";
    return html;
}
$(function() {
    $("#batch_id").focus()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});