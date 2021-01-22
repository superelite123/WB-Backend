var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
let waist_ids = windowvar.waist_ids
let sel_harvest_total_weight = 0;
console.log(waist_ids)
var harvest_table;
var perm = windowvar.perm;
let sel_harvest_id = null;

$("#harvest_table").on('click','.view',function(){
    var invoice_id = this.id;
    
    $.ajax({
        url:"items",
        data:'i_id='+invoice_id,
        type:'post',
        async:false,
        success:function(res)
        {
            showModal(res);
        }
    });
});

var showModal = (res) => {
    var items = res;
    var html = "";
    var cnt = 1;
    items.forEach(element => {
        html += "<tr><td>" + cnt + "</td>";
        html += "<td>" + element.plant_tag + "</td>";
        html += "<td>" + element.weight + "</td></tr>";
        html += "<td>" + element.id + "</td></tr>";
        cnt ++;
    })

    $("#harvest_item_table > tbody").html(html);
    $("#harvest_modal").modal();
}

// This must be a hyperlink
$("#export_btn").on('click', function(event) {
    var res = harvest_table.rows().data();
    convertToCSV(res).then(function(result){
        let filename = $("#reservation").val();

        exportCSVfile(filename,result);
    })
});

/*
var convertToCSV = (objArray) => {

return new Promise(function(next_operation){

var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
var str = "id,harvest_batch_id,Total Weight,Total_Pounds,Wet Weight,Total_Count,Unit Of Weight,Flower Drying Room Location,Strain,License,Creation Date\r\n";

for (var i = 0; i < array.length; i++) {
var line = '';
line += (i + 1) + ',';
line += array[i].harvest_batch_id + ',';
line += array[i].total_weight + ',';
line += array[i].total_pounds + ',';
line += array[i].wet_weight + ',';
line += array[i].total_count + ',';
line += array[i].unit + ',';
line += array[i].name + ',';
line += array[i].strain + ',';
line += array[i].license + ',';
line += array[i].created_at + ',';

var sub_array = array[i].items;
var sub_result = ' ,Plant Tag,Weight\r\n';

if(sub_array != null)
{
for (var j = 0; j < sub_array.length; j++) {
var newline = ' ';

newline += ' ,' + sub_array[j].plant_tag + ',';
newline += ' ,' + sub_array[j].weight;

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
*/
var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = "id,harvest_batch_id,Total Weight,Total_Pounds,Wet Weight,Total_Count,Unit Of Weight,Flower Drying Room Location,Strain,License,Creation Date\r\n";

        for (var i = 0; i < array.length; i++) {
            var line = '';
            line += (i + 1) + ',';
            line += array[i].harvest_batch_id + ',';
            line += array[i].total_weight + ',';
            line += array[i].total_pounds + ',';
            line += array[i].wet_weight + ',';
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
    html += '<tr>';
    html += '<td>Plant Tag</td><td>Weight</td></tr>';

    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr style="font-size: small;">';
        html += '<td>' + data[i].plant_tag + '</td>';
        html += '<td>' + data[i].weight + '</td>';
        html += '</tr>';
    }

    html += "</table>";
    return html;
}

var createTable = (date_range) => {
    if(perm == 'admin')
    {
        harvest_table = $('#harvest_table').DataTable({
            "ajax": {
                url: "get_harvest_table_data",
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
                      json[i].csv_btn = '<button class="btn btn-info btn-xs btn-edit csv_btn">CSV-Manager</button>';
                      json[i].csv_btncomp = '<button class="btn btn-info btn-xs btn-edit csv_btncomp">CSV-Compliance</button>';
                      json[i].edit_btn = '<button class="btn btn-info btn-xs btn-edit edit_btn">EDIT</button>';
                      json[i].delete_btn = '<button class="btn btn-danger btn-xs btn-edit delete_btn">Delete</button>';
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
                { "data": "csv_btncomp" },
                { "data": "csv_btn" },
                { "data": "edit_btn" }, 
                { "data": "delete_btn" },
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
    else
    {
        harvest_table = $('#harvest_table').DataTable({
            "ajax": {
                url: "get_harvest_table_data",
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
         //             json[i].ant_dry_weightlbs   = json[i].total_pounds_wet / 5;
        //              json[i].ant_dry_weightlbs = json[i].ant_dry_weightlbs.toFixed(2);
        //              json[i].ant_dry_weighton   = json[i].ant_dry_weightlbs * 16;
        //              json[i].ant_dry_weighton = json[i].ant_dry_weighton.toFixed(2);
        //              json[i].ant_dry_weightgr   = json[i].ant_dry_weightlbs * 453.592;
        //              json[i].ant_dry_weightgr = json[i].ant_dry_weightgr.toFixed(2);
                      json[i].total_count = json[i].items.length;
  //                    json[i].csv_btn = '<button class="btn btn-info btn-xs btn-edit csv_btn">CSV</button>';
                      if(perm == 'admin')
                      {
                        json[i].edit_btn = '<button class="btn btn-info btn-xs btn-edit edit_btn">EDIT</button>';
                        json[i].delete_btn = '<button class="btn btn-danger btn-xs btn-edit csv_btn">Delete</button>';
                      }
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
     //           { "data": "ant_dry_weightlbs" }, 
    //            { "data": "ant_dry_weighton" }, 
     //           { "data": "ant_dry_weightgr" },
                { "data": "name" }, 
                { "data": "strain" }, 
                { "data": "license" },
                { "data": "unit" },
                { "data": "created_at" },
  //              { "data": "csv_btn" },
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
                
  
            },
            "scrollX": true,
            "order": [[1, 'asc']]
        });
    }
    
}

let ask_target = () => {
    swal({
        title: "Harvest has now moved to  Harvest Dynamics",
        text: "How would you like to Proceed",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Go to Harvest Dynamics",
        cancelButtonText: "Return to Harvest List",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
            location.href="../harvestdynamics"
        } else {
            swal.close()
            $('#harvest_table').dataTable().fnDestroy()
            createTable($("#reservation").val());
        }
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
$('#harvest_table tbody').on('click', '.edit_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var harvest_id = row.data().id;
    location.href="edit?id=" + harvest_id;
});

$('#harvest_table tbody').on('click', '.dry_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var harvest_id = row.data().id;
    swal({
        title: "Are You Sure",
        text: "Send this Harvest to Harvest Dynamics?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
        }, function () {
            $.ajax({
                url:"send_dynamcis",
                data:'id=' + harvest_id,
                type:'post',
                async:false,
                success:(res) => {
                   ask_target()
                }
            })
    })
});

$('#harvest_table tbody').on('click', '.barcode_btn', function () {
    let tr = $(this).closest('tr');
    let row = harvest_table.row( tr );
    let harvest_id = row.data().id;

    $.ajax({
        data:'id=' + harvest_id,
        url:'_list_harvest_barcode',
        type:'post',
        aysnc:false,
        success:(res) => {
            printBarcode(res)
        }
    })

})

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
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var data = Array();
    data[0] = row.data(); 
    
    convertToCSV(data).then(function(result){
        let filename = data[0].harvest_batch_id + data[0].created_at;
        exportCSVfile(filename,result);
    });
});

$('#harvest_table tbody').on('click', '.delete_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var id = row.data().id;
    if(confirm('Are you really going to remove one Harvest?'))
    {
        $.ajax({
            url:'delete_harvest',
            data:'id=' + id,
            type:'post',
            async:false,
            success:(res) => {
                
                if(res == '1')
                {
                    alert('One Record Removed Successfully');
                    $('#harvest_table').dataTable().fnDestroy()
                    createTable($("#reservation").val());
                }
            }
        })
    }
});

$('#harvest_table tbody').on('click', '.fresh_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var harvest_id = row.data().id;
    sel_harvest_id = harvest_id
    $("#invalid_value").hide()
    $("#total_weight").html(row.data().total_weight)
    $("#modal_fresh").modal('show')
    //location.href="form_fresh?id=" + harvest_id;
});



$('#harvest_table tbody').on('click', '.waist_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var harvest_id = row.data().id;
    sel_harvest_id = harvest_id
    $("#total_weight_waist").html(row.data().total_weight)
    $("#waist_title").html('Waste Matrix for Harvest: ' + row.data().harvest_batch_id)
    $("#close_alert_waist").click()
    //clear modal
    waist_ids.forEach(element => {
        $("#w_" + element).val('')
        
        $("#m_" + element).val('')
    })
    sel_harvest_total_weight = row.data().total_weight
    $("#waist_total_weight").html('Total Harvest Wet Weight: ' + sel_harvest_total_weight)
    $("#modal_waist").modal('show')
    //location.href="form_fresh?id=" + harvest_id;
});
$("#close_alert").click(() => {
    $("#invalid_value").hide()
})
$("#close_alert_waist").click(() => {
    $("#invalid_value_waist").hide()
})
$(".saveBtn").click(() => {
    if(sel_harvest_id == null)
    {
        alert('select harvest')
        return
    }

    let containerCount = parseFloat($("#fresh_weight").val())
    
    if(Number.isNaN(containerCount))
    {
        $("#error_message").html('Enter the Correc Line Number')
        $("#invalid_value").show()
        return
    }
    
    location.href="form_fresh?id=" + sel_harvest_id + "&containerCount=" + containerCount;
})

//waist button
$("#deduct_waist").click(() => {
    let waist_type = []
    let weight
    let metrc
    let isEmpty = true
    let data = {id:sel_harvest_id,items:[]}
    let total_weight = 0
    waist_ids.forEach(element => {
        weight = parseFloat($("#w_" + element).val())
        
        metrc = $("#m_" + element).val()

        // if(Number.isNaN(weight))
        // {
        //     $("#invalid_value_waist").show()
        //     $("#error_message_waist").html('Enter the Correct Weight')
        //     return
        // }

        if(!Number.isNaN(weight) && metrc != "")
        {
            isEmpty = false
            data.items.push({waist_type:element,weight:weight,metrc:metrc})
            total_weight += weight;
        }
    });

    if(isEmpty)
    {
        $("#invalid_value_waist").show()
        $("#error_message_waist").html('You must enter at least one Waste Data')
        return
    }

    if(total_weight > sel_harvest_total_weight)
    {
        $("#invalid_value_waist").show()
        $("#error_message_waist").html('Weight can not be larger than Total Weight')
        return
    }
    
    swal({
        title: "Are You Sure",
        text: "Do You really process this Wasted Weight?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
        }, function () {
            $.ajax({
                url:"_deduct_waist",
                headers:{"content-type" : "application/json"},
                data:JSON.stringify(data),
                type:'post',
                async:false,
                success:(res) => {
                    console.log(res)
                    $("#modal_waist").modal('hide')
                    swal("Success!", "Waste Weight has processed!", "success")
                    $('#harvest_table').dataTable().fnDestroy()
                    createTable($("#reservation").val());
                }
            })
    })
})

$(function(){

    createTable($("#reservation").val());

    $("body").addClass('fixed');

    $("#filter").click(function(){
        $('#harvest_table').dataTable().fnDestroy()
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