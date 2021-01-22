var s_date = windowvar.start_date;
var s_date1 = windowvar.start_date1;
var e_date = windowvar.end_date;
let list_btn_template_start = ''
let list_btn_template_end = ''
list_btn_template_start += '<div class="dropdown pull-right">'
list_btn_template_start += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
list_btn_template_start += '<span class="caret"></span></button>'
list_btn_template_start += '<ul class="dropdown-menu">'
list_btn_template_end += '</ul></div>'
var invoice_table
let customer_table
let verification_table
$("#btn_export_invoice_hopper").on('click', function(event) {
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
        type:$('input[name="invoiceType"]:checked').val(),
    }
    $.ajax({
        url:'getInvoices',
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
$("#btn_export_invoice_maritsa").on('click', function(event) {
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
        type:$('input[name="invoiceType"]:checked').val(),
        exporting:1
    }
    $.ajax({
        url:'getInvoices',
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
        var str     = "Customer Name,Account Rep,Metrc Manifest,Invoice Number,Creation Date,Delivery Date,Terms,Total Qty Grams,Flower,Pre Roll,Concentrate,Total Invoice,Discount,"
            str    +="Net Invoice,Excise Tax,Total Due\r\n"

        for (var i = 0; i < array.length; i++) {

            var line1 = ''
            line1 += '\"' + array[i].clientname + '\",'
            line1 += '\"' + array[i].salesrep_name + '\",'
            line1 += array[i].mmstr + ','
            line1 += array[i].number.split('-')[1] == undefined?array[i].number:array[i].number.split('-')[1]
            line1 += ','
            line1 += array[i].creationDate + ','
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
            str += line1
        }
        next_operation(str);
    });
}
$("#export_customer_btn").on('click', function(event) {
    $('#loadingModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#loadingModal').modal('show')
    let tableInfo = customer_table.page.info()
    let post_data = {
        length:tableInfo.recordsTotal,
        start:0,
    }
    $.ajax({
        url:'getCustomers',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        success:(res) => {
            $('#loadingModal').modal('hide')
            convertCustomersToCSV( res.data ).then(function(result){
                let filename = 'Customers '
                exportCSVfile(filename,result);
            })
        },
        error:(e) => {
            $('#loadingModal').modal('hide')
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })

});

var convertCustomersToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str  = "DBA/Short Name,Legal Name,Address 1,Address 2,City,State,Zip,Phone,"
            str += "Fax,Email,Website,Payment Terms,Resale #,Customer Type,Cannabis Lic,City/Business Lic,EIN\r\n"


        for (var i = 0; i < array.length; i++) {

            var line1 = ''

            line1 += '\"' + array[i].clientname + '\",'
            line1 += '\"' + array[i].legalname + '\",'
            line1 += '\"' + array[i].address1 + '\",'
            line1 += '\"' + array[i].address2 + '\",'
            line1 += '\"' + array[i].city + '\",'
            line1 += '\"' + array[i].stateName + '\",'
            line1 += '\"' + array[i].zip + '\",'
            line1 += '\"' + array[i].companyphone + '\",'
            line1 += '\"' + array[i].fax + '\",'
            line1 += '\"' + array[i].companyemail + '\",'
            line1 += '\"' + array[i].website + '\",'
            line1 += '\"' + array[i].termName + '\",'
            line1 += '\"' + array[i].resale + '\",'
            line1 += '\"' + array[i].LicTypeLabel + '\",'
            line1 += '\"' + array[i].licensenumber + '\",'
            line1 += '\"' + array[i].companylic + '\",'
            line1 += '\"' + array[i].ein + '\"\r\n'
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
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

var createInvoicesTable = (date_range) => {
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"getInvoices",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range,type:$('input[name="invoiceType"]:checked').val()},
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
            $('#footer-qty_grams').text(numberWithCommas(qtyGrams.toFixed(2)))
            $('#footer-total_invoice').text(numberWithCommas('$' + basePrice.toFixed(2)))
            $('#footer-discount').text(numberWithCommas('$' + discount.toFixed(2)))
            $('#footer-net_invoice').text(numberWithCommas('$' + subTotal.toFixed(2)))
            $('#footer-e_tax').text(numberWithCommas('$' + tax.toFixed(2)))
            $('#footer-total_due').text(numberWithCommas('$' + totalDue.toFixed(2)))
        },
        "columns":
        [
            { "data": "legalname" },
            { "data": "clientname" },
            { "data": "salesrep_name" },
            { "data": "mmstr" },
            { "data": "number" },
            { "data": "creationDate" },
            { "data": "date" },
            { "data": "terms" },
            { "data": "qtyGrams" },
            { "data": "ptweight1" },
            { "data": "ptweight2" },
            { "data": "ptweight3" },
            { "data": "basePrice" },
            { "data": "discount" },
            { "data": "extended" },
            { "data": "tax" },
             { "data": "totalDue" },
             { "data": "exported_chk" },
            { "data": "actions" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "orderable": false, "targets": 1 },
            { "orderable": false, "targets": 2 },
            { "orderable": false, "targets": 3 },
            { "orderable": true, "targets": 4 },
            { "orderable": true, "targets": 5 },
            { "orderable": true, "targets": 6 },
            { "orderable": false, "targets": 7 },
            { "orderable": false, "targets": 8 },
            { "orderable": false, "targets": 9 },
            { "orderable": true, "targets": 10 },
            { "orderable": false, "targets": 11 },
            { "orderable": false, "targets": 12 },
            { "orderable": false, "targets": 13 },
            { "orderable": false, "targets": 14 },
            { "orderable": false, "targets": 15 },
            { "orderable": false, "targets": 16 },
            { "orderable": false, "targets": 17 },
            { "orderable": false, "targets": 18 },
        ],
        'scrollX':true,
        "order": [[ 4, "desc" ]]
    });
}
var createCustomerTable = (date_range) => {
    $('#customer_table').dataTable().fnDestroy()
    customer_table = $('#customer_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"getCustomers",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range,},
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
            { "data": "legalname" },
            { "data": "address1" },
            { "data": "address2" },
            { "data": "city" },
            { "data": "stateName" },
            { "data": "zip" },
            { "data": "companyphone" },
            { "data": "fax" },
            { "data": "companyemail" },
            { "data": "website" },
            { "data": "termName" },
            { "data": "resale" },
            { "data": "LicTypeLabel" },
            { "data": "licensenumber" },
            { "data": "companylic" },
            { "data": "ein" },
            { "data": "created" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "orderable": false, "targets": 1 },
            { "orderable": false, "targets": 2 },
            { "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 4 },
            { "orderable": false, "targets": 5 },
            { "orderable": false, "targets": 6 },
            { "orderable": false, "targets": 7 },
            { "orderable": false, "targets": 8 },
            { "orderable": false, "targets": 9 },
            { "orderable": false, "targets": 10 },
            { "orderable": false, "targets": 11 },
            { "orderable": false, "targets": 12 },
            { "orderable": false, "targets": 13 },
            { "orderable": false, "targets": 14 },
            { "orderable": false, "targets": 15 },
            { "orderable": false, "targets": 16 },
        ],
        'scrollX':true,
    });
}
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
        const exported      = json[i].exported == 1?'checked':''
        json[i].exported_chk = '<input type="checkbox" class="exported_chk" ' + exported + ' />'
        json[i].actions = list_btn_template_start
        json[i].actions += '<li><a href="view/' + json[i].id + '/0" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'
        json[i].actions += '<li><a href="_download_invoice_pdf/' + json[i].id + '" target="_blank"><i class="fas fa-file-pdf"></i>&nbsp;Download Invoice</a></li>'
        json[i].actions += list_btn_template_end
    }
    return json
}

$('#invoice_table tbody').on('click','.exported_chk',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    $.ajax({
        url:'_toggle_exported',
        data:'id=' + invoice_id,
        type:'post',
        success:(res) => {
            //createInvoicesTable($("#reservation").val())
        },
        error:(e) => {
        swal(e.statusText, e.responseJSON.message, "error")
        }
    })
})
$('#customer_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = customer_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>')
    }
    else {
        // Open this row
        const rowData = row.data()
        $.ajax({
            url:'_getCustomerInvoice',
            data:'id='+rowData.client_id,
            type:'post',
            success:(res) => {
                row.child( row_details_format({...rowData,...res}) ).show();
                tr.addClass('shown');
                $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
            },
            error:(e) => {
                alert("error during pull customers invoice")
            }
        })
        // row.child( row_details_format(row.data()) ).show();
        // tr.addClass('shown');
        // $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
    }
});

var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.myInvoices
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>'
    html += '<th>Number</th>'
    html += '<th>Date</th>'
    html += '<th>Sub Total</th>'
    html += '<th>Total Excise Tax</th>'
    html += '<th>Total Due</th>'
    html += '<th>Total Collected</th>'
    html += '<th>Total Collected Tax</th>'
    html += '<th>Remaining Sub Total</th>'
    html += '<th>Remaining Tax</th>'
    html += '<th></th>'
    html += '<th></th>'
    html += '</thead>'

    html += "<tbody>";
    if(data.length > 0)
    {
        for(var i = 0; i < data.length; i ++)
        {
            html += '<tr>'
            html += '<td>' + data[i].number + '</td>'
            html += '<td>' + data[i].date + '</td>'
            html += '<td>' + data[i].subTotal + '</td>'
            html += '<td>' + data[i].tax + '</td>'
            html += '<td>' + data[i].total + '</td>'
            html += '<td>' + data[i].pTotal + '</td>'
            html += '<td>' + data[i].pTax + '</td>'
            html += '<td>' + data[i].rTotal + '</td>'
            html += '<td>' + data[i].rTax + '</td>'
            html += '<td><a target="_blank" href="' + data[i].url + '" class="btn btn-info btn-xs"><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></td>';
            html += '<td><a target="_blank" href="' + data[i].download + '" class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i>&nbsp;Download Invoice</a></td>';
            html += '</tr>';
        }
        html += '<tr>'
        html += '<td></td><td></td>'
        html += '<td>' + d.sumSubTotal + '</td>'
        html += '<td>' + d.sumTax + '</td>'
        html += '<td>' + d.sumTotal + '</td>'
        html += '<td>' + d.sumPTotal + '</td>'
        html += '<td>' + d.sumPTax + '</td>'
        html += '<td>' + d.sumRTotal + '</td>'
        html += '<td>' + d.sumRTax + '</td>'
        html += '<td></td>'
        html += '<td></td>'
        html += '<tr>'
    }
    if(data.length == 0)
    {
        html += '<tr><td colspan=4 style="text-align:center">No Order</td><tr>'
    }
    html += "</tbody></table>";
    return html;
}
$(function(){

    $("body").addClass('fixed');

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        createInvoicesTable($("#reservation").val());
    })
    $("#reservation_customer").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date1,
        endDate: e_date
      }).on("change", function() {
        createCustomerTable($("#reservation_customer").val())
    })
    createInvoicesTable($("#reservation").val())
    createCustomerTable($("#reservation_customer").val())
    $('.select2').select2()
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
