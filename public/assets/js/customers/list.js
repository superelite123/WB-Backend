var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
var invoice_table;

$("#export_btn").on('click', function(event) {
    
    var res = invoice_table.rows().data();
    
    convertToCSV(res).then(function(result){
        let filename = 'Invoice Data ' + $("#reservation").val();
        exportCSVfile(filename,result);
    })
});

var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){
        
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = "id,Invoice Number,Customer,Total Cost,Shipped Via,Creation Date\r\n";
        
        for (var i = 0; i < array.length; i++) {
            var line = '';

            line += (i + 1) + ',';
            line += array[i].number + ',';
            line += array[i].clientname + ',';
            line += array[i].total + ',';
            line += array[i].shipped_via + ',';
            line += array[i].date + ',';

            var sub_array = array[i].items;
            console.log(sub_array);
            var sub_result = ' ,Description,Quantity,Weight,Unit Price,Discount,Sub Total,Less Discount,TAX,Adjust Price\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';
                    
                    newline += ' ,' + sub_array[j].description;
                    newline += ' ,' + sub_array[j].qty;
                    newline += ' ,' + sub_array[j].weight;
                    newline += ' ,' + sub_array[j].unit_price;
                    newline += ' ,' + sub_array[j].discount;
                    newline += ' ,' + sub_array[j].sub_total;
                    newline += ' ,' + sub_array[j].less_discount;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + (parseFloat(sub_array[j].qty) * parseFloat(sub_array[j].unit_price));

                    sub_result += newline + '\r\n';
                }
            }

            str += line + '\r\n';
            if(sub_result != "")
            {
                str += sub_result+ '\r\n';
            }
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
            url: "get_table_data",
            type: 'POST',
            "data": function ( d ) {
                d.status=2
                d.date_range=date_range;
            },
            dataSrc: function ( json ) {
                
                for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                  json[i].no = i + 1;
                  if(json[i].shipping_method[0].carrier == null)
                  {
                    json[i].shipped_via = 'No Data'
                  }
                  else
                  {
                    json[i].shipped_via = json[i].shipping_method[0].carrier.name
                  }
                  json[i].edit_btn = '<button class="btn btn-info btn-xs btn-edit edit_btn">EDIT</button>'
                  json[i].csv_btn = '<button class="btn btn-info btn-xs btn-edit csv_btn">CSV</button>'
                  json[i].email_btn = '<button class="btn btn-info btn-xs btn-edit email_btn">Email</button>'
                  json[i].print_btn = '<button class="btn btn-info btn-xs btn-edit print_btn">Print</button>'
                  json[i].view_btn = '<button class="btn btn-info btn-xs btn-edit view_btn">View</button>'
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
            { "data": "shipped_via" }, 
            { "data": "date" },
            { "data": "edit_btn" }, 
            { "data": "csv_btn" },
            { "data": "email_btn" }, 
            { "data": "print_btn" },
            { "data": "view_btn" },
        ],
        "order": [[1, 'asc']],
        'responsive': true
    });
}

var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>';
    html += '<th>No</th>'
    html += '<th>Description</th>';
    html += '<th>Qty</th>';
    html += '<th>Unit_price</th>';
    html += '<th>Discount</th>';
    html += '<th>Sub Total</th>';
    html += '<th>Extended</th>';
    html += '<th>Tax</th>'
    html += '<th>Line Note</th>';
    html += '<th>Adjust Price</th>';
    html += '</thead>';
    console.log(data)
    html += "<tbody>";
    let less_discount = 0;
    let adjust_price = 0;
    let sub_total = 0;
    for(var i = 0; i < data.length; i ++)
    {
        sub_total = parseFloat(data[i].qty) * parseFloat(data[i].unit_price)
        less_discount = sub_total - data[i].discount
        adjust_price = less_discount + parseFloat(data[i].tax)
        let tax_note = data[i].tax_note != null?data[i].tax_note:'No Note'
        
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].description + '</td>';
        html += '<td>' + data[i].qty + '</td>';
        html += '<td>' + data[i].unit_price + '</td>';
        html += '<td>' + data[i].discount + '</td>';
        html += '<td>' + data[i].sub_total + '</td>';
        html += '<td>' + data[i].less_discount + '</td>';
        html += '<td>' + data[i].tax + '</td>';
        html += '<td>' + tax_note + '</td>';
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
    location.href="create?id=" + invoice_id;
});

$('#invoice_table tbody').on('click', '.view_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    window.open("view?id=" + invoice_id + "&print=false", '_blank');
    //location.href="view?id=" + invoice_id;
});
$('#invoice_table tbody').on('click', '.print_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    window.open("view?id=" + invoice_id + "&print=true", '_blank');
    //location.href="view?id=" + invoice_id;
});
$('#invoice_table tbody').on('click', '.email_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    
    if( !validateEmail( row.data().companyemail ) )
    {
        swal('Invalid Email Address',"Email Address you are requiring is invalid","warning")
        return
    }

    swal({
        title: row.data().companyemail,
        text: "You are going to send the Email?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        swal.close()
        waitingDialog.show('Sending Email...')
        $.ajax({
            url:'email',
            type:'post',
            data:'id=' + invoice_id,
            success:function(res){
                waitingDialog.hide();
                
                if(res == '1')
                {
                    swal("Email sent Successfully", "", "success")
                }
            },
            error:function(e){
                waitingDialog.hide()
                swal("Sending Email failed", "", "danger")
            },
        })
    });
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