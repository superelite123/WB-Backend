var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
var invoice_table;

let list_btn_template_start = ''
let list_btn_template_end = ''
list_btn_template_start += '<div class="dropdown pull-right">'
list_btn_template_start += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
list_btn_template_start += '<span class="caret"></span></button>'
list_btn_template_start += '<ul class="dropdown-menu">'
list_btn_template_end += '</ul></div>'

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
        l_type:$('#l_type').val()
    }
    $.ajax({
        url:'i_report/get_list',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        success:(res) => {
            console.log(res)
            $('#loadingModal').modal('hide')
            convertInvoicesToCSV( convert_ajax_table_data(res.data)).then(function(result){
                let filename = 'Invoices ' + $("#reservation").val();
                exportCSVfile(filename,result);
            })
        },
        error:(e) => {
            $('#loadingModal').modal('hide')
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })

});
$('input[name="invoiceType"]').click(() => {
    createInvoicesTable($("#reservation").val())
})
var convertInvoicesToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str     = "Customer Name,Invoice Number,Invoice Date,Terms,Total Qty Grams,Flower,Pre Roll,Concentrate,Total Invoice,Discount,"
            str    +="Net Invoice,Excise Tax,Total Due\r\n"

        for (var i = 0; i < array.length; i++) {

            var line1 = ''

            line1 += '\"' + array[i].clientname + '\",'
            line1 += array[i].number.split('-')[1] + ','
            line1 += array[i].date + ','
            line1 += array[i].terms + ','
            line1 += array[i].qtyGrams + ','
            line1 += array[i].ptweight1 + ','
            line1 += array[i].ptweight2 + ','
            line1 += array[i].ptweight3 + ','
            line1 += array[i].basePrice + ','
            line1 += array[i].discount + ','
     //       line1 += array[i].totalDue + ','
            line1 += array[i].extended + ','
            line1 += array[i].tax + ','
     //       line1 += array[i].basePrice + '\r\n'
            line1 += array[i].totalDue + '\r\n'
            var sub_array = array[i].items;
            var sub_result = ' ,Description,Qty,Units,Unit Price,Weight,Unit Label,Discount,Discount Type,Extra Discount,Sub Total,'
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

var createTable = () => {
    const date_range = $("#reservation").val()
    const l_type = $('#l_type').val()
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({

        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"i_report/get_list",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range,l_type:l_type},
            "dataSrc": function ( json ) {
                return convert_ajax_table_data(json.data)
            }
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            let qtyGrams = 0;
            let subTotal = 0;
            let tax = 0;
            let discount = 0
            let basePrice = 0
            let totalDue = 0
            for(let i = 0; i < data.length; i ++)
            {
                qtyGrams  += parseFloat(data[i].qtyGrams)
                subTotal  += parseFloat(data[i].extended)
                discount  += parseFloat(data[i].discount)
                basePrice += parseFloat(data[i].basePrice)
                tax       += parseFloat(data[i].tax)
                totalDue  += parseFloat(data[i].totalDue)
            }
            $( api.column( 4 ).footer() ).html(
                'QtyGrams:<br>' + qtyGrams.toFixed(2)
            );
            $( api.column( 5 ).footer() ).html(
                'Total Invoice:<br>$' + basePrice.toFixed(2)
            );
            $( api.column( 6 ).footer() ).html(
                'Discount:<br>$' + discount.toFixed(2)
            );
            $( api.column( 7 ).footer() ).html(
                'Net Invoice:<br>$' + subTotal.toFixed(2)
            );
            $( api.column( 8 ).footer() ).html(
                'Excise Tax:<br>$' + tax.toFixed(2)
            );
            $( api.column( 9 ).footer() ).html(
                'Total Due:<br>$' + totalDue.toFixed(2)
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
            { "data": "clientname" },
            { "data": "number" },
            { "data": "date" },
            { "data": "terms" },
            { "data": "qtyGrams" },
            { "data": "ptweight1" },
            { "data": "ptweight2" },
            { "data": "ptweight3" },
            { "data": "basePrice" },
            { "data": "discount" },
      //      { "data": "totalDue" },
            { "data": "extended" },
            { "data": "tax" },
      //      { "data": "basePrice" },
             { "data": "totalDue" },
            { "data": "actions" },
        ],
        'scrollX':true
    });

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
        html += '<td colspan=7>' + d.pDiscount.note + '</td>'
        html += '<td colspan=6>' + d.pDiscount.value + '</td>'
        html += '<td colspan=6></td>'
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
let convert_ajax_table_data = (json) => {
    for ( var i=0, ien=json.length ; i<ien ; i++ ) {
        json[i].terms       = json[i].total_info.term
        json[i].qtyGrams    = json[i].total_info.weight
        /**
         * Ptweight
         * 4.9
         */
        json[i].ptweight1   = json[i].total_info.ptweight.pt1
        json[i].ptweight2   = json[i].total_info.ptweight.pt2
        json[i].ptweight3   = json[i].total_info.ptweight.pt3
        /** */
        json[i].basePrice   = json[i].total_info.base_price
        json[i].extended    = json[i].total_info.extended
        json[i].discount    = json[i].total_info.discount
        json[i].tax         = json[i].total_info.tax
        json[i].totalDue    = json[i].total_info.adjust_price

        json[i].actions = list_btn_template_start
        json[i].actions += '<li><a href="view/' + json[i].id + '/0" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'
        json[i].actions += '<li><a href="_download_invoice_pdf/' + json[i].id + '" target="_blank"><i class="fas fa-file-pdf"></i>&nbsp;Download Invoice</a></li>'
        json[i].actions += list_btn_template_end
    }
    return json
}
$('#l_type').change(() => {
    createTable()
})
$(function(){

    $('.select2').select2();
    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        createTable();
    })
    createTable();
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
