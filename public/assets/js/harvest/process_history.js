let s_date = windowvar.start_date;
let e_date = windowvar.end_date;
let harvest_table;
$("#export_btn").on('click', function(event) {
    let res = harvest_table.rows().data();
    convertToCSV(res).then(function(result){
        let filename = $("#reservation").val();

        exportCSVfile(filename,result);
    })
});

let convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        let array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        let str = "id,Parent Harvest_batch_id,Total Weight,Flower Drying Room Location,Strain,License,Unit Of Weight,Creation Date\r\n";

        for (let i = 0; i < array.length; i++) {
            let line = '';
            line += (i + 1) + ','
            line += array[i].harvest_batch_id + ','
            line += array[i].total_weight + ','
            line += array[i].name + ','
            line += array[i].strain + ','
            line += array[i].license + ','
            line += array[i].unit + ','
            line += array[i].updated_at + ','
        
            for(let j =0; j < array[i].items.length; j ++)
            {
                line += '\r\n'
                line += ','
                line += (j + 1) + ','
                line += array[i].items[j].weight + ','
                line += array[i].items[j].metrc + ','
            }
            str += line + '\r\n'
        }
        
        next_operation(str);
    });
}

let exportCSVfile = (filename,csv) =>{
    let exportedFilenmae = filename + '.csv' || 'export.csv';

    let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, exportedFilenmae);
    } else {
        let link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            let url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", exportedFilenmae);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}
let row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
    var html = ''
    html += '<h2>Curning Asset</h2>'
    html += '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<tr>';
    html += '<td>Weight</td><td>Metrc Tag</td></tr>';
    if(data.length != 0)
    {
        for(var i = 0; i < data.length; i ++)
        {
            html += '<tr style="font-size: small;">';
            html += '<td>' + data[i].weight + '</td>';
            html += '<td>' + data[i].metrc + '</td>';
            html += '</tr>';
        }
    }
    else
    {
        html += '<tr><td colspan=2>No Asset Data</td></tr>'
    }
    
    html += '</table>'
    data = d.waste
    html += '<h2>Wasted Data</h2>'
    html += '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<tr>';
    html += '<td>Weight</td><td>Metrc Tag</td></tr>';

    if(data.length != 0)
    {
        for(var i = 0; i < data.length; i ++)
        {
            html += '<tr style="font-size: small;">';
            html += '<td>' + data[i].weight + '</td>';
            html += '<td>' + data[i].metrc + '</td>';
            html += '</tr>';
        }
    }
    else
    {
        html += '<tr><td colspan=2>No Waste Data</td></tr>'
    }

    html += "</table>";
    return html;
}
let createTable = (date_range) => {
   
    harvest_table = $('#harvest_table').DataTable({
        "ajax": {
            url: "get_process_history_data",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range;
                // d.custom = $('#myInput').val();
                // etc
            },
            dataSrc: function ( json ) {
                
                for ( let i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1;
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
            { "data": "total_weight" }, 
            { "data": "name" }, 
            { "data": "strain" }, 
            { "data": "license" },
            { "data": "unit" },
            { "data": "updated_at" },
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
            },
        "scrollX": true,
        "order": [[0, 'asc']]
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

    $('.select2').select2();
})

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
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});