var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
var invoice_table;
let list_btn_template_start = ''
let list_btn_template_end = ''
list_btn_template_start += '<div class="dropdown pull-right dropdown-menu-right">'
list_btn_template_start += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
list_btn_template_start += '<span class="caret"></span></button>'
list_btn_template_start += '<ul class="dropdown-menu">'
list_btn_template_end += '</ul></div>'
let sel_invoice_id = null
$("#export_btn").on('click', function(event) {

    var res = invoice_table.rows().data();

    convertToCSV(res).then(function(result){
        let filename = 'Pending Orders ' + $("#reservation").val();
        exportCSVfile(filename,result);
    })
});

var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = ''
        var str1 = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,Metrc Manifest,Customer Terms\r\n"
        let str2 = "Qty,Discount,Extra Discount,Sub Total,Promotion Value,Less Discount,Exercise Tax,Total Du\r\n";

        for (var i = 0; i < array.length; i++) {
            var line = '';

            var line1 = str1;
            var line2 = str2;
            line1 += array[i].date + ',';
            line1 += array[i].clientname + ',';
            line1 += array[i].customer.address1 + ',';
            line1 += array[i].number + ',';
            line1 += array[i].customer.licensenumber + ',';
            line1 += array[i].m_m_str + ',';
            if(array[i].customer.term != null)
                line1 += array[i].customer.term.term + '\r\n'
            else
                line1 += 'No Term\r\n'
            line2 += array[i].total_info.qty + ',';
            line2 += array[i].total_info.discount + ',';
            line2 += array[i].total_info.e_discount + ',';
            line2 += array[i].total_info.base_price + ',';
            line2 += array[i].total_info.promotion + ',';
            line2 += array[i].total_info.extended + ',';
            line2 += array[i].total_info.tax + ',';
            line2 += array[i].total_info.adjust_price + '\r\n';

            var sub_array = array[i].items;
            var sub_result = ' ,Strain,Type,Quantity,Units,Unit Price,CPU,Discount,Discount Type,Extra Discount,Sub Total,Extended,TAX,Line Note,Adjust Price\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';

                    newline += ' ,' + sub_array[j].description;
                    newline += ' ,' + sub_array[j].qty;
                    newline += ' ,' + sub_array[j].units;
                    newline += ' ,' + sub_array[j].unit_price;
                    newline += ' ,' + sub_array[j].cpu;
                    newline += ' ,' + sub_array[j].discount;
                    newline += ' ,' + sub_array[j].discount_label;
                    newline += ' ,' + sub_array[j].e_discount;
                    newline += ' ,' + sub_array[j].sub_total;
                    newline += ' ,' + sub_array[j].less_discount;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + sub_array[j].tax_note;
                    newline += ' ,' + sub_array[j].adjust_price;

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

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
var createTable = (date_range) => {
    invoice_table = $('#invoice_table').DataTable({
        "ajax": {
            url: "get_pending_list",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range
                d.status=0
            },
            dataSrc: function ( json ) {
                return process_ajax_to_table(json)
            }
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            let tbp = 0;
            let discount = 0
            let e_discount = 0
            let sub = 0
            let pr = 0
            let etax = 0
            let totalDue = 0
            let totalDebt = 0
            for(let i = 0; i < data.length; i ++)
            {
                tbp  += parseFloat(data[i].base_price)
                discount  += parseFloat(data[i].discount)
                e_discount  += parseFloat(data[i].e_discount)
                sub  += parseFloat(data[i].extended)
                pr += parseFloat(data[i].promotion)
                etax       += parseFloat(data[i].tax)
                totalDue  += parseFloat(data[i].adjust_price)
                totalDebt  += parseFloat(data[i].total_debt)
            }
            
            $('.footer-tbp').text('$' + numberWithCommas(tbp.toFixed(2)))
            // $( api.column(0 ).footer() ).html(
            //     '$' + numberWithCommas(tbp.toFixed(2))
            // );
            $('.footer-tbp').text('$' + numberWithCommas(tbp.toFixed(2)))
            $('.footer-discount').text('$' + numberWithCommas(discount.toFixed(2)))
            $('.footer-e_discount').text('$' + numberWithCommas(e_discount.toFixed(2)))
            $('.footer-sub').text('$' + numberWithCommas(sub.toFixed(2)))
            $('.footer-pr_value').text('$' + numberWithCommas(pr.toFixed(2)))
            $('.footer-e_tax').text('$' + numberWithCommas(etax.toFixed(2)))
            $('.footer-total_due').text('$' + numberWithCommas(totalDue.toFixed(2)))
            $('.footer-total_debt').text('$' + numberWithCommas(totalDebt.toFixed(2)))
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

            { "data": "base_price" },
            { "data": "discount" },
            { "data": "e_discount" },
            { "data": "extended" },
            { "data": "promotion" },
            { "data": "tax" },
            { "data": "adjust_price" },
            { "data": "total_debt" },
            { "data": "fTime" },

            { "data": "date" },
            { "data": "priority_html"},
            { "data": "btn_fulfillment" },
            { "data": "btn_email"},
            { "data": "btn_edit" },
            { "data": "btn_discount"},
            { "data": "btn_detail" },
            { "data": "btn_print"},
            { "data": "btn_csv" },
            { "data": "btn_delete"}
        ],
        "rowCallback": function( row, data, dataIndex){
            if(parseFloat(data.total_info.total_debt) > 0.01)
            {
                $(row).find('td:eq(11)').css('color', 'red')
                $(row).find('td:eq(11)').css('font-weight', 'bold')
            }

        },
        'responsive': true,
        'scrollX': true
    });
}

let process_ajax_to_table = (json) => {
    for ( var i=0, ien=json.length ; i<ien ; i++ ) {
        json[i].no = i + 1;

        json[i].base_price      = json[i].total_info.base_price
        json[i].discount        = json[i].total_info.discount
        json[i].e_discount      = json[i].total_info.e_discount
        json[i].extended        = json[i].total_info.extended
        json[i].promotion       = json[i].total_info.prValue
        json[i].tax             = json[i].total_info.tax
        json[i].adjust_price    = json[i].total_info.adjust_price
        json[i].total_debt      = json[i].total_info.total_debt
        json[i].fTime           = json[i].total_info.fTime

        json[i].actions = list_btn_template_start
        json[i].actions += '<li><a href="#" class="fulfillment_btn"><i class="fas fa-check">&nbsp;</i>FulFill</a></li>'
        json[i].actions += '<li><a href="#" class="email_btn"><i class="fas fa-envelope-square">&nbsp;</i>Email</a></li>'
        json[i].actions += '<li><a href="#" class="edit_btn"><i class="fas fa-edit">&nbsp;</i>EDIT</a></li>'
        json[i].actions += '<li><a class="discount_btn"><i class="fas fa-comments-dollar"></i>&nbsp;Add Discount</a></li>'
        json[i].actions += '<li><a href="pending_detail/' + json[i].id + '/0' + '" target="_blank">'
        json[i].actions += '<i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'

        json[i].actions += '<li><a href="pending_detail/' + json[i].id + '/1' + '" target="_blank">'
        json[i].actions += '<i class="fas fa-print">&nbsp;</i>Print</a></li>'

        json[i].actions += '<li><a href="#" class="csv_btn"><i class="fas fa-file-csv"></i>&nbsp;CSV</a></li>'
        json[i].actions += '<li class="divider"></li>'
        json[i].actions += '<li><a href="#" class="delete_btn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete</a></li>'
        json[i].actions += list_btn_template_end
        
        json[i].btn_fulfillment = '<a href="#" class="btn btn-info btn-xs fulfillment_btn"><i class="fas fa-check">&nbsp;</i>FulFill</a>'
        json[i].btn_email = '<a href="#" class="btn btn-info btn-xs email_btn"><i class="fas fa-envelope-square">&nbsp;</i>Email</a>'
        json[i].btn_edit = '<a href="#" class="btn btn-info btn-xs edit_btn"><i class="fas fa-edit">&nbsp;</i>EDIT</a>'
        json[i].btn_discount = '<a href="#" class="btn btn-info btn-xs discount_btn"><i class="fas fa-comments-dollar"></i>&nbsp;Add Discount</a>'
        json[i].btn_detail = '<a href="pending_detail/' + json[i].id + '/0' + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a>'
        json[i].btn_print = '<a href="pending_detail/' + json[i].id + '/1' + '" target="_blank" class="btn btn-info btn-xs"><i class="fas fa-print">&nbsp;</i>Print</a>'
        json[i].btn_csv = '<a href="#" class="btn btn-info btn-xs csv_btn"><i class="fas fa-file-csv"></i>&nbsp;CSV</a>'
        json[i].btn_delete = '<a href="#" class="btn btn-danger btn-xs delete_btn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete</a>'
        
        let priority = ''
        let selected = ''
        priority = '<select class="form-control select2" style="width: 100%;" id="priorities">'
        priorities.forEach(item => {
            selected = ''
            if(json[i].priority_id == item.id) selected = 'selected'
            priority += '<option value="' + item.id + '" ' + selected + '>'
            priority += item.name + '</option>'
        })
        priority += '</select>'
        json[i].priority_html = priority
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
    html += '<th>Units</th>';
    html += '<th>Unit_price</th>';
    html += '<th>CPU</th>';
    html += '<th>Discount</th>';
    html += '<th>Discount Type</th>';
    html += '<th>Extra Discount</th>';
    html += '<th>Sub</th>';
    html += '<th>Extended</th>';
 //   html += '<th>Tax</th>';
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
        html += '<td>' + data[i].units + '</td>';
        html += '<td>' + data[i].unit_price + '</td>';
        html += '<td>' + data[i].cpu + '</td>';
        html += '<td>' + data[i].discount + '</td>';
        html += '<td>' + data[i].discount_label + '</td>';
        html += '<td>' + data[i].e_discount + '</td>';
        html += '<td>' + data[i].sub_total + '</td>';
        html += '<td>' + data[i].less_discount + '</td>';
        html += '<td>' + data[i].tax_note + '</td>';
        html += '<td>' + data[i].adjust_price + '</td>';
        html += '</tr>';
    }
    if(d.pDiscount != null)
    {
        html += '<tr>'
        html += '<td colspan=6>' + d.pDiscount.note + '</td>'
        html += '<td colspan=6>' + d.pDiscount.value + '</td></tr>'
    }
    html += "</tbody></table>";
    data = d.customerFinacialInfo.myInvoices
    html += '<h4>Invoice Tracker</h4>'
    html += '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;background:#D3D3D3	;">';
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
            html += '<tr style="color:#E42217">'
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
        data = d.customerFinacialInfo
        html += '<tr style="color:#E42217;font-weight:bold">'
        html += '<td></td><td></td>'
        html += '<td>' + data.sumSubTotal + '</td>'
        html += '<td>' + data.sumTax + '</td>'
        html += '<td>' + data.sumTotal + '</td>'
        html += '<td>' + data.sumPTotal + '</td>'
        html += '<td>' + data.sumPTax + '</td>'
        html += '<td>' + data.sumRTotal + '</td>'
        html += '<td>' + data.sumRTax + '</td>'
        html += '<td></td>'
        html += '<td></td>'
        html += '<tr>'
    }
    if(d.customerFinacialInfo.myInvoices.length == 0)
    {
        html += '<tr><td colspan=11 style="text-align:center;font-size:16px;font-weight:bold">No Active Invoice outstanding</td><tr>'
    }
    html += "</tbody></table>";
    data = d.pendingOrders
    html += '<h4>Pending Orders</h4>'
    html += '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" '
    html += 'style="padding-left:50px;background:#F5F5F5;">';
    html += '<thead>'
    html += '<th>Number</th>'
    html += '<th>Date</th>'
    html += '<th>TBP</th>'
    html += '<th>Discount</th>'
    html += '<th>Sub</th>'
    html += '<th>PR-Value</th>'
    html += '<th>ETax</th>'
    html += '<th>Total Due</th>'
    html += '<th>Total Debt</th>'
    html += '</thead>'

    html += "<tbody>";
    let sumTbp = 0,sumDiscount = 0,sumSub = 0,sumPr = 0,sumTax = 0,sumDue = 0,sumDebt = 0
    data.forEach(element => {
        html += '<tr>'
        html += '<td>' + element.number + '</td>'
        html += '<td>' + element.date + '</td>'
        html += '<td>' + element.total_info.base_price + '</td>'
        html += '<td>' + element.total_info.discount + '</td>'
        html += '<td>' + element.total_info.extended + '</td>'
        html += '<td>' + element.total_info.prValue + '</td>'
        html += '<td>' + element.total_info.tax + '</td>'
        html += '<td>' + element.total_info.adjust_price + '</td>'
        html += '<td>' + element.total_info.total_debt + '</td>'
        html += '</tr>'

        sumTbp      += parseFloat(element.total_info.base_price)
        sumDiscount += parseFloat(element.total_info.discount)
        sumSub      += parseFloat(element.total_info.extended)
        sumPr       += parseFloat(element.total_info.prValue)
        sumTax      += parseFloat(element.total_info.tax)
        sumDue      += parseFloat(element.total_info.adjust_price)
        sumDebt     += parseFloat(element.total_info.total_debt)
    })
    sumTbp      = sumTbp.toFixed(2)
    sumDiscount = sumDiscount.toFixed(2)
    sumSub      = sumSub.toFixed(2)
    sumPr       = sumPr.toFixed(2)
    sumTax      = sumTax.toFixed(2)
    sumDue      = sumDue.toFixed(2)
    sumDebt     = sumDebt.toFixed(2)
    if(data.length > 0)
    {
        html += '<tfoot style="background-color:#e8e8e8">'
        html += '<th></th>'
        html += '<th></th>'
        html += '<th>' + sumTbp + '</th>'
        html += '<th>' + sumDiscount + '</th>'
        html += '<th>' + sumSub + '</th>'
        html += '<th>' + sumPr + '</th>'
        html += '<th>' + sumTax + '</th>'
        html += '<th>' + sumDue + '</th>'
        html += '<th>' + sumDebt + '</th>'
        html += '</tfoot>'
    }

    if(data.length == 0)
    {
        html += '<tr><td colspan=9 style="text-align:center;font-size:16px;font-weight:bold">No Pending Order.</td><tr>'
    }
    html += "</tbody></table>";
    return html;
}
$('#invoice_table tbody').on('change','.select2',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    let pId = $(this).val()
    $.ajax({
        url:'_set_priority',
        data:'id='+invoice_id + '&priority='+pId,
        type:'post',
        async:false,
        success:(res) => {
            // $('#invoice_table').dataTable().fnDestroy()
            // createTable($("#reservation").val());
        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
            return false
        }
    })
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
        const rowData = row.data()
        $.ajax({
            url:'_getPOrderCustomerDetail',
            data:'id='+rowData.id,
            type:'post',
            success:(res) => {
                row.child( row_details_format({...rowData,...res}) ).show();
                tr.addClass('shown');
                $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
            },
            error:(e) => {

            }
        })
    }
});

$('#invoice_table tbody').on('click', '.edit_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    location.href="form?id=" + invoice_id;
});
$('#invoice_table tbody').on('click', '.discount_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    sel_invoice_id = row.data().id
    $('#modal-discount').modal('show')
})
$('#btnAddDiscount').click(() => {
    let note = $('#discount_note').val()
    let amount = parseFloat($('#discount_amount').val())
    //$.growl({ title: "Restocked One Inventory", message: "You restocked one Inventory in this Order<br>You can restore that by refresh" });
    if(note == '')
    {
        $.growl.error({ message: "You need to enter reason for this discount" })
        return false
    }
    if(amount <= 0)
    {
        $.growl.error({ message: "Enter Correct Price" })
        return false
    }
    //Save Discount
    swal({
        title: "Are You Sure",
        text: "Are You going to add this Discount?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
      }, function () {
        $.post({
            url:'_add_discount',
            data:'id='+sel_invoice_id+'&amount='+amount+'&note='+note,
            success:() => {
                $('#modal-discount').modal('hide')
                $.growl.notice({ message: "Success on adding discount" });
            },
            error:() => {
                $.growl.error({ message: "Failed on adding discount" })
            }
        })
      })
})

$('#invoice_table tbody').on('click', '.fulfillment_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    swal({
        title: "Are You Sure",
        text: "Are You going to fulfill this order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        $.ajax({
            url:'_send_pending_fulfillment',
            data:'id=' + invoice_id,
            type:'post',
            success:function(res){
                if(res == 1)
                {
                    swal('Success', 'One Order is Sent to Fulfillment', "success")
                    // $('#invoice_table').dataTable().fnDestroy()
                    // createTable($("#reservation").val());
                    location.href='fulfillment_list'
                }
                else
                {
                    swal('Warning', 'This Order does not exist', "warning")
                }
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
});
$('#invoice_table tbody').on('click', '.email_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );

    var invoice_id = row.data().id;
    let customer = row.data().customer
    let customer_email = customer != null?customer.companyemail:''
    if(customer_email == '')
    {
        swal("This Customer doesn't have Company Email", "", "warning")
        return false
    }
    swal({
        title: customer_email,
        text: "You are going to send the Email?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        $.ajax({
            url:'_pending_email',
            type:'post',
            data:'id=' + invoice_id,
            success:function(res){

                if(res == '1')
                {
                    swal("Email sent Successfully", "", "success")
                }
            },
            error:function(e){
                swal("Sending Email failed", "", "warning")
            },
        })
    });
});
$('#invoice_table tbody').on('click', '.delete_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id

    swal({
        title: "Are You Sure",
        text: "You about to remove this Order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false
      }, function () {
        $.get({
            url:'_remove_order',
            data:'id=' + invoice_id + '&type=2',
            async:false,
            success:function(res)
            {
                $('#invoice_table').dataTable().fnDestroy()
                createTable($("#reservation").val());
            }
        })
      }
    )
})

$('#invoice_table tbody').on('click', '.csv_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var data = Array();
    data[0] = row.data();

    convertToCSV(data).then(function(result){
        let filename = data[0].number + data[0].date;
        exportCSVfile(filename,result);
    });
})
$body = $("body");
let validateEmail = (email) => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
$(function(){

    createTable($("#reservation").val());
    $('#invoice_table').DataTable().draw(false);
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
