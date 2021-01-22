var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
let deliveries = windowvar.deliveries
let delivered_status = windowvar.delivered_status
var invoice_table;
let sel_order_id = -1;
var createTable = (date_range) => {
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"get_signature_list",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range,status:3},
            "dataSrc": function ( json ) {
                json = json.data
                let { start } = invoice_table.page.info()
                for ( var i=0, ien=json.length ; i<ien ; i++, start ++ ) {
                    json[i].no = start + 1;
                    json[i].sign_delivery_btn = '<a href="panel/' + json[i].id + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-signature"></i>&nbsp;Sign&nbsp;Delivery</a>'
                    json[i].sign_payment_btn = '<a href="payment_panel/' + json[i].id + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-comment-dollar"></i>&nbsp;Sign&nbsp;Payment</a>'
                    json[i].note_btn = '<button class="btn btn-info btn-xs note_btn"><i class="fas fa-pen"></i>&nbsp;Note</button>'
                    json[i].email_btn = '<button class="btn btn-info btn-xs email_btn"><i class="fas fa-envelope-square">&nbsp;</i>Email</button>'
                    json[i].pdf_btn = '<a href="../order_fulfilled/_download_invoice_pdf/' + json[i].id + '?name=2" target="_blank"><i class="fas fa-file-pdf"></i>&nbsp;PDF INV</a>'
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
                "defaultContent": '<button class="btn btn-info btn-xs"><i class="fas fa-plus"></i></button>'
            },
            { "data": "no" },
            { "data": "number" },
            { "data": "number2" },
            { "data": "salesRep" },
            { "data": "clientname" },
            { "data": "companyname" },
            { "data": "total" },
            { "data": "rSub" },
            { "data": "rTax" },
            { "data": "date" },
            { "data": "sign_delivery_btn" },
            { "data": "note_btn" },
            { "data": "email_btn" },
            { "data": "pdf_btn" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "orderable": false, "targets": 1 },
            { "orderable": true, "targets": 2 },
            { "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 4 },
            { "orderable": true, "targets": 5 },
            { "orderable": false, "targets": 6 },
            { "orderable": false, "targets": 7 },
            { "orderable": false, "targets": 8 },
        ],
        'scrollX':true
    });
}

$('#invoice_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs"><i class="fas fa-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs"><i class="fas fa-minus"></i></button>')
    }
})
$('#invoice_table tbody').on('click', '.note_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    sel_order_id = row.data().id
    $('#deliver_note').val(row.data().deliver_note)
    $('#modal_deliver_note').modal('show')
})
$('#invoice_table tbody').on('click', '.email_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    const id = row.data().id
    const emailAddress = row.data().salesEmail
    let retailerEmail = row.data().customer != null?row.data().customer.secondaryc_email:''
    retailerEmail = retailerEmail == null?'No Retailer':retailerEmail
    swal({
        title: emailAddress + '\n' + retailerEmail,
        text: "You are about to send Email",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url:'_send_sales_email',
            data:'id=' + id,
            type:'post',
            success:(res) => {
                swal("Email sent Successfully", "", "success")
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
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
})

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
    html += '<th>Extra Discount</th>'
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
        html += '<td>' + data[i].e_discount + '</td>';
        html += '<td>' + data[i].base_price + '</td>';
        html += '<td>' + data[i].extended + '</td>';
        html += '<td>' + data[i].tax_note + '</td>';
        html += '<td>' + data[i].adjust_price + '</td>';
        html += '</tr>';
    }
    if(d.pDiscount != null)
    {
        html += '<tr>'
        html += '<td colspan=5>' + d.pDiscount.note + '</td>'
        html += '<td colspan=6>' + d.pDiscount.value + '</td></tr>'
    }
    html += "</tbody></table>";
    return html;
}

$(function(){

    $("body").addClass('fixed')

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        createTable($("#reservation").val());
    })
    createTable($("#reservation").val());
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
