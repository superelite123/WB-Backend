var s_date = windowvar.s_date
var e_date = windowvar.e_date
var isAdmin = windowvar.isAdmin
let clocks = windowvar.clocks
let report_table
let sel_row = -1
let clocking_chart
$("#export_btn").on('click', function(event) {
    var res = report_table.rows().data();
    convertToCSVCompliance(res).then(function(result){
        let filename = $("#reservation").val() + '_Compliance';

        exportCSVfile(filename,result);
    })
});
var convertToCSVCompliance = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = "id,Driver,Lunch In,Lunch Out,Lunched In Time\r\n";

        for (var i = 0; i < array.length; i++) {
            var line = '';
            line += (i + 1) + ',';
            line += array[i].name + ',';
            line += array[i].start_time + ',';
            line += array[i].end_time + ',';
            line += array[i].clocked_in;
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

let createTable = (date_range) => {
    var param = {
        "ajax": {
            url: "_get_clocking_lunch_data",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range;
            },
            dataSrc: function ( json ) {
                
                for( let i=0, ien=json.length; i < ien; i++ ) {
                    json[i].no          = i + 1
                    if(json[i].user != null)
                        json[i].name        = json[i].user.firstname
                    else
                        json[i].name        = 'No Driver in lunch'
                    json[i].start_time  = json[i].start_time.substring(0,16)
                    json[i].end_time    = json[i].end_time.substring(0,16)
                    json[i].clocked_in  = time_diff(json[i].start_time.substring(11,16),json[i].end_time.substring(11,16))
                    json[i].edit_btn    = '<button class="btn btn-info btn-edit edit_btn"><i class="fas fa-edit"></i>&nbsp;Edit</button>'
                    json[i].delete_btn    = '<button class="btn btn-danger btn-edit delete_btn"><i class="fas fa-trash"></i>&nbsp;Delete Log</button>'
                }

                return json;
            }
        },
        "columns": 
        [
            { "data": "no" }, 
            { "data": "name" }, 
            { "data": "start_time" },
            { "data": "end_time" }, 
            { "data": "clocked_in" }, 
            { "data": "edit_btn" },
        ],
        "order": [[0, 'asc']]
    }

    if(isAdmin == 1)
    {
        param.columns.push({ "data": "delete_btn" })
    }
    console.log(param.columns)
    report_table = $('#tbl_report').DataTable(param);  
}

function time_diff(start, end) {
    start = start.split(":");
    end = end.split(":");
    var startDate = new Date(0, 0, 0, start[0], start[1], 0);
    var endDate = new Date(0, 0, 0, end[0], end[1], 0);
    var diff = endDate.getTime() - startDate.getTime();
    var hours = Math.floor(diff / 1000 / 60 / 60);
    diff -= hours * 1000 * 60 * 60;
    var minutes = Math.floor(diff / 1000 / 60);

    // If using time pickers with 24 hours format, add the below line get exact hours
    if (hours < 0)
       hours = hours + 24;

    return (hours <= 9 ? "0" : "") + hours + ":" + (minutes <= 9 ? "0" : "") + minutes;
}

$('#tbl_report tbody').on('click', '.edit_btn', function () {
    var tr = $(this).closest('tr')
    var row = report_table.row( tr )
    sel_row = row.data().id
    const start_time = row.data().start_time.split(' ')[1]
    const end_time   = row.data().end_time.split(' ')[1]
    $('#picker_s_time').val(start_time)
    $('#picker_e_time').val(end_time)
    
    $('#modal_time_range').modal('show')
})
$('#tbl_report tbody').on('click', '.delete_btn', function () {
    var tr = $(this).closest('tr')
    var row = report_table.row( tr )
    swal({
        title: "Are You Sure",
        text: "You are about to this Log",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
    }, function (choice) {
        if(choice)
        {
            $.ajax({
                url:'_delete_distro_log',
                data:'id=' + row.data().id,
                type:'get',
                success:(res) => {
                    swal('Success', "Log is deleted", "info")
                    $('#tbl_report').dataTable().fnDestroy()
                    createTable($("#reservation").val());
                },
                error:(e) => {
                    swal(e.statusText, e.responseJSON.message, "error")
                }
            })
        }
    })
})

$('.confirmBtn').click(() => {
    let s_time = $('#picker_s_time').val()
    let e_time = $('#picker_e_time').val()
    
    if(s_time == '' || e_time == '')
    {
        alert('Selet the time')
        return false
    }

    let s_time_split = s_time.split(':')
    let e_time_split = e_time.split(':')

    if(s_time_split[0] > e_time_split[0])
    {
        alert('Select the correct time range')
        return false
    }
    if(s_time_split[0] == e_time_split[0] && s_time_split[1] >= e_time_split[1])
    {
        alert('Select the correct time range')
        return false
    }

    swal({
        title: "Are You Sure",
        text: "You are about to change lunch time?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false
        }, function () {
            $.ajax({
                url:'_change_time_range',
                type:'post',
                data:'row_id=' + sel_row + '&s_time=' + s_time + '&e_time=' + e_time + '&mode=1',
                async:false,
                success:(res) => {
                    $('#modal_time_range').modal('hide')
                    $('#tbl_report').dataTable().fnDestroy()
                    createTable($("#reservation").val());
                },
                error:(e) => {

                }
            })
    })
})
let renderChart = () => {
    let sel_date = $('#chart_date').val()
    if(sel_date == '')
    {
        alert('Select the Date')
        return false
    }

    $.ajax({
        url:'_get_clocking_chart_data',
        type:'post',
        data:'date_range=' + $("#reservation_c").val(),
        async:false,
        success:(res) => {
            clocking_chart = new FusionCharts({
                type: 'stackedcolumn3d',
                dataFormat: 'json',
                renderAt: 'clocking_chart',
                width: '100%',
                height: '400',
                dataSource: {
                  "chart": {
                    "theme": "zune",
                    "caption": "Work Time",
                    "xAxisName": "Harvester",
                    "yAxisName": "Hour per day",
                    "numberPrefix": "h",
                    "lineThickness": "3",
                    "flatScrollBars": "1",
                    "scrollheight": "10",
                    "numVisiblePlot": "12",
                    "showHoverEffect": "1",
                    "exportEnabled": "1"
                  },
                  "categories": [{
                    "category": res.label,
                  }],
                  "dataset": [{
                    "data": res.value
                  }]
                }
              }).render()
        }
    })   
}

$('#chart_date').focusout(() => {
    renderChart()
})
$(() =>{
    createTable($("#reservation").val());
    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        $('#tbl_report').dataTable().fnDestroy()
        createTable($("#reservation").val());
    })

    $("#reservation_c").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        renderChart()
    })

    $('.date').datetimepicker({
        format: 'H:m',
    });
    $('.date2').datetimepicker({
        format: 'YYYY-MM-DD',
    }).on('changeDate', function(ev){
        console.log('Hi')
    });
    
    let d = new Date()
    $('#chart_date').val(d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate())
    //renderChart()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});