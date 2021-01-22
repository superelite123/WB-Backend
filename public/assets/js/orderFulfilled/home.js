var s_date = windowvar.start_date;
var e_date = windowvar.end_date;
let edit_permission = windowvar.edit_permission
var invoice_table;
let list_btn_template_start = ''
let list_btn_template_end = ''
list_btn_template_start += '<div class="dropdown pull-right">'
list_btn_template_start += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
list_btn_template_start += '<span class="caret"></span></button>'
list_btn_template_start += '<ul class="dropdown-menu">'
list_btn_template_end += '</ul></div>'
let selected_invoice = null
let totalStat = {};
$("#export_btn").on('click', function(event) {
    $('#loadingModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#loadingModal').modal('show')
    let tableInfo = invoice_table.page.info()
    let post_data = {
        date_range:$('#reservation').val(),
        length:tableInfo.recordsTotal,
        start:0,
        status:3
    }
    $.ajax({
        url:'get_fulfilled_list',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        success:(res) => {
            $('#loadingModal').modal('hide')
            convertToCSV(res.data).then(function(result){
                let filename = 'Fulfilled Orders ' + $("#reservation").val();
                exportCSVfile(filename,result);
            })
        },
        error:(e) => {
            $('#loadingModal').modal('hide')
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })

});

var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = ''
        var str1 = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,Metrc Manifest,Customer Terms,Paid,Delivered\r\n"
        let str2 = "Qty,Discount,Extra Discount,Sub Total,Promotion Value,Less Discount,Exercise Tax,Total Due\r\n";

        for (var i = 0; i < array.length; i++) {

            var line1 = str1;
            var line2 = str2;
            let paid      = array[i].paid != null?'Paid':'No Paid'
            let delivered = array[i].delivered != null?'Delivered':'No Delivered'
            line1 += array[i].date + ',';
            line1 += '\"' + array[i].clientname + '\",';
            line1 += '\"' + array[i].customer.address1 + '\",';
            line1 += array[i].number + ',';
            line1 += array[i].customer.licensenumber + ','
            line1 += array[i].m_m_str + ',';
            line1 += array[i].total_info.termLabel + ','
            line1 += paid + ','
            line1 += delivered + '\r\n'
            line2 += array[i].total_info.qty + ',';
            line2 += array[i].total_info.discount + ',';
            line2 += array[i].total_info.e_discount + ',';
            line2 += array[i].total_info.base_price + ',';
            line2 += array[i].total_info.promotion + ',';
            line2 += (array[i].total_info.base_price - array[i].total_info.discount) + ',';
            line2 += array[i].total_info.tax + ',';
            line2 += array[i].total_info.adjust_price + '\r\n';
            var sub_array = array[i].items;
            var sub_result = ' ,Description,Qty,Units,Unit Price,Weight,Unit Label,Discount,Discount Type,Extra Discount,Sub Total,'
            sub_result += 'Less Disocunt,Excise TAX,Line Note\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';

                    newline += ' ,' + sub_array[j].description;
                    newline += ' ,' + sub_array[j].qty;
                    newline += ' ,' + sub_array[j].units;
                    newline += ' ,' + sub_array[j].unit_price;
                    newline += ' ,' + sub_array[j].weight;
                    newline += ' ,' + sub_array[j].unit_label;
                    newline += ' ,' + sub_array[j].discount;
                    newline += ' ,' + sub_array[j].discount_label;
                    newline += ' ,' + sub_array[j].e_discount;
                    newline += ' ,' + sub_array[j].base_price;
                    newline += ' ,' + sub_array[j].extended;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + sub_array[j].tax_note;

                    sub_result += newline + '\r\n';
                }
            }
            if(array[i].pDiscount != null)
            {
                sub_result += ' ,' + array[i].pDiscount.note
                sub_result += ' , , , , , ,' + array[i].pDiscount.value + '\r\n'
            }
            line1 += line2;
            if(sub_result != "")
            {
                line1 += sub_result+ '\r\n';
            }
            str += line1
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

let _email_requirment_check = (order_id) => {
    return new Promise(function(fulfill,reject){
        if($('#distributors').val() == 0)
        {
            alert('You need to set distributor')
            return false
        }
        $.ajax({
            url:'_email_requirment_check',
            type:'post',
            data:'id=' + order_id,
            async:false,
            success:(res) => {
                if(res.status.total == 1)
                {
                    fulfill()
                }
                else
                {
                    reject(res)
                }
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
}

var createTable = (date_range) => {
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"get_fulfilled_list",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range,status:3},
            "dataSrc": function ( json ) {
                totalStat = json.total_stat
                $('.footer-sub_total').text('$' + totalStat.sub_total)
                $('.footer-discount_total').text('$' + totalStat.discount_total)
                $('.footer-e_discount_total').text('$' + totalStat.e_discount_total)
                $('.footer-tax_total').text('$' + totalStat.tax_total)

                return convert_ajax_table_data(json.data)
            }
        },
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
            { "data": "number2" },
            { "data": "salesRep"},
            { "data": "clientname" },
            { "data": "total" },
            { "data": "date" },
            { "data": "distributor" },
            { "data": "metrc_manifest" },
            { "data": "txtMMStr" },
            { "data": "scheduledLabel" },
            { "data": "coainbox_chk" },
            { "data": "metrc_chk"},
            { "data": "actions" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "orderable": false, "targets": 1 },
            { "orderable": false, "targets": 2 },
            { "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 4 },
            { "orderable": false, "targets": 5 },
            { "orderable": false, "targets": 6 },
            { "orderable": false, "targets": 7 },
            { "orderable": false, "targets": 8 },
            { "orderable": false, "targets": 9 },
            { "orderable": false, "targets": 10 },
            { "orderable": false, "targets": 11 },
            { "orderable": false, "targets": 12 },
            { "orderable": false, "targets": 13 },
            { "orderable": false, "targets": 14 },
        ],
        'scrollX':true
    });
}
let convert_ajax_table_data = (json) => {
    let { start } = invoice_table.page.info()
    for ( var i=0, ien=json.length ; i<ien ; i++, start ++ ) {
        json[i].no = start + 1;

        let coainbox = ''
        let paid = ''
        let metrc_ready = ''

        coainbox    = json[i].coainbox != null?'checked':''
        paid        = json[i].paid != null?'checked':''
        metrc_ready = json[i].metrc_ready != null?'checked':''

        json[i].coainbox_chk    = '<input type="checkbox" class="coainbox_chk" ' + coainbox + ' >'
        json[i].paid_chk        = '<input type="checkbox" class="paid_chk" ' + paid + ' >'
        json[i].metrc_chk       = '<input type="checkbox" class="metrc_chk" ' + metrc_ready + ' >'
        json[i].chkDeliver      = '<input type="checkbox" class="chkDeliver">'
        json[i].txtMMStr        = '<p class="form-control txtMMStr" style="width:100%;cursor:pointer" data-original-title title>' + json[i].m_m_str + '</p>';
        json[i].total = '$' + json[i].total_info.adjust_price
        json[i].actions = list_btn_template_start

        json[i].actions += '<li><a href="view/' + json[i].id + '/0" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'
        json[i].actions += '<li><a href="#" class="email_btn"><i class="fas fa-envelope-square">&nbsp;</i>Email</a></li>'
        json[i].actions += '<li><a href="view/' + json[i].id + '/1" target="_blank"><i class="fas fa-print">&nbsp;</i>Print</a></li>'
        json[i].actions += '<li><a href="#" class="schedule_btn"><i class="fas fa-shipping-fast">&nbsp;</i>Schedule</a></li>'
        json[i].actions += '<li><a href="_download_invoice_pdf/' + json[i].id + '" target="_blank"><i class="fas fa-file-pdf"></i>&nbsp;Download Invoice</a></li>'
        json[i].actions += '<li><a href="barcode_print/' + json[i].id + '" target="_blank"><i class="fas fa-print">&nbsp;</i>Barcode sheet</a></li>'
        json[i].actions += '<li><a href="#" class="csv_btn"><i class="fas fa-file-csv"></i>&nbsp;CSV</a></li>'
        if(edit_permission == '1')
        {
            json[i].actions += '<li><a href="edit/' + json[i].id + '"><i class="fas fa-edit"></i>&nbsp;Edit</a></li>'
            json[i].actions += '<li class="divider"></li>'
            json[i].actions += '<li><a href="delete/' + json[i].id + '"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete</a></li>'
        }
        json[i].actions += list_btn_template_end
        let distributor_html = ''
        let selected = ''
        distributor_html = '<select class="form-control select2 " style="width: 100%;" id="distributors">'

        distributors.forEach(item => {
            selected = ''
            if(json[i].distuributor_id == item.distributor_id) selected = 'selected'
            distributor_html += '<option value="' + item.distributor_id + '" ' + selected + '>'
            distributor_html += item.companyname + '</option>'
        })
        distributor_html += '</select>'
        json[i].distributor = distributor_html

        //metrc manifest
        distributor_html = ''
        selected = ''
        distributor_html = '<select class="form-control select2" style="width: 100%;" id="mManifest">'

        metrc_manifests.forEach(item => {
            selected = ''
            if(json[i].metrc_manifest == item.id) selected = 'selected'
            distributor_html += '<option value="' + item.id + '" ' + selected + '>'
            distributor_html += item.name + '</option>'
        })
        distributor_html += '</select>'
        json[i].metrc_manifest = distributor_html

        //add scheduled result
        json[i].scheduledLabel = json[i].scheduled == 1?'Yes':'No'
    }
    return json
}
var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>';
    html += '<th>No</th>'
    html += '<th>Description</th>';
    html += '<th>Qty</th>';
    html += '<th>Weight</th>';
    html += '<th>Units</th>';
    html += '<th>Unit_price</th>';
    html += '<th>CPU</th>';
    html += '<th>Discount</th>';
    html += '<th>Discount Type</th>';
    html += '<th>Extra Discount</th>';
    html += '<th>Sub Total</th>';
    html += '<th>Extended</th>';
    html += '<th>Line Note</th>';
    html += '<th>Adjust Price</th>';
    html += '</thead>';

    html += "<tbody>";
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].description + '</td>';
        html += '<td>' + data[i].qty + '</td>';
        html += '<td>' + data[i].weight + '</td>';
        html += '<td>' + data[i].units + '</td>';
        html += '<td>' + data[i].unit_price + '</td>';
        html += '<td>' + data[i].cpu + '</td>';
        html += '<td>' + data[i].discount + '</td>';
        html += '<td>' + data[i].discount_label + '</td>';
        html += '<td>' + data[i].e_discount + '</td>';
        html += '<td>' + data[i].base_price + '</td>';
        html += '<td>' + data[i].extended + '</td>';
        html += '<td>' + data[i].tax_note + '</td>';
        html += '<td>' + data[i].adjust_price + '</td>';
        html += '</tr>';
    }
    if(d.pDiscount != null)
    {
        html += '<tr>'
        html += '<td colspan=7>' + d.pDiscount.note + '</td>'
        html += '<td colspan=6>' + d.pDiscount.value + '</td>'
        html += '<td colspan=6></td>'
    }
    html += "</tbody></table>";
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
$('#invoice_table tbody').on('click','.txtMMStr',function(){
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    selected_invoice = row.data().id
    $(this).popover({
        trigger: 'click',
        html: true,
        title: function() {
            return 'Enter Metrc Manigest Tag';
        },
        content: function() {
            return $('#popover-form').html();
        },

        container: 'body',
        placement: 'left'
    })
})
$(document).on("click", ".popover .btn-popover-save" , function(){
    const mmstr = $(this).parents(".popover").find('.txt-popover-mmstr').val()
    $.post({
        url:'_set_metrc_manifest',
        data:'m_m_str=' + mmstr + '&id=' + selected_invoice,
        success:(res) => {
            swal('Success', 'Metrc Manifest is set successfully', "success")
            createTable($("#reservation").val())
            $(this).parents(".popover").popover('hide')
        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
        }
    });
})
$(document).on("click", ".popover .btn-popover-cancel" , function(){
    $(this).parents(".popover").popover('hide')
});

$('#invoice_table tbody').on('click','.chkDeliver',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    swal({
        title: "Are You Sure",
        text: "You are about to treat this Order as Delivered",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
      }, function (choice) {
          console.log(choice)
          $.ajax({
              url:'_set_status',
              data:'id=' + invoice_id + '&status=4',
              type:'get',
              success:(res) => {
                  swal.close()
                  createTable($("#reservation").val());
              },
              error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
              }
          })
      })
})
$('#invoice_table tbody').on('change','#mManifest',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    let dId = $(this).val()
    $.ajax({
        url:'_set_m_manifest',
        data:'id='+invoice_id + '&status='+dId,
        type:'post',
        async:false,
        success:(res) => {
            // $('#invoice_table').dataTable().fnDestroy()
            // createTable($("#reservation").val());
        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
            return false
        }
    })
})
$('#invoice_table tbody').on('click','.schedule_btn',function(){
    const tr = $(this).closest('tr')
    const row = invoice_table.row( tr )
    selected_invoice = row.data().id

    $('#modal_time_range').modal('show')
})

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
        deliveryer:deliveryer,
        id:selected_invoice
    }
    $.ajax({
        url:'registerDeliverySchedule',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(postData),
        success:(res) => {
            $.growl.notice({ message: "Success to schedule delivery" });
            swal('Email Sent', 'Email Sent to ' + res.salesEmail, "info")
            createTable($("#reservation").val());
        },
        error:(e) => {
            $.growl.error({ message: "Fail to schedule delivery" });
        }
    })
})
$('#invoice_table tbody').on('change','#distributors',function(){
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )
    let invoice_id = row.data().id
    let dId = $(this).val()
    $.ajax({
        url:'_set_distributor',
        data:'id='+invoice_id + '&distributor='+dId,
        type:'post',
        async:false,
        success:(res) => {
            // $('#invoice_table').dataTable().fnDestroy()
            // createTable($("#reservation").val());
        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
            return false
        }
    })
})
$('#invoice_table tbody').on('click', '.coainbox_chk', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    let coainbox = $(this).prop('checked') == true?1:0

    $.ajax({
        url:'_set_coainbox_order',
        type:'post',
        async:false,
        data:'id=' + invoice_id + '&coainbox=' + coainbox,
        success:(res) => {

        },
        error:(e) => {
            swal('Error',"Error occur while connect to server coa","warning")
        }
    })
});


$('#invoice_table tbody').on('click', '.paid_chk', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    let paid = $(this).prop('checked') == true?1:0

    $.ajax({
        url:'_set_paid_order',
        type:'post',
        async:false,
        data:'id=' + invoice_id + '&paid=' + paid,
        success:(res) => {

        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })
});
$('#invoice_table tbody').on('click', '.metrc_chk', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    let paid = $(this).prop('checked') == true?1:0

    $.ajax({
        url:'_set_metrc_ready_order',
        type:'post',
        async:false,
        data:'id=' + invoice_id + '&status=' + paid,
        success:(res) => {

        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })
});
$('#invoice_table tbody').on('click', '.email_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var order_id = row.data().id;
    let order_inv = row.data().number
    let companyemail = row.data().companyemail
    let salesemail   = row.data().salesemail
    _email_requirment_check(order_id).then(() => {
        _emailing(companyemail,salesemail,order_id,order_inv)
    },(res) => {
        _email_reject(res,order_inv)
    })
});
$('#invoice_table tbody').on('click', '.barcode_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var invoice_id = row.data().id;
    window.open("fulfilled_print?id=" + invoice_id, '_blank');
    //location.href="view?id=" + invoice_id;
});

$('#invoice_table tbody').on('click', '.csv_btn', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );
    var data = Array();
    data[0] = row.data();

    convertToCSV(data).then(function(result){
        let filename = data[0].number + data[0].date;
        exportCSVfile(filename,result);
    });
})
$('#invoice_table tbody').on('click', '.archive_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
});
let _emailing = (cemail,salesemail,order_id,order_inv) => {
    if(!validateEmail(cemail))
    {
        swal('Customer Email  "' + cemail + '" is not valild', "", "error")
    }
    if(!validateEmail(salesemail))
    {
        swal('Sales Man Email  "' + salesemail + '" is not valild', "", "error")
    }
    selected_invoice = order_id
    $('.i-num').html(order_inv)
    $('#modal-client-name').html(cemail)
    $('#modal-sales-name').html(salesemail)
    $('#modal-email-confirm').modal('show')
}
let _submit_email = (mode) => {
    $('#modal-email-confirm').modal('hide')
    $.ajax({
        url:'_fulfilled_email',
        type:'post',
        data:'id=' + selected_invoice + '&mode='+mode,
        success:function(res){
            swal("Email sent Successfully", "", "success")
        },
        error:function(e){
            swal(e.statusText, e.responseJSON.message, "error")
        },
    })
}
let _email_reject = (res,inv=null) => {
    let html = ''
    let cnt = 1;
    if(res.status.metrc_ready == 0)
    {
        html += '<h4>' + cnt + '.&nbsp;Metrc need to ready</h4>'
        cnt ++
    }
    if(res.status.metrc_manifest == 0)
    {
        html += '<h4>' + cnt + '.&nbsp;Metrc Manifest need to be completed</h4>'
        cnt ++
    }
    if(res.status.term == 0)
    {
        html += '<h4>' + cnt + '.&nbsp;Client need to have Term</h4>'
        cnt ++
    }
    if(res.status.coa == 0)
    {
        html += '<h4>' + cnt + '.&nbsp;Coas are missing</h4>'
        cnt ++
    }
    res.missing_coas.forEach(element => {
        html += '<h5>' + element + '</h5>'
    });
    $('#missed_coas').html(html)
    $('#coa_inv').html(inv)
    $('#modal_missing_coa').modal()
}
$body = $("body");
let validateEmail = (email) => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
$(function(){

    $("body").addClass('fixed')
    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        createTable($("#reservation").val())
    })
    createTable($("#reservation").val())
    createRejectTable(null)
    $('.select2').select2();
    //datetimepicker
    $('#delivery_schedule').datetimepicker({
        format: 'MM/DD/YYYY h:mm a'
    });
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
