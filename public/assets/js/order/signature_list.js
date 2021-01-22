var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
var invoice_table;
let sel_order_id = -1;
var createTable = (date_range) => {
    invoice_table = $('#invoice_table').DataTable({
        "ajax": {
            url: "get_sign_list",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range;
                d.status=2
            },
            dataSrc: function ( json ) {
                
                for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                  json[i].no = i + 1;
                  json[i].sign_btn = '<button class="btn btn-info btn-xs btn-edit sign_btn"><i class="fas fa-pencil"></i>SIGN</button>'
                  json[i].note_btn = '<button class="btn btn-info btn-xs btn-edit note_btn"><i class="fas fa-pen"></i>Note</button>'
                  json[i].total = json[i].total_info.adjust_price
                }
                return json
              }
        },
        "columns": 
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>'
            },
            { "data": "no" }, 
            { "data": "number" }, 
            { "data": "clientname" }, 
            { "data": "companyname" },
            { "data": "total" }, 
            { "data": "date" },
            { "data": "sign_btn" }, 
            { "data": "note_btn" }, 
        ],
        "order": [[1, 'asc']]
    });
}

var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>'
    html += '<th>No</th>'
    html += '<th>Description</th>'
    html += '<th>Qty</th>'
    html += '<th>Units</th>'
    html += '<th>Unit_price</th>'
    html += '<th>Discount</th>'
    html += '<th>Discount Type</th>'
    html += '<th>Sub Total</th>'
    html += '<th>Extended</th>'
    html += '<th>Line Note</th>'
    html += '<th>Adjust Price</th>'
    html += '</thead>'
    
    html += "<tbody>"
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].description + '</td>';
        html += '<td>' + data[i].qty + '</td>';
        html += '<td>' + data[i].units + '</td>';
        html += '<td>' + data[i].unit_price + '</td>';
        html += '<td>' + data[i].discount + '</td>';
        html += '<td>' + data[i].discount_label + '</td>';
        html += '<td>' + data[i].base_price + '</td>';
        html += '<td>' + data[i].extended + '</td>';
        html += '<td>' + data[i].tax_note + '</td>';
        html += '<td>' + data[i].adjust_price + '</td>';
        html += '</tr>';
    }

    html += "</tbody></table>";
    return html;
}

$('#invoice_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
    }
})

$('#invoice_table tbody').on('click', '.sign_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    window.open("signature_panel?id=" + invoice_id, '_blank');
})

$('#invoice_table tbody').on('click', '.note_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    sel_order_id = row.data().id
    $('#deliver_note').val(row.data().deliver_note)
    $('#modal_deliver_note').modal('show')
})
$('#save_deliver_note').click(function () {
    $('#deliver_note').val()
    $('#modal_deliver_note').modal('hide')
    let post_data = {
        id:sel_order_id,
        note:$('#deliver_note').val()
    }
    $.ajax({
        url:'_save_deliver_note',
        headers:{"content-type" : "application/json"},
        type:'post',
        data: JSON.stringify(post_data),
        async:false,
        success:(res) => {
            alert('Delivery Note is added')
            $('#invoice_table').dataTable().fnDestroy()
            createTable($("#reservation").val());
        },
        error:(e) => {

        }
    })
});
$(function(){

    createTable($("#reservation").val());

    $("body").addClass('fixed');

    $("#filter").click(function(){
        $('#invoice_table').dataTable().fnDestroy()
        createTable($("#reservation").val());
    })

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        $('#invoice_table').dataTable().fnDestroy()
        createTable($("#reservation").val());
    })

})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});