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
        var str1 = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,Customer Terms\r\n"
        let str2 = "Qty,Discount,Sub Total,Promotion Value,Less Discount,Exercise Tax,Total Du\r\n";
        
        for (var i = 0; i < array.length; i++) {
            var line = '';

            var line1 = str1;
            var line2 = str2;
            line1 += array[i].date + ',';
            line1 += array[i].clientname + ',';
            line1 += array[i].customer.address1 + ',';
            line1 += array[i].number + ',';
            line1 += array[i].customer.licensenumber + ',';
            if(array[i].customer.term != null)
                line1 += array[i].customer.term.term + '\r\n'
            else
                line1 += 'No Term\r\n'
            line2 += array[i].total_info.qty + ',';
            line2 += array[i].total_info.discount + ',';
            line2 += array[i].total_info.base_price + ',';
            line2 += array[i].total_info.promotion + ',';
            line2 += (array[i].total_info.base_price - array[i].total_info.discount) + ',';
            line2 += array[i].total_info.tax + ',';
            line2 += array[i].total_info.adjust_price + '\r\n';

            var sub_array = array[i].items;
            var sub_result = ' ,Strain,Type,Quantity,Units,Unit Price,CPU,Discount,Discount Type,Sub Total,Extended,TAX,Line Note,Adjust Price\r\n';

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
                    newline += ' ,' + sub_array[j].sub_total;
                    newline += ' ,' + sub_array[j].less_discount;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + sub_array[j].tax_note;
                    newline += ' ,' + sub_array[j].adjust_price;

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
            { "data": "shipped_via" }, 
            { "data": "date" },
            { "data": "actions" },
        ],
        "order": [[1, 'asc']],
        'responsive': true
    });
}

let process_ajax_to_table = (json) => {
    for ( var i=0, ien=json.length ; i<ien ; i++ ) {
        json[i].no = i + 1;
        json[i].shipped_via = json[i].total_info.total_debt
        json[i].actions = list_btn_template_start
        json[i].actions += '<li><a href="#" class="fulfillment_btn"><i class="fas fa-check">&nbsp;</i>FulFill</a></li>'
        json[i].actions += '<li><a href="#" class="email_btn"><i class="fas fa-envelope-square">&nbsp;</i>Email</a></li>'
        json[i].actions += '<li><a href="#" class="edit_btn"><i class="fas fa-edit">&nbsp;</i>EDIT</a></li>'
        json[i].actions += '<li><a href="#" class="view_btn"><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'
        json[i].actions += '<li><a href="#" class="print_btn"><i class="fas fa-print">&nbsp;</i>Print</a></li>'
        json[i].actions += '<li><a href="#" class="csv_btn"><i class="fas fa-file-csv"></i>&nbsp;CSV</a></li>'
        json[i].actions += '<li class="divider"></li>'
        json[i].actions += '<li><a href="#" class="delete_btn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete</a></li>'
        json[i].actions += list_btn_template_end
      }
    return json
}

var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
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
    html += '<th>Sub Total</th>';
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
        html += '<td>' + data[i].sub_total + '</td>';
        html += '<td>' + data[i].less_discount + '</td>';
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
});

$('#invoice_table tbody').on('click', '.edit_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    location.href="form?id=" + invoice_id;
});

$('#invoice_table tbody').on('click', '.view_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    window.open('pending_detail?id='+invoice_id)
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
        closeOnConfirm: true,
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
$('#invoice_table tbody').on('click', '.print_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    window.open('pending_detail?id='+invoice_id + '&print=1')
})

$body = $("body");
let validateEmail = (email) => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
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