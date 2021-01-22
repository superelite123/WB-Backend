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
            var sub_result = ' ,Description,Quantity,Unit Price,Discount,Sub Total,Less Discount,TAX,Adjust Price\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';
                    
                    newline += ' ,' + sub_array[j].description;
                    newline += ' ,' + sub_array[j].qty;
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
                d.date_range=date_range
                d.status=1
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
                  
                  json[i].fulfill_btn = '<button class="btn btn-info fulfill_btn"><i class="fas fa-edit">&nbsp;</i>FULFILL</button>'
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
            { "data": "fulfill_btn" },
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
    html += '<th>Tax</th>';
    html += '<th>Line Note</th>';
    html += '<th>Adjust Price</th>';
    html += '</thead>';
    
    html += "<tbody>";
    let less_discount = 0;
    let adjust_price = 0;
    let sub_total = 0;
    for(var i = 0; i < data.length; i ++)
    {
        sub_total = parseFloat(data[i].qty) * parseFloat(data[i].unit_price)
        less_discount = sub_total - data[i].discount
        adjust_price = less_discount + data[i].tax
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
    console.log(html);
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

$('#invoice_table tbody').on('click', '.fulfill_btn', function () {
    
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    
    location.href='fulfill_form?id=' + invoice_id
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

})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});