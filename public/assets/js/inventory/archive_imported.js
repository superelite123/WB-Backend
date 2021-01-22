let tblHistory = null
let tbl_inventory = $('#tbl_inventory').DataTable({
    data:harvests_inventory,
    columns:
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs flat"><i class="glyphicon glyphicon-plus"></i></button>'
            },
            { "data": "batch_id" },
            { "data": "strain_label" },
            { "data": "type" },
            { "data": "qty" },
            { "data": "date" },
            { "data": "btn_approve" },
        ],
});
$('#tbl_inventory tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = tbl_inventory.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit flat"><i class="fas fa-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit flat"><i class="fas fa-minus"></i></button>')
    }
})
var row_details_format = (d) => 
{
    // `d` is the original data object for the row
    var data = d.inventory
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>';
    html += '<th>No</th>'
    html += '<th>Metrc Tag</th>';
    html += '<th>Weight</th>';
    html += '<th>Inventory</th>';
    html += '</thead>';

    html += "<tbody>";
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].metrc_tag + '</td>';
        html += '<td>' + data[i].weight + '</td>';
        html += '<td>' + data[i].i_type_label + '</td>';
        html += '</tr>';
    }
    return html
}
$('#tbl_inventory tbody').on('click', '.btn_approve', function () {
    const tr = $(this).closest('tr')
    const row = tbl_inventory.row( tr )
    const harvest_id = row.data().id
    swal({
        title: "Confirm",
        text: "You are about to approve Inventory",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url:'_approve_imported',
            data:'id=' + harvest_id,
            type:'post',
            success:(res) => {
                swal("Successfully imported", "", "success")
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
})

var createTable = (date_range) => {
    tblHistory = $('#tbl_history').DataTable({
        "ajax": {
            url: "_approve_history",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range
            },
            dataSrc: function ( json ) {
                return json
            },
        },
        columns:
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>'
            },
            { "data": "batch_id" },
            { "data": "id" },
            { "data": "strainLabel" },
            { "data": "pTypeLabel" },
            { "data": "qty" },
            { "data": "importedDate" },
            { "data": "approveDate" },
            { "data": "importerName" },
            { "data": "approverName" },
        ],
        'responsive': true
    });
}

$('#tbl_history tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = tblHistory.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit flat"><i class="fas fa-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format2(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit flat"><i class="fas fa-minus"></i></button>')
    }
})
var row_details_format2 = (d) => 
{
    // `d` is the original data object for the row
    var data = d.items
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>';
    html += '<th>No</th>'
    html += '<th>Metrc Tag</th>';
    html += '<th>Weight</th>';
    html += '<th>Inventory</th>';
    html += '</thead>';

    html += "<tbody>";
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].metrc_tag + '</td>';
        html += '<td>' + data[i].weight + '</td>';
        html += '<td>' + data[i].i_type_label + '</td>';
        html += '</tr>';
    }
    return html
}
$("#export_btn").on('click', function(event) {

    var res = tblHistory.rows().data();

    convertToCSV(res).then(function(result){
        let filename = 'Bulk Upload History ' + $("#reservation").val();
        exportCSVfile(filename,result);
    })
});

var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        
        var str = "Harvest Batch ID,Batch Upload ID,Strain,Type,Qty,Imported Date,Approved Date,Imported By,Approved By\r\n"

        for (var i = 0; i < array.length; i++) {
            var line = '';
            line += array[i].batch_id + ',';
            line += array[i].id + ',';
            line += array[i].strainLabel + ',';
            line += array[i].pTypeLabel + ',';
            line += array[i].qty + ',';
            line += array[i].importedDate + ',';
            line += array[i].approveDate + ',';
            line += array[i].importerName + ',';
            line += array[i].approverName + '\r\n';
            var sub_array = array[i].items;
            var sub_result = ' ,Metrc Tag,Weight,Inventory\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';

                    newline += ' ,' + sub_array[j].metrc_tag;
                    newline += ' ,' + sub_array[j].weight;
                    newline += ' ,' + sub_array[j].i_type_label;

                    sub_result += newline + '\r\n';
                }
            }
            
            line += sub_result+ '\r\n';
            str += line
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
$( function() {
    $("#reservation").daterangepicker({
        format: 'mm/dd/yyyy',
        startDate: s_date,
        endDate: e_date
    }).on("change", function() {
        $('#tbl_history').dataTable().fnDestroy()
        createTable($("#reservation").val());
    })
    createTable($("#reservation").val());
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});