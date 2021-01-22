let invoice_table = null
let list_btn_template_start = ''
let list_btn_template_end = ''
list_btn_template_start += '<div class="dropdown pull-right">'
list_btn_template_start += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
list_btn_template_start += '<span class="caret"></span></button>'
list_btn_template_start += '<ul class="dropdown-menu">'
list_btn_template_end += '</ul></div>'

$("#export_btn").on('click', function(event) {
    let tableInfo = invoice_table.page.info()
    let post_data = {
        date_range:$('#reservation').val(),
        length:tableInfo.recordsTotal,
    }
    $.ajax({
        url:'credit_notes/_archives',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        success:(res) => {
            convertToCSV(res.data).then(function(result){
                let filename = 'Credit Notes ' + $("#reservation").val();
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
        var str = "No,Customer,Current Balance,Total Credits\r\n"

        for (var i = 0; i < array.length; i++) {
            let line = "";
            line += (i + 1) + ','
            line += '\"' + array[i].name + '\",'
            line += array[i].balancePrice + ','
            line += array[i].totalPrice + '\r\n'

            var items = array[i].items;
            var sub_result = ',SO,Credit Note Value' + '\r\n'
            if(items != null)
            {
                for (var j = 0; j < items.length; j++) {
                    var newline = '  ';

                    newline += ' ,' + items[j].so;
                    newline += ' ,' + items[j].total_price + '\r\n';

                    sub_result += newline;
                }
            }
            if(sub_result != "")
            {
                line += sub_result+ '\r\n';
            }
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
var createTable = (date_range) => {
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"credit_notes/_archives",
            "dataType":"json",
            "type":"POST",
            "data":{date_range:date_range},
        },
        "columns":
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs"><i class="fas fa-plus"></i></button>'
            },
            { "data": "no" },
            { "data": "name" },
            { "data": "balancePrice" },
            { "data": "totalPrice" },
        ],
        "columnDefs": [
            { "orderable": false, "width": "5px", "targets": 0 },
            { "orderable": true, "width": "10px", "targets": 1 },
            { "orderable": false, "targets": 2 },
            { "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 4 },
        ],
    })
}
$('#invoice_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs"><i class="fas fa-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs"><i class="fas fa-minus"></i></button>')
    }
})

var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
    var html = '<table class="table table-bordered  table-striped childTable" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;table-layout: fixed;">';
    html += '<thead>'
    html += '<th style="width:10px">No</th>'
    html += '<th>SO</th>'
    html += '<th>Credit Note NO</th>'
    html += '<th>Date</th>'
    html += '<th>Credit Note Value</th>'
    html += '<th width=80>Action</th>'
    html += '<th>Reason</th>'
    html += '<th>Further Details</th>'
    html += '<th>Approved By</th>'
    html += '<th>Approved At</th>'
    html += '<th>Approve</th>'
    html += '</thead>'

    html += "<tbody>"
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].so + '</td>';
        html += '<td>' + data[i].no + '</td>';
        html += '<td>' + data[i].date + '</td>';
        html += '<td>' + data[i].total_price + '</td>';
        html += '<td>' + list_btn_template_start
        html += '<li><a href="credit_notes/snap/' + data[i].id + '" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>CN Snap</a></li>'
        html += '<li><a href="credit_notes/download/' + data[i].id + '" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>CN PDF</a></li>'
        html += '<li><a href="order_fulfilled/view/' + data[i].id_inv + '/0" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>Inv Snap</a></li>'
        html += '<li><a href="order_fulfilled/_download_invoice_pdf/' + data[i].id_inv + '" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>Inv PDF</a></li>'
        html += '<li><a href="#" onclick="javascript:onEmail(' + data[i].id + ')"><i class="fas fa-at">&nbsp;</i>Email</a></li>'
        if(data[i].status == 2)
        {
            html += '<li><a href="#" onclick="javascript:onDelete(' + data[i].id + ')"><i class="fas fa-trash-alt">&nbsp;</i>Delete</a></li>'
        }
        html += list_btn_template_end + '</td>'
        html += '<td class=\'break-td\'>' + data[i].note + '</td>';
        html += '<td class=\'break-td\'>' + data[i].detail + '</td>';
        html += '<td>' + data[i].approved_by + '</td>';
        html += '<td>' + data[i].approved_at + '</td>';
        html += '<td>'
        if(data[i].status == 2)
        {
            html += '<button class="btn btn-info btn-approve" data-id="' + data[i].id + '"><i class="fas fa-time"></i>&nbsp;Approve</button>'
        }
        if(data[i].status == 0)
        {
            html += '<span style="color:green;font-size:14px"><i class="fas fa-check"></i>&nbsp;Approved</span>'
        }
        if(data[i].status == 1)
        {
            html += '<span style="color:#333;font-size:14px">Archived</span>'
        }
        html += '</td>'
        html += '</tr>';
    }
    if(d.pDiscount != null)
    {
        html += '<tr>'
        html += '<td colspan=5>' + d.pDiscount.note + '</td>'
        html += '<td colspan=6>' + d.pDiscount.value + '</td></tr>'
    }
    html += "</tbody></table>";
    //applied credits
    html += '<h4>Applied Credits</h4>'
    html += '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0">';
    html += '<thead>'
    html += '<th>SO</th>'
    html += '<th width=100>Action</th>'
    html += '<th>Credit Note No</th>'
    html += '<th>Date</th>'
    html += '<th>Amount</th>'
    html += '</thead>'
    d.appliedCreditsData.forEach(element => {
        html += '<tr>' 
        html += '<td>' + element.so + '</td>'
        
        html += '<td>' + list_btn_template_start
        html += '<li><a href="order_fulfilled/view/' + element.id_inv + '/0" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>Inv Snap</a></li>'
        html += '<li><a href="order_fulfilled/_download_invoice_pdf/' + element.id_inv + '" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>Inv PDF</a></li>'
        html += '<li><a href="credit_notes/download/' + element.id_cn + '" target="_blank"><i class="fas fa-file-invoice-dollar">&nbsp;</i>CMemo Snap</a></li>'
        html += list_btn_template_end + '</td>'

        html += '<td>' + element.cn_no + '</td>'
        html += '<td>' + element.date + '</td>'
        html += '<td>' + element.amount + '</td>'
        html += '</tr>'
    });
    html += "<tbody>";
    return html;
}
$('#invoice_table tbody').on('click', '.btn-approve', function(){
    const id = $(this).attr('data-id')
    swal(
        {
            title: "Are You Sure",
            text: "You are about to approve Credit Note",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, 
        function () 
        {
          $.ajax({
              url:'credit_notes/_approve',
              data:'id=' + id,
              type:'post',
              success:(res) => {
                    swal('Success', 'One Credit Note has been approved', "success")
                  createTable($("#reservation").val());
              },
              error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
              }
          })
        }
    )
})
const onDelete = (id) => {
    swal(
        {
            title: "Are You Sure",
            text: "You are about to remove Credit Note",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, 
        function () 
        {
          console.log(id)
          $.ajax({
              url:'credit_notes/_delete/' + id,
              type:'get',
              success:(res) => {
                swal('Success', 'One Credit Note has been removed', "info")
                createTable($("#reservation").val());
              },
              error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
              }
          })
        }
    )
}

let validateEmail = (email) => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
const onEmail = (id) => {
    swal({
        title: "Email",
        text: "Enter Email Address",
        type: "input",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        inputPlaceholder: "please enter email address"
      }, function (inputValue) {
        if (inputValue === false) return false;
        if (inputValue === "") 
        {
          swal.showInputError("please correct email address");
          return false
        }
        let emails = inputValue.split(',').filter(email => validateEmail(email))
        console.log(emails)
        if(emails.length == 0)
        {
            swal.showInputError("No Valid Email Address!");
            return false
        }

        if(id == null)
        {
            swal.showInputError("No Selected Credit Note!");
            return false;
        }
        const post_data = {
            id:id,
            emails:emails
        }
        $.ajax({
            url:'credit_notes/email',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(post_data),
            type:'POST',    
            success:(res) => {
                swal('Success!', 'Email is sent Successfully', "success")
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    });
}
$(function(){

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        createTable($("#reservation").val());
    })
    createTable($("#reservation").val());
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
