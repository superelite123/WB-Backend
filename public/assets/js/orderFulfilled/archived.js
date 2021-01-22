var s_date = windowvar.start_date;
var e_date = windowvar.end_date;

let selected_invoice = null
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
        status:5
    }
    $.ajax({
        url:'get_archived_list',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        success:(res) => {
            $('#loadingModal').modal('hide')
            convertToCSV(res.data).then(function(result){
                let filename = 'Archived Orders ' + $("#reservation").val();
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
        var str1 = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,Customer Terms,Paid,Delivered\r\n"
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
            var sub_result = ' ,Description,Qty,Units,Unit Price,Weight,Unit Label,Discount,Discount Type,Discount,Sub Total,'
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
                    newline += ' ,' + sub_array[j].e_discount;
                    newline += ' ,' + sub_array[j].base_price;
                    newline += ' ,' + sub_array[j].extended;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + sub_array[j].tax_note;

                    sub_result += newline + '\r\n';
                }
            }
            if(array[i].pDiscount != null)
            {
                sub_result += ' ,' + array[i].pDiscount.note
                sub_result += ' , , , , , ,' + array[i].pDiscount.value + '\r\n'
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
            "url":"get_archived_list",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range,status:7},
            "dataSrc": function ( json ) {
                return convert_ajax_table_data(json.data)
            }
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            let sub_total = 0;
            let discount_total = 0;
            let tax_total = 0;
            for(let i = 0; i < data.length; i ++)
            {
                let total_info = data[i].total_info
                sub_total       += parseFloat(total_info.base_price)
                discount_total  += parseFloat(total_info.discount)
                tax_total       += parseFloat(total_info.tax)
            }

            $( api.column( 2 ).footer() ).html(
                'Sub Total:' + sub_total.toFixed(2)
            );
            // Update footer
            $( api.column( 3 ).footer() ).html(
                'Discount Total:' + discount_total.toFixed(2)
            );
            $( api.column( 4 ).footer() ).html(
                'Tax Total:' + tax_total.toFixed(2)
            );
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
            { "data": "total" },
            { "data": "rSubTotal" },
            { "data": "rTax" },
            { "data": "date" },
            { "data": "btnView" },
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
    for ( var i=0, ien=json.length ; i<ien ; i++ ) {
        json[i].no = i + 1;
        json[i].chkUndeliver    = '<input type="checkbox" class="chkUndeliver" >'
        json[i].btnView         = '<a class="btn btn-info btn-xs" href="archived_view/' + json[i].id + '/0" target="_blank">'
        json[i].btnView        += '<i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a>'
        json[i].total           = json[i].total_info.adjust_price
        json[i].rSubTotal       = json[i].total_financial.rSubTotal
        json[i].rTax            = json[i].total_financial.rTax
    }
    return json
}
var row_details_format = (d) => {
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
    html += '<th>Extra Discount</th>';
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

$('#invoice_table tbody').on('click','.chkUndeliver',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    swal({
        title: "Are You Sure",
        text: "You are about to treat this Order as Delivered",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
      }, function (choice) {
          console.log(choice)
          $.ajax({
              url:'_set_status',
              data:'id=' + invoice_id + '&status=3',
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
