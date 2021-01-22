let invoice_table
let selectedEvent = null
//RadioBox Check
$('input[name*="radio"]').click(() => {
    createTable()
})
$('.btnCollection').click(() => {
    if(selectedEvent == null)
    {
        $('#calendarModal').hide()
        return false
    }
    if($('#rOptions').val() == 0)
    {
        window.open(collectionUrl + selectedEvent.id,'_blank')
    }
    else
    {
        window.open(viewUrl + selectedEvent.id,'_blank')
    }
})
let createTable = () => {
    let payType = $('input[name*="radioPType"]:checked').val()
    let dayType = $('input[name*="radioDType"]:checked').val()
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({
        // "processing":false,
        // "serverSide":true,
        "ajax": {
            url: "_get_list",
            type: 'POST',
            "data": function ( d ) {
                d.p=payType
                d.d=dayType
            },
            dataSrc: function ( json ) {
                json.forEach(element => {
                    element.lTotal = '$' + element.total
                    element.lRSubTotal = '$' + element.rSubTotal
                    element.lRTax = '$' + element.rTax
                });
                return json
            }
        },
        "columns":
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs btn-edit flat"><i class="fas fa-plus"></i></button>'
            },
            { "data": "no" },
            { "data": "number" },
            { "data": "number2" },
            { "data": "clientname" },
            { "data": "lTotal" },
            { "data": "lRSubTotal" },
            { "data": "lRTax" },
            { "data": "date" },
            { "data": "dDate" },
            { "data": "termLabel" },
            { "data": "payDate" },
            { "data": "paidDate" },
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            let total = 0;
            let rSubTotal = 0;
            let rTax = 0;
            for(let i = 0; i < data.length; i ++)
            {
                total       += parseFloat(data[i].total)
                rSubTotal  += parseFloat(data[i].rSubTotal)
                rTax       += parseFloat(data[i].rTax)
            }

            $( api.column( 4 ).footer() ).html(
                '$' + total.toFixed(2)
            );
            // Update footer
            $( api.column( 5 ).footer() ).html(
                '$' + rSubTotal.toFixed(2)
            );
            $( api.column( 6 ).footer() ).html(
                '$' + rTax.toFixed(2)
            );
        },
        "rowCallback": function( row, data, dataIndex){
            let font_color
            console.log(payType)
            switch(payType)
            {
                case '0':
                    font_color = 'blue'
                    break;
                case '1':
                    font_color = 'red'
                    break;
                case '2':
                    font_color='green'
                    break;
            }

            $(row).css('color',font_color)
        },
        'responsive': true
    })
}
$('#invoice_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );

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
var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = [d.logs.allowed,d.logs.unallowed]
    let titles = [
        {title:'Verified Payments',message:'No Veriffied Payments'},
        {title:'Awaiting Verification',message:'No Awaiting Verification'}
    ]
    let html = ''

    data.forEach((items,k) => {
        html += '<h3>' + titles[k].title + '</h3>'
        html += '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
        html += '<thead>'
        html += '<th>No</th>'
        html += '<th>Type</th>'
        html += '<th>Cost</th>'
        html += '<th>Collected By</th>'
        html += '<th>Date</th>'
        html += '</thead>'
        items.forEach((element,i) => {
            html += '<tr>'
            html += '<td>' + (i + 1) + '</td>'
            html += '<td>' + element.type + '</td>'
            html += '<td>' + element.amount + '</td>'
            html += '<td>' + element.deliveryerName + '</td>'
            html += '<td>' + element.date + '</td>'
            html += '</tr>'
        })
        if(items.length == 0)
        {
            html += '<tr style="text-align:center"><td colspan=5>' + titles[k].message + '</td></tr>'
        }
        html += "</tbody></table>"
    })
    return html
}
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
    events    : calendarData,
    editable  : false,
    height:500,
    eventClick:  function(event, jsEvent, view) {
        selectedEvent = event
        $('#modalTitle').html(event.number)
        $('#modalBody').html(event.description)
        $('#eventUrl').attr('href',event.url)
        $('#calendarModal').modal()
    },
    eventRender: function(event, element) {
        if(event.isContact == 1){
            element.find(".fc-title").prepend('<i class="fas fa-phone">&nbsp;</i>' + event.number + '<br>');
        }
        else
        {
            element.find(".fc-title").prepend(event.number + '<br>');
        }
    }
    ,
    eventAfterRender: function (event, element) {
        $(element).tooltip({title:event.number, container: "body"});
    }
  })
  createTable()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
