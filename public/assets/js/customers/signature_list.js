var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
var invoice_table;

var createTable = (date_range) => {
    invoice_table = $('#invoice_table').DataTable({
        "ajax": {
            url: "get_table_data",
            type: 'POST',
            "data": function ( d ) {
                
                d.date_range=date_range;
                d.status=2
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
                  json[i].sign_btn = '<button class="btn btn-info btn-xs btn-edit sign_btn"><i class="fas fa-pencil"></i>SIGN</button>'
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
            { "data": "sign_btn" }, 
        ],
        "order": [[1, 'asc']]
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

$('#invoice_table tbody').on('click', '.sign_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    window.open("signature_panel?id=" + invoice_id, '_blank');
});
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
      }).on("show", function() {
        $(this).val("01.05.2012").datepicker('update');
    });

})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});