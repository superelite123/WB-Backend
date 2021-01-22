var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
var harvest_table;

// This must be a hyperlink
$("#export_btn").on('click', function(event) {
    var res = harvest_table.rows().data();
    convertToCSV(res).then(function(result){
        let filename = $("#reservation").val();

        exportCSVfile(filename,result);
    })
});

var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = "id,harvest_batch_id,Total Weight,Total_Pounds,Wet Weight,Total_Count,Unit Of Weight,Flower Drying Room Location,Strain,License,Creation Date\r\n";

        for (var i = 0; i < array.length; i++) {
            var line = '';
            line += (i + 1) + ',';
            line += array[i].harvest_batch_id + ',';
            line += array[i].total_weight + ',';
            line += array[i].total_pounds_wet + ',';
            line += array[i].ant_dry_weightlbs + ',';
            line += array[i].total_count + ',';
            line += array[i].unit + ',';
            line += array[i].name + ',';
            line += array[i].strain + ',';
            line += array[i].license + ',';
            line += array[i].created_at + ',';
            str += line + '\r\n';
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

var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<tr>'
    html += '<td>Plant Tag</td>'
    html += '<td>Weight</td></tr>'

    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr style="font-size: small;">'
        html += '<td>' + data[i].plant_tag + '</td>';
        html += '<td>' + data[i].weight + '</td>'
        html += '</tr>';
    }

    html += "</table>";
    return html;
}

var createTable = (date_range) => {
   
    harvest_table = $('#harvest_table').DataTable({
        "ajax": {
            url: "get_harvest_archived_table_data",
            type: 'POST',
            "data": function ( d ) {
                
                d.date_range=date_range;
                // d.custom = $('#myInput').val();
                // etc
            },
            dataSrc: function ( json ) {
                
                for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1;
                    json[i].total_pounds_wet = json[i].total_weight * 0.00220462;
                    json[i].total_pounds_wet = json[i].total_pounds_wet.toFixed(2);
                    json[i].ant_dry_weightlbs   = json[i].total_pounds_wet / 5;
                    json[i].ant_dry_weightlbs = json[i].ant_dry_weightlbs.toFixed(2);
                    json[i].ant_dry_weighton   = json[i].ant_dry_weightlbs * 16;
                    json[i].ant_dry_weighton = json[i].ant_dry_weighton.toFixed(2);
                    json[i].ant_dry_weightgr   = json[i].ant_dry_weightlbs * 453.592;
                    json[i].ant_dry_weightgr = json[i].ant_dry_weightgr.toFixed(2);
                    json[i].total_count = json[i].items.length;
                    json[i].csv_btn = '<button class="btn btn-info btn-xs btn-edit csv_btn">CSV</button>';
                }
                return json;
                }
        },
        "columns": 
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs btn-edit"><i class="fas fa-plus"></i></button>'
            },
            { "data": "no" }, 
            { "data": "harvest_batch_id" }, 
            { "data": "total_count" }, 
            { "data": "total_weight" }, 
            { "data": "total_pounds_wet" }, 
            { "data": "ant_dry_weightlbs" }, 
            { "data": "ant_dry_weighton" }, 
            { "data": "ant_dry_weightgr" },
            { "data": "name" }, 
            { "data": "strain" }, 
            { "data": "license" },
            { "data": "unit" },
            { "data": "created_at" },
            { "data": "csv_btn" },
            ],
                "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
    
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            var weight_total = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            // Update footer
            $( api.column( 3 ).footer() ).html(
                weight_total
            );

            weight_total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            weight_total = weight_total.toFixed(2);
            // Update footer
            $( api.column( 4 ).footer() ).html(
                weight_total
            );

            weight_total = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            weight_total = weight_total.toFixed(2);
            // Update footer
            $( api.column( 5 ).footer() ).html(
                weight_total
            );

            weight_total = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            weight_total = weight_total.toFixed(2);
            // Update footer
            $( api.column( 6 ).footer() ).html(
                weight_total
            );
        
            weight_total = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            weight_total = weight_total.toFixed(2);
            // Update footer
            $( api.column( 7 ).footer() ).html(
                weight_total
            );    
                    
            weight_total = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            weight_total = weight_total.toFixed(2);
            // Update footer
            $( api.column( 8 ).footer() ).html(
                weight_total
            );   
                    
            weight_total = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 9 ).footer() ).html(
                weight_total
            );   
        },
        "scrollX": true,
        "order": [[1, 'asc']]
    });   
}

$('#harvest_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr')
    var row = harvest_table.row( tr )

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide()
        tr.removeClass('shown')
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show()
        tr.addClass('shown')
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
    }
});

$('#harvest_table tbody').on('click', '.csv_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var data = Array();
    data[0] = row.data(); 
    
    convertToCSV(data).then(function(result){
        let filename = data[0].harvest_batch_id + data[0].created_at;
        exportCSVfile(filename,result);
    });
});
$('#harvest_table tbody').on('click', '.barcode_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var harvest_id = row.data().id;
    location.href="dry?id=" + harvest_id + "&mode=new";
});
$('#harvest_table tbody').on('click', '.build_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var data = Array();
    data[0] = row.data(); 
    
    convertToCSV(data).then(function(result){
        let filename = data[0].harvest_batch_id + data[0].created_at;
        exportCSVfile(filename,result);
    });
});
$(function(){

    createTable($("#reservation").val());

    $("body").addClass('fixed');

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        $('#harvest_table').dataTable().fnDestroy()
        createTable($("#reservation").val());
      })
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});