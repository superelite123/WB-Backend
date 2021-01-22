let reject_table = null
let action_html_s = ''
let action_html_e = ''
action_html_s += '<div class="dropdown pull-right">'
action_html_s += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
action_html_s += '<span class="caret"></span></button>'
action_html_s += '<ul class="dropdown-menu">'
action_html_e += '</ul></div>'
var createRejectTable = (date_range = null) => {
    $('#reject_table').dataTable().fnDestroy()
    reject_table = $('#reject_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"get_reject_list",
            "dataType":"json",
            "type":"POST",
            "data": {date_range:null},
            "dataSrc": function ( json ) {
                json = json.data
                for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1
                    json[i].total = '$' + json[i].total;
                    json[i].actions = action_html_s
                    json[i].actions += '<li><a href="view/' + json[i].id + '/0" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'
                    json[i].actions += '<li><a href="edit/' + json[i].id + '"><i class="fas fa-edit"></i>&nbsp;Edit Items</a></li>'
                    json[i].actions += '<li><a href="#" class="sendF"><i class="fas fa-edit"></i>&nbsp;Walnut to Deliver</a></li>'
                    json[i].actions += '<li><a href="#" class="restock"><i class="fas fa-reply"></i>&nbsp;Restock Entire Order</a></li>'
                    json[i].actions += '<li class="divider"></li>'
                    json[i].actions += '<li><a href="delete/' + json[i].id + '"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete</a></li>'
                    json[i].actions += action_html_e
                }
                return json
            }
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
            { "data": "total" },
            { "data": "date" },
            { "data": "rType" },
            { "data": "cPName" },
            { "data": "actions" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "orderable": false, "targets": 1 },
            { "orderable": true, "targets": 2 },
            { "orderable": true, "targets": 3 },
            { "orderable": true, "targets": 4 },
        ],
        'scrollX':true
    });
}
$('#reject_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = reject_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format1(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
    }
})
/**
 * Remove Order and send all items to inventory on hold
 */
$('#reject_table tbody').on('click','.restock',function () {
    var tr = $(this).closest('tr')
    var row = reject_table.row( tr )
    var invoice_id = row.data().id
    swal({
        title: "Notice",
        text: "Entire Order will be sent to Return Path Verification",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        $.ajax({
            url:'_restock_order/' + invoice_id,
            type:'get',
            success:(res) => {
                createRejectTable()
                createTable($("#reservation").val())
                swal.close()
                $.growl.notice({ message: "Restore one rejected Order" });
            },
            error:(e) => {

            }
        })
    })
})
$('#reject_table tbody').on('click','.sendF',function () {
    var tr = $(this).closest('tr')
    var row = reject_table.row( tr )
    var invoice_id = row.data().id
    swal({
        title: "Are You Sure?",
        text: "You will now send order back to Sig+Delivery Page",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        $.ajax({
            url:'_set_status',
            data:'id=' + invoice_id + '&status=3',
            type:'get',
            success:(res) => {
                createRejectTable()
                createTable($("#reservation").val())
                swal.close()
                $.growl.notice({ message: "Restore one rejected Order" });
            },
            error:(e) => {

            }
        })
    })
})
var row_details_format1 = (d) => {
    // `d` is the original data object for the row
    var data = d.items
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>';
    html += '<th>No</th>'
    html += '<th>Description</th>';
    html += '<th>Qty</th>';
    html += '<th>Weight</th>';
    html += '<th>Units</th>';
    html += '<th>Unit_price</th>';
    html += '<th>CPU</th>';
    html += '<th>Discount</th>';
    html += '<th>Discount Type</th>';
    html += '<th>Sub Total</th>';
    html += '<th>Extended</th>';
    html += '<th>Line Note</th>';
    html += '<th>Adjust Price</th>';
    html += '</thead>';

    html += "<tbody>";
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].description + '</td>';
        html += '<td>' + data[i].qty + '</td>';
        html += '<td>' + data[i].weight + '</td>';
        html += '<td>' + data[i].units + '</td>';
        html += '<td>' + data[i].unit_price + '</td>';
        html += '<td>' + data[i].cpu + '</td>';
        html += '<td>' + data[i].discount + '</td>';
        html += '<td>' + data[i].discount_label + '</td>';
        html += '<td>' + data[i].base_price + '</td>';
        html += '<td>' + data[i].extended + '</td>';
        html += '<td>' + data[i].tax_note + '</td>';
        html += '<td>' + data[i].adjust_price + '</td>';
        html += '</tr>';
    }
    if(d.pDiscount != null)
    {
        html += '<tr>'
        html += '<td colspan=7>' + d.pDiscount.note + '</td>'
        html += '<td colspan=6>' + d.pDiscount.value + '</td>'
        html += '<td colspan=6></td>'
    }
    html += "</tbody></table>";
    return html;
}
