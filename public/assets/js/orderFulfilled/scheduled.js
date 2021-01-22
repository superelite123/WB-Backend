let selectedOrder = null
let tblSchedule
const s_date = windowvar.start_date
const e_date = windowvar.end_date
$("#export_btn").on('click', function() {
    console.log('hi')
    var array = typeof calendarData != 'object' ? JSON.parse(calendarData) : calendarData;
    var str     = "Invoice,Delivery Date,Transported Via,Time,Customer,Amount\r\n"

    for (var i = 0; i < array.length; i++) {

        var line1 = ''
        line1 += array[i].number + ','
        line1 += array[i].dDate + ','
        line1 += array[i].deliveryer + ','
        line1 += array[i].time + ','
        line1 += '\"' + array[i].cName + '\",'
        line1 += array[i].amount + '\r\n'
        str += line1
    }
    const filename = 'Scheduled Deliveries'
    exportCSVfile(filename,str)
});
const onChnageDatte = (id,date,deliveryer) => {
    selectedOrder = id
    $('#delivery_schedule').val(date)
    $('#deliveries').val(deliveryer).change()
    $('#modal_time_range').modal('show')
}
const onRemoveDate = (id) => {
    swal({
        title: "Are You Sure",
        text: "You are about to delete Schedule",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
    }, function () {
        $.ajax({
            url:'_delete_schedule',
            data:'id=' + id,
            type:'get',
            success:(res) => {
                swal('Info', 'Schedule is removed', "info")
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
}
$('.deliveryConfirmBtn').on('click',() => {
    $('#modal_time_range').modal('hide')

    const schedule = $('#delivery_schedule').val()
    if(schedule == '')
    {
        alert('You need to select date')
        return
    }
    const deliveryer = $('#deliveries').val()
    let isAlert = false
    if(deliveryer == -1)
    {
        isAlert = true
        if(confirm("Are you sure to continue without assigning delivery method?"))
        {
            isAlert = false
        }
    }
    if(isAlert) return
    const postData = {
        date:schedule,
        id:selectedOrder,
        deliveryer:deliveryer,
    }
    $.ajax({
        url:'_chage_order_delivery_date',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(postData),
        success:(res) => {
            $.growl.notice({ message: "Success to schedule delivery" });
            location.reload()
        },
        error:(e) => {
            $.growl.error({ message: "Fail to schedule delivery" });
        }
    })
})
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
$(".toggle-expand-btn").click(function (e) {
    $(this).closest('.box.box-info').toggleClass('panel-fullscreen');
    $('.fc-day-grid-container').toggleClass('fc-day-grid-full-container');
});
$(function(){
    /* initialize the calendar
    -----------------------------------------------------------------*/
    $('#calendar').fullCalendar({
        header    : {
            left  : 'prev,next today',
            center: 'title',
            right : 'month,agendaWeek,agendaDay'
        },
        buttonText: {
            today: 'today',
            month: 'month',
            week : 'week',
            day  : 'day'
        },
        //Random default events
        events    : 'get_calendar_request',//calendarData,//[{'start':'2020-09-21'}],
        editable  : false,
        height:500,
        eventRender: function(event, element) {
            const title =   '<span class="fc-title">' +
                            event.title1 + '<br>' +
                            event.title5 + '<br>' +
                            event.title2 + '<br>' +
                            event.title3 + '<br>' +
                            event.title4 + '<br>' +
                            '</span>'
            element.prepend(title);
            element.find(".fc-time").html("")
        },
        eventClick:  function(event, jsEvent, view) {
            window.open('view/' + event.id + '/0');
        },
    })
    tblSchedule = $('#tbl-schedule').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    })
    $("body").addClass('fixed')
    //datetimepicker
    $('#delivery_schedule').datetimepicker({
        format: 'MM/DD/YYYY hh:mm a'
    });
    //dateranger
    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
    }).on("change", function() {
        loadRangedData($(this).val())
    })
    $('.select2').select2();
})
const loadRangedData = (date_range) =>
{
    let url = location.href.split('?')[0]
    location.href = url + '?date_range=' + date_range
}
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
