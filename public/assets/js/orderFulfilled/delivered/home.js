var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
const isAdmin = windowvar.isAdmin
let signFileUrl = windowvar.signFileUrl
let selected_invoice = null
let list_btn_template_start = ''
let list_btn_template_end = ''
list_btn_template_start += '<div class="dropdown pull-right">'
list_btn_template_start += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
list_btn_template_start += '<span class="caret"></span></button>'
list_btn_template_start += '<ul class="dropdown-menu">'
list_btn_template_end += '</ul></div>'
let total_stat = null
$("#export_btn").on('click', function(event) {
    $('#loadingModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#loadingModal').modal('show')
    let tableInfo = invoice_table.page.info()
    let post_data = {
        date_range:$('#reservation').val(),
        length:tableInfo.recordsTotal,
        start:0,
        status:4
    }
    $.ajax({
        url:'get_csv_list',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        success:(res) => {
            $('#loadingModal').modal('hide')
            convertToCSV(res.data).then(function(result){
                let filename = 'Fulfilled Orders ' + $("#reservation").val();
                exportCSVfile(filename,result);
            })
        },
        error:(e) => {
            $('#loadingModal').modal('hide')
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })

});

var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = ''
        var str1 = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,Metrc Manifest,Customer Terms,Paid,Delivered\r\n"
        let str2 = "Qty,Discount,Sub Total,Promotion Value,Less Discount,Exercise Tax,Total Due\r\n";

        for (var i = 0; i < array.length; i++) {

            var line1 = str1;
            var line2 = str2;
            let paid      = array[i].paid != null?'Paid':'No Paid'
            let delivered = array[i].delivered != null?'Delivered':'No Delivered'
            line1 += array[i].date + ',';
            line1 += array[i].clientname + ',';
            line1 += array[i].customer.address1 + ',';
            line1 += array[i].number + ',';
            line1 += array[i].customer.licensenumber + ',';
            line1 += array[i].m_m_str + ',';
            if(array[i].customer.term != null)
                line1 += array[i].customer.term.term + ','
            else
                line1 += 'No Term,'
            line1 += paid + ','
            line1 += delivered + '\r\n'
            line2 += array[i].total_info.qty + ',';
            line2 += array[i].total_info.discount + ',';
            line2 += array[i].total_info.base_price + ',';
            line2 += array[i].total_info.promotion + ',';
            line2 += (array[i].total_info.base_price - array[i].total_info.discount) + ',';
            line2 += array[i].total_info.tax + ',';
            line2 += array[i].total_info.adjust_price + '\r\n';
            var sub_array = array[i].items;
            var sub_result = ' ,Description,Qty,Units,Unit Price,Weight,Unit Label,Discount,Discount Type,Sub Total,'
            sub_result += 'Less Disocunt,Excise TAX,Line Note\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';

                    newline += ' ,' + sub_array[j].description;
                    newline += ' ,' + sub_array[j].qty;
                    newline += ' ,' + sub_array[j].units;
                    newline += ' ,' + sub_array[j].unit_price;
                    newline += ' ,' + sub_array[j].weight;
                    newline += ' ,' + sub_array[j].unit_label;
                    newline += ' ,' + sub_array[j].discount;
                    newline += ' ,' + sub_array[j].discount_label;
                    newline += ' ,' + sub_array[j].base_price;
                    newline += ' ,' + sub_array[j].extended;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + sub_array[j].tax_note;

                    sub_result += newline + '\r\n';
                }
            }

            line1 += line2;
            if(sub_result != "")
            {
                line1 += sub_result+ '\r\n';
            }
            str += line1
        }
        next_operation(str);
    });
}

var exportCSVfile = (filename,csv) =>{
    var exportedFilenmae = filename + '.csv' || 'export.csv';

    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, exportedFilenmae);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", exportedFilenmae);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}

var createTable = (date_range) => {
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"get_delivered_list",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range,status:4},
            "dataSrc": function ( json ) {
                totalStat = json.total_stat
                $('.footer-sub_total').text('$' + totalStat.sub_total)
                $('.footer-discount_total').text('$' + totalStat.discount_total)
                $('.footer-e_discount_total').text('$' + totalStat.e_discount_total)
                $('.footer-tax_total').text('$' + totalStat.tax_total)
                return convert_ajax_table_data(json.data)
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
            { "data": "number2" },
            { "data": "salesRep" },
            { "data": "clientname" },
            { "data": "lTotal" },
            { "data": "lRSubTotal" },
            { "data": "lRTax" },
            { "data": "date" },
            { "data": "delivery_time" },
            { "data": "actions" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "orderable": true, "targets": 1 },
            { "orderable": true, "targets": 2 },
            { "orderable": true, "targets": 3 },
            { "orderable": true, "targets": 4 },
            { "orderable": false, "targets": 5 },
            { "orderable": false, "targets": 6 },
            { "orderable": false, "targets": 7 },
            { "orderable": false, "targets": 8 },

        ],
        'scrollX':true
    });
}
let convert_ajax_table_data = (json) => {
    let { start } = invoice_table.page.info()
    for ( var i= 0, ien=json.length ; i<ien ; i++,start ++ ) {
        json[i].no = start + 1
        json[i].lTotal          = '$' + json[i].total
        json[i].lRSubTotal      = '$' + json[i].rSubTotal
        json[i].lRTax           = '$' + json[i].rTax
        json[i].chkUndeliver    = '<input type="checkbox" class="chkUndeliver" >'
        json[i].actions = list_btn_template_start
        json[i].actions += '<li><a href="delivered_payment_view/' + json[i].id + '" target="_blank">'
        json[i].actions += '<i class="fas fa-file-invoice-dollar">&nbsp;</i>Inv Snap</a></li>'
        json[i].actions += '<li><a href="../order_fulfilled/_download_invoice_pdf/' + json[i].id + '?name=1" target="_blank"><i class="fas fa-file-pdf"></i>&nbsp;PDF INV</a></li>'
        json[i].actions += '<li><a class="btnArchive"><i class="fas fa-file-invoice-dollar">&nbsp;</i>Archive</a></li>'
        json[i].actions += '<li><a href="payment/' + json[i].id + '" target="_blank"><i class="fas fa-dollar-sign"></i>Collect Payments</a></li>'
        json[i].actions += '<li><a href="../credit_notes/create/' + json[i].id + '" target="_blank"><i class="fas fa-dollar-sign"></i>Credit Note</a></li>'
        if(isAdmin == 1)
            json[i].actions += '<li><a href="#" class="btn_undeliver"><i class="fas fa-undo"></i>&nbsp;Undeliver</a></li>'
        json[i].actions += list_btn_template_end
    }
    return json
}
var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = [d.logs.allowed,d.logs.unallowed]
    let titles = [
        {title:'Verified Payments',message:'No Veriffied Payments'},
        {title:'Awaiting Verification',message:'No Awaiting Verification'}
    ]
    let html = ''

    data.forEach((items,k) => {
        html += '<h3>' + titles[k].title + '</h3>'
        html += '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
        html += '<thead>'
        html += '<th>No</th>'
        html += '<th>Type</th>'
        html += '<th>Cost</th>'
        html += '<th>Collected By</th>'
        html += '<th>Signature</th>'
        html += '<th>Date</th>'
        html += '</thead>'
        items.forEach((element,i) => {
            html += '<tr>'
            html += '<td>' + (i + 1) + '</td>'
            html += '<td>' + element.type + '</td>'
            html += '<td>$' + element.amount + '</td>'
            html += '<td>' + element.deliveryerName + '</td>'
            html += '<td><img class="stockimg" username="' + element.deliveryerName +
                    '" alt="" style="width:100px;height:100px" src="' + signFileUrl +
                    '/' + element.sign_filename + '"></td>'
            html += '<td>' + element.hDate + '</td>'
            html += '</tr>'
        })
        if(items.length == 0)
        {
            html += '<tr style="text-align:center"><td colspan=5>' + titles[k].message + '</td></tr>'
        }
        html += "</tbody></table>"
    })
    return html
}
$('#invoice_table tbody').on('click','.stockimg', function() {
    $('.imagepreview').attr('src', $(this).attr('src'))
    $('#signModalTitle').html($(this).attr('username') + "'s Signature")
    $('#imagemodal').modal('show')
})
//paymentButton
$('#invoice_table tbody').on('click','.btnCollectPayment', function() {
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    window.open('payment/' + invoice_id)
    window.open('delivered_payment_view/' + invoice_id)
})
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
});

$('#invoice_table tbody').on('click','.btn_undeliver',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    swal({
        title: "Are You Sure",
        text: "You are about to treat this Order as Undelivered",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
      }, function (choice) {
          console.log(choice)
          $.ajax({
              url:'_set_order_undeliver',
              data:'id=' + invoice_id,
              type:'post',
              success:(res) => {
                  swal.close()
                  createTable($("#reservation").val());
              },
              error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
              }
          })
      })
})
$('#invoice_table tbody').on('click','.btnArchive',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    swal({
        title: "Are You Sure",
        text: "You are about to Archive this Order",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
      }, function () {
          $.ajax({
              url:'_set_status',
              data:'id=' + invoice_id + '&status=7',
              type:'get',
              success:(res) => {
                  swal.close()
                  createTable($("#reservation").val());
              },
              error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
              }
          })
      })
})
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
    $('.select2').select2();
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
