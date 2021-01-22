let s_date = windowvar.start_date
let e_date = windowvar.end_date
let harvest_table

// This must be a hyperlink
$("#export_btn").on('click', function(event) {
    let res = harvest_table.rows().data();
    convertToCSV(res).then(function(result){
        let filename = $("#reservation").val()
        exportCSVfile(filename,result);
    })
});

let convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        let array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        let str = "id,Parent Harvest_batch_id,Total Weight,Remain Weight,Flower Drying Room Location,Strain,License,Unit Of Weight,Creation Date\r\n";

        for (let i = 0; i < array.length; i++) {
            let line = '';
            line += (i + 1) + ',';
            line += array[i].harvest_batch_id + ','
            line += array[i].total_weight + ','
            line += array[i].remain_weight + ','
            line += array[i].name + ','
            line += array[i].strain + ','
            line += array[i].license + ','
            line += array[i].unit + ','
            line += array[i].created_at + ','
            str += line + '\r\n';
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

let createTable = (date_range) => {
   
    harvest_table = $('#harvest_table').DataTable({
        "ajax": {
            url: "get_harvest_dry_table_data",
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
                    json[i].build_btn = '<button class="btn btn-info btn-xs btn-edit dry_btn">Process Dry</button>';
                }
                return json;
                }
        },
        "columns": 
        [
            
            { "data": "no" }, 
            { "data": "harvest_batch_id" }, 
            { "data": "total_weight" }, 
            { "data": "remain_weight" }, 
            { "data": "name" }, 
            { "data": "strain" }, 
            { "data": "license" },
            { "data": "unit" },
            { "data": "created_at" },
            { "data": "csv_btn" },
            { "data": "build_btn" },
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
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                }, 0 );
                // Update footer
                $( api.column( 2 ).footer() ).html(
                    weight_total
                );
            },
        "scrollX": true,
        "order": [[0, 'asc']]
    });   
}

let printBarcode = (res) => {
    new Promise((fulfill) => {
        $('#print_barcode_panel').html(res)
        fulfill()
    }).then(() => {
        let _style = '' +
        '<style type="text/css">' +
        'div,img {' +
        'width:100px;' +
        'height:50px;' +
        '}' +
        '</style>'
        newWin= window.open("")
        newWin.document.write(_style + $('#print_barcode_panel').html())
        newWin.print()
        newWin.close()
    })
}

$("#close_alert").click(function(){
    panel_error_message.hide();
});



$('#harvest_table tbody').on('click', '.csv_btn', function () {
    let tr = $(this).closest('tr');
    let row = harvest_table.row( tr );
    let data = Array();
    data[0] = row.data(); 
    
    convertToCSV(data).then(function(result){
        let filename = data[0].harvest_batch_id + data[0].created_at;
        exportCSVfile(filename,result);
    });
})

$('#harvest_table tbody').on('click', '.dry_btn', function () {
    let tr = $(this).closest('tr');
    let row = harvest_table.row( tr );
    let harvest = row.data();
    id = harvest.id
    // remain_weight = parseFloat(harvest.remain_weight)
    // initDialog()
    // $("#um_batch").html(harvest.unit)
    // $("#modal-build").modal('show')
    location.href="dry?id=" + id
})

//--------------------------/modal-build-------------------------------------
let id
let remain_weight = 0

let msg_invalid_weight = "weight must be greater than 0"
let msg_big_weight = "weight used must be less than Weightremain"

let input_weight = $("#w_remain")

let error_message = $("#error_message")
let panel_error_message = $("#invalid_value")

$("#close_alert").click(function(){
    panel_error_message.hide();
});
let initDialog = () => {
    input_weight.val(0)
    panel_error_message.hide()
    $("#weight_remain").html(remain_weight)
}
$('.saveBtn').click(function(){

    let w_remain = input_weight.val();

    if(w_remain <= 0)
    {
        error_message.text(msg_invalid_weight);
        panel_error_message.show();
        return;
    }

    if(w_remain > remain_weight)
    {
        error_message.text(msg_big_weight);
        panel_error_message.show();
        return;
    }

    if(w_remain == remain_weight)
    {
        if(!confirm('This Dry will archived ok?'))
        return;
    }

    if(confirm('Do you really add this dry item?'))
    {

        $.ajax({
            url:'_dry_build_one',
            data:'id=' + id + "&w=" + w_remain,
            type:'post',
            async:false,
            success:function(res)
            {
                if(res != -1)
                {
                    remain_weight = parseFloat(res)
                    initDialog()
                    $("#modal-build").modal('toggle')
                    $('#harvest_table').dataTable().fnDestroy()
                    createTable()
                }
                else{
                    alert('Incorrect Input Weight')
                }
            }
        });
    }
});
$("#modal-build").on("hidden.bs.modal", function () {
    $('#harvest_table').dataTable().fnDestroy()
    createTable()
});
//--------------------------./modal-build------------------------------------


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
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});