let s_date = windowvar.start_date
let e_date = windowvar.end_date
let harvest_table
let waste_id = null
// This must be a hyperlink
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
        let str = "id,harvest_batch_id,Total Weight,Flower Drying Room Location,Strain,License,Unit Of Weight,Creation Date\r\n";

        for (let i = 0; i < array.length; i++) {
            let line = '';
            line += (i + 1) + ',';
            line += array[i].harvest_batch_id + ','
            line += array[i].total_weight + ','
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
            url: "get_curning_table_data",
            type: 'POST',
            "data": function ( d ) {
                
                d.date_range=date_range;
                // d.custom = $('#myInput').val();
                // etc
            },
            dataSrc: function ( json ) {
                
                for( let i=0, ien=json.length; i < ien; i++ ) {
                    json[i].no          = i + 1
                    json[i].csv_btn     = '<button class="btn btn-info btn-xs btn-edit csv_btn">CSV</button>'
                    json[i].barcode_btn = '<button class="btn btn-info btn-xs btn-edit barcode_btn">Print Barcode</button>'
                    json[i].build_btn   = '<button class="btn btn-info btn-xs btn-edit build_btn">Process Package</button>'
                    json[i].waist_btn   = '<button class="btn btn-danger btn-xs btn-edit waist_btn">Waste</button>'
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
            { "data": "barcode_btn" },
            { "data": "build_btn" },
            { "data": "waist_btn" },
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

                weight_total = api
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

let printBarcode = (res) => {
    new Promise((fulfill) => {
        $('#print_barcode_panel').html(res)
        fulfill()
    }).then(() => {
        let _style = '' +
        '<style type="text/css">' +
        'div,img {' +
        'width:250px;' +
        'height:30px;' +
        'text-align:center;' +
        'text-weight:bold;' +
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
$('#harvest_table tbody').on('click', '.waist_btn', function () {
    $('#modal_waste').modal('show')

    let tr = $(this).closest('tr')
    let row = harvest_table.row( tr )
    let data = Array()
    data = row.data()
    waste_id = data.id
    $("#waste_weight").html(data.remain_weight)
})
$('#deduct_waist').click(function () {
    let metrc = $('#waste_metrc').val()
    if(metrc == '')
    {
        alert('You need to enter the metrc')
    }

    $.ajax({
        url:'_throw_curing',
        data:'id=' + waste_id + '&metrc=' + metrc,
        type:'post',
        async:false,
        success:(res) => {
            $('#modal_waste').modal('hide')
            $('#harvest_table').dataTable().fnDestroy()
            createTable($("#reservation").val())
        }
    })
})

$('#harvest_table tbody').on('click', '.barcode_btn', function () {
    let tr = $(this).closest('tr');
    let row = harvest_table.row( tr );
    let harvest_id = row.data().id;

    $.ajax({
        data:'id=' + harvest_id,
        url:'_curning_harvest_barcode',
        type:'post',
        aysnc:false,
        success:(res) => {
            printBarcode(res)
        }
    })

})

$('#harvest_table tbody').on('click', '.build_btn', function () {
    let tr = $(this).closest('tr');
    let row = harvest_table.row( tr );
    let harvest = row.data();

    //send_to_holdingInventory(harvest)
    location.href = 'form_curning_asset?id=' + harvest.id
})

//--------------------------/modal-build-------------------------------------
let batch_id
let weight_remain = 0

let msg_invalid_qty = "Quantity must be greater than 0"
let msg_invalid_weight = "weight must be greater than 0"
let msg_big_weight = "weight used must be less than Weightremain"
let msg_select_type = "please select the asset type"

let input_qty    = $("#qty")
let input_weight = $("#w_remain")
let input_type   = $("#type_id")

let error_message = $("#error_message")
let panel_error_message = $("#invalid_value")

let send_to_holdingInventory = (harvest) => {
    let batchid = harvest.id
    let um_batch = harvest.unit;
    let weightremain = harvest.remain_weight;

    $("#modal-build").modal('show')
    input_qty.val(0)
    input_weight.val(0)
    input_type.val(0)
    input_qty.focus()

    $("#um_batch").text("unit used: " + um_batch)
    
    input_weight.attr('placeholder','Enter the weight reference for ' + um_batch)
    
    panel_error_message.hide();
    $("#weight_remain").text('Weightremain is ' + weightremain)
    batch_id = batchid
    weight_remain = parseFloat(weightremain)
}

$("#close_alert").click(function(){
    panel_error_message.hide();
});

$('.saveBtn').click(function(){
    
    let qty = input_qty.val();

    let w_remain = input_weight.val();
    
    let type_id = input_type.val();

    if(qty <= 0 || w_remain <= 0)
    {
        if(qty <= 0)
        {
            error_message.text(msg_invalid_qty);
        }
        else
        {
            error_message.text(msg_invalid_weight);
        }
        
        panel_error_message.show();
        return;
    }

    if(w_remain > weight_remain)
    {
        error_message.text(msg_big_weight);
        panel_error_message.show();
        return;
    }

    if(type_id == 0)
    {
        $("#error_message").text(msg_select_type);
        panel_error_message.show();
        return;
    }

    if(confirm('Do you really send this harvest to Inventory on Holding?'))
    {
        let post_data = {
            id:batch_id,
            qty:qty,
            w:w_remain,
            type_id:type_id
        }

        $.ajax({
            url:'_curning_to_holding',
            data:post_data,
            type:'post',
            async:false,
            success:function(res)
            {
                if(res == 1)
                {
                    $("#modal-build").modal('toggle')
                    $('#harvest_table').dataTable().fnDestroy()
                    createTable()
                }
            }
        });
    }
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