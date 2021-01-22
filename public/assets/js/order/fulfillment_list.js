var s_date = windowvar.start_date
var e_date = windowvar.end_date
var invoice_table
let problematic_table
let inv_restock_table
let inv_restock_table_data
let list_btn_template_start = ''
let list_btn_template_end = ''
list_btn_template_start += '<div class="dropdown pull-right">'
list_btn_template_start += '<button class="btn btn-info btn-sm btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action'
list_btn_template_start += '<span class="caret"></span></button>'
list_btn_template_start += '<ul class="dropdown-menu">'
list_btn_template_end += '</ul></div>'
$("#export_btn").on('click', function(event) {

    var res = invoice_table.rows().data();

    convertToCSV(res).then(function(result){
        let filename = 'Fulfillment Orders ' + $("#reservation").val();
        exportCSVfile(filename,result);
    })
});

$("#export_problematic_btn").on('click', function(event) {

    var res = problematic_table.rows().data();

    convertToCSV(res).then(function(result){
        let filename = 'Problematic Orders ' + $("#reservation").val();
        exportCSVfile(filename,result);
    })
});

var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){

        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = ''
        var str1 = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,Metrc Manifest,Customer Terms\r\n"
        let str2 = "Qty,Discount,Sub Total,Promotion Value,Less Discount,Exercise Tax,Total Du\r\n";

        for (var i = 0; i < array.length; i++) {
            var line = '';

            var line1 = str1;
            var line2 = str2;
            line1 += array[i].date + ',';
            line1 += array[i].clientname + ',';
            line1 += array[i].customer.address1 + ',';
            line1 += array[i].number + ',';
            line1 += array[i].customer.licensenumber + ',';
            line1 += array[i].m_m_str + ',';
            line1 += array[i].total_info.termLabel + '\r\n'
            line2 += array[i].total_info.qty + ',';
            line2 += array[i].total_info.discount + ',';
            line2 += array[i].total_info.base_price + ',';
            line2 += array[i].total_info.promotion + ',';
            line2 += (array[i].total_info.base_price - array[i].total_info.discount) + ',';
            line2 += array[i].total_info.tax + ',';
            line2 += array[i].total_info.adjust_price + '\r\n';

            var sub_array = array[i].items;
            var sub_result = ' ,Strain,Type,Quantity,Units,Unit Price,CPU,Discount,Discount Type,Extra Discount,Sub Total,Extended,TAX,Line Note,Adjust Price\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';

                    newline += ' ,' + sub_array[j].description;
                    newline += ' ,' + sub_array[j].qty;
                    newline += ' ,' + sub_array[j].units;
                    newline += ' ,' + sub_array[j].unit_price;
                    newline += ' ,' + sub_array[j].cpu;
                    newline += ' ,' + sub_array[j].discount;
                    newline += ' ,' + sub_array[j].discount_label;
                    newline += ' ,' + sub_array[j].e_discount;
                    newline += ' ,' + sub_array[j].sub_total;
                    newline += ' ,' + sub_array[j].less_discount;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + sub_array[j].tax_note;
                    newline += ' ,' + sub_array[j].adjust_price;

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

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
var createTable = (date_range) => {
    invoice_table = $('#invoice_table').DataTable({
        "ajax": {
            url: "get_fulfillment_list",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range
            },
            dataSrc: function ( json ) {
                console.log(json)
                for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1;
                    json[i].total = parseFloat(json[i].total).toFixed(2)
                    json[i].actions = list_btn_template_start
                    json[i].actions += '<li><a href="#" class="fulfillment_btn"><i class="fas fa-check">&nbsp;</i>FulFill</a></li>'

                    json[i].actions += '<li><a href="fulfillment_detail/' + json[i].id + '/0' + '" target="_blank">'
                    json[i].actions += '<i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'

                    json[i].actions += '<li><a href="fulfillment_detail/' + json[i].id + '/1' + '" target="_blank">'
                    json[i].actions += '<i class="fas fa-print">&nbsp;</i>Print</a></li>'

                    json[i].actions += '<li><a href="#" class="csv_btn"><i class="fas fa-file-csv"></i>&nbsp;CSV</a></li>'
                    json[i].actions += '<li class="divider"></li>'
                    json[i].actions += '<li><a href="#" class="delete_btn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete</a></li>'
                    json[i].actions += list_btn_template_end
                    json[i].fTime = json[i].total_info.fTime
                    json[i].priorityLabel = json[i].total_info.priorityLabel
                }
                return json
            }
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            let total = 0;
            for(let i = 0; i < data.length; i ++)
            {
                total       += parseFloat(data[i].total)
            }
            $('.footer-total').text('$' + numberWithCommas(total.toFixed(2)))
            // $( api.column( 5 ).footer() ).html(
            //     'Total:<br>$' + total.toFixed(2)
            // );
        },
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
            { "data": "clientname" },
            { "data": "companyname" },
            { "data": "total" },
            { "data": "date" },
            { "data": "fTime" },
            { "data": "priorityLabel" },
            { "data": "actions" },
        ],
        "rowCallback": function( row, data, dataIndex){
            if(data.priority.bk_color != null)
            {
                $(row).css('background-color',data.priority.bk_color);
                $(row).css('color',data.priority.font_color);
            }

        },
        'responsive': true
    });
}
let createProblematicTable = (date_range) => {
    problematic_table = $('#problematic_table').DataTable({
        "ajax": {
            url: "get_fulfillment_problematic_list",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range
                d.status=0
            },
            dataSrc: function ( json ) {

                for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1;
                    json[i].total = parseFloat(json[i].total).toFixed(2)
                    let s_person = json[i].salesperson
                    if(s_person != null)
                    {
                        s_person = json[i].salesperson.firstname + ' ' + json[i].salesperson.lastname
                    }
                    else
                    {
                        s_person = 'no name'
                    }
                    json[i].edit_btn = '<button class="btn btn-info  btn-xs edit_btn"><i class="fas fa-edit">&nbsp;</i>Edit</button>'
                    json[i].email_btn = '<button class="btn btn-info  btn-xs email_btn"><i class="fas fa-envelope-square"></i>&nbsp;</i>Email to ' + s_person + '</button>'
                    json[i].delete_btn = '<button class="btn btn-danger btn-xs delete_btn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>DELETE</button>'

                    json[i].actions = list_btn_template_start
                    json[i].actions += '<li><a href="#" class="edit_btn"><i class="fas fa-edit">&nbsp;</i>EDIT</a></li>'
                    json[i].actions += '<li><a href="#" class="email_btn"><i class="fas fa-check">&nbsp;</i>Email to ' + s_person + '</a></li>'

                    json[i].actions += '<li><a href="fulfillment_detail/' + json[i].id + '/0' + '" target="_blank">'
                    json[i].actions += '<i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a></li>'

                    json[i].actions += '<li><a href="fulfillment_detail/' + json[i].id + '/1' + '" target="_blank">'
                    json[i].actions += '<i class="fas fa-print">&nbsp;</i>Print</a></li>'

                    json[i].actions += '<li><a href="#" class="csv_btn"><i class="fas fa-file-csv"></i>&nbsp;CSV</a></li>'
                    json[i].actions += '<li class="divider"></li>'
                    json[i].actions += '<li><a href="#" class="delete_btn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete</a></li>'
                    json[i].actions += list_btn_template_end
                }

                return json
            }
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            let total = 0;
            for(let i = 0; i < data.length; i ++)
            {
                total       += parseFloat(data[i].total)
            }

            $( api.column( 5 ).footer() ).html(
                'Total:<br>$' + total.toFixed(2)
            );
        },
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
            { "data": "clientname" },
            { "data": "companyname" },
            { "data": "total" },
            { "data": "date" },
            { "data": "actions" },
        ],
        "order": [[1, 'asc']],
        'responsive': true
    });
}
var row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>';
    html += '<th>No</th>'
    html += '<th>Description</th>';
    html += '<th>Qty</th>';
    html += '<th>Units</th>';
    html += '<th>Unit_price</th>';
    html += '<th>CPU</th>';
    html += '<th>Discount</th>';
    html += '<th>Discount Type</th>';
    html += '<th>Extra Discount</th>';
    html += '<th>Sub Total</th>';
    html += '<th>Extended</th>';
 //   html += '<th>Tax</th>';
    html += '<th>Line Note</th>';
    html += '<th>Adjust Price</th>';
    html += '</thead>';

    html += "<tbody>";
    let less_discount = 0;
    let adjust_price = 0;
    let sub_total = 0;
    let strain_str = ''
    let p_typ_str  = ''
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].description + '</td>';
        html += '<td>' + data[i].qty + '</td>';
        html += '<td>' + data[i].units + '</td>';
        html += '<td>' + data[i].unit_price + '</td>';
        html += '<td>' + data[i].cpu + '</td>';
        html += '<td>' + data[i].discount + '</td>';
        html += '<td>' + data[i].discount_label + '</td>';
        html += '<td>' + data[i].e_discount + '</td>';
        html += '<td>' + data[i].sub_total + '</td>';
        html += '<td>' + data[i].less_discount + '</td>';
        html += '<td>' + data[i].tax_note + '</td>';
        html += '<td>' + data[i].adjust_price + '</td>';
        html += '</tr>';
    }
    if(d.pDiscount != null)
    {
        html += '<tr>'
        html += '<td colspan=6>' + d.pDiscount.note + '</td>'
        html += '<td colspan=6>' + d.pDiscount.value + '</td>'
        html += '<td colspan=6></td></tr>'
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
})
$('#invoice_table tbody').on('click', '.fulfillment_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id
    location.href='fulfillment_form?id=' + invoice_id
})
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
$('#invoice_table tbody').on('click', '.delete_btn', function () {
    var tr = $(this).closest('tr')
    var row = invoice_table.row( tr )
    var invoice_id = row.data().id

    swal({
        title: "Are You Sure",
        text: "You about to remove this Order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false
      }, function () {
        $.get({
            url:'_remove_order',
            data:'id=' + invoice_id + '&type=2',
            async:false,
            success:function(res)
            {
                $('#invoice_table').dataTable().fnDestroy()
                createTable($("#reservation").val());
            }
        })
      }
    )
})
$('#problematic_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = problematic_table.row( tr );

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
})
$('#problematic_table tbody').on('click', '.edit_btn', function () {
    var tr = $(this).closest('tr');
    var row = problematic_table.row( tr );
    var invoice_id = row.data().id;
    location.href="form?id=" + invoice_id;
})
$('#problematic_table tbody').on('click', '.email_btn', function () {
    var tr = $(this).closest('tr')
    var row = problematic_table.row( tr )
    let order_id = row.data().id
    let salesperson = row.data().salesperson
    if(salesperson == null)
    {
        swal("This Order has no sales person", "", "warning")
        return
    }
    swal({
        title: salesperson.email,
        text: "You are going to send the Email?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        $.post({
            url:'_send_problem_salesPerson',
            data:'id=' + order_id,
            success:(res) => {
                swal("Email sent Successfully", "", "success")
            },
            error:(e) => {
                swal("Sending Email failed", "", "warning")
            }
        })
    });
})
$('#problematic_table tbody').on('click', '.view_btn', function () {
    var tr = $(this).closest('tr')
    var row = problematic_table.row( tr )
    var invoice_id = row.data().id
    window.open('pending_detail?id='+invoice_id)
})
$('#problematic_table tbody').on('click', '.print_btn', function () {
    var tr = $(this).closest('tr')
    var row = problematic_table.row( tr )
    var invoice_id = row.data().id
    window.open('pending_detail?id='+invoice_id + '&print=1')
})
$('#problematic_table tbody').on('click', '.delete_btn', function () {
    var tr = $(this).closest('tr')
    var row = problematic_table.row( tr )
    var invoice_id = row.data().id

    swal({
        title: "Are You Sure",
        text: "You about to remove this Order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false
      }, function () {
        $.get({
            url:'_remove_order',
            data:'id=' + invoice_id + '&type=2',
            async:false,
            success:function(res)
            {
                $('#problematic_table').dataTable().fnDestroy()
                createProblematicTable($("#reservation").val());
            }
        })
      }
    )
})
$('#problematic_table tbody').on('click', '.csv_btn', function () {
    var tr = $(this).closest('tr');
    var row = problematic_table.row( tr );
    var data = Array();
    data[0] = row.data();

    convertToCSV(data).then(function(result){
        let filename = data[0].number + data[0].date;
        exportCSVfile(filename,result);
    });
})

let createInvRestockTable = () => {
    $.get({
        url: "../get_invrestock",
        success:(json) => {
            inv_restock_table_data = json
            drawInvRestockTable()
        }
    })
}
let drawInvRestockTable = () => {
    $('#inv_restock_table').dataTable().fnDestroy()
    let nVerified = 0
    let nApproved = 0
    for ( var i=0, ien=inv_restock_table_data.length ; i<ien ; i++ ) {
        let tmp = inv_restock_table_data[i]
        const is_verified = tmp.pass1 == 1?'checked':''
        const is_pass_1 = tmp.verified == 1?'':'disabled'
        const is_pass_2 = tmp.pass2 == 1?'checked':''
        if(tmp.pass1 == 1)
        {
            nVerified ++
        }
        if(tmp.pass2 == 1)
        {
            nApproved ++
        }
        
        let buttonLabel = 'Distro Verified(' + nVerified + ')'
        $('.btn-verified').text(buttonLabel)
        buttonLabel = 'Fullfilnent Approved(' + nApproved + ')'
        $('.btn-approved').text(buttonLabel)

        tmp.no = i + 1
        tmp.pass_1_button = '<input type="checkbox" class="chkVerified" ' + is_verified + ' />'
        tmp.pass_2_button = '<input type="checkbox" class="chkApproved" ' + is_pass_1 + ' ' + is_pass_2 + ' />'
    }
    inv_restock_table = $('#inv_restock_table').DataTable({
        "data":inv_restock_table_data,
        "columns":
        [
            { "data": "no" },
            { "data": "metrc_tag" },
            { "data": "orderLabel" },
            { "data": "retailer" },
            { "data": "strain" },
            { "data": "type" },
            { "data": "qty" },
            { "data": "reason" },
            { "data": "pass_1_button" },
            { "data": "pass_2_button" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "orderable": true, "targets": 1 },
            { "orderable": true, "targets": 2 },
            { "orderable": true, "targets": 3 },
            { "orderable": true, "targets": 4 },
            { "orderable": true, "targets": 5 },
            { "orderable": false, "targets": 6 },
            { "orderable": false, "targets": 7 },
            { "orderable": false, "targets": 8 },
            { "orderable": false, "targets": 9 },
            { "width": '10px', "targets": 0 },
            { "width": '40px', "targets": 2 },
            { "width": '50px', "targets": 8 },
            { "width": '50px', "targets": 9 },
        ],
        "order": [[0, 'asc']],
        'responsive': true
    });
}
$('#inv_restock_table').on('click','.chkVerified',function(){
    var tr = $(this).closest('tr')
    var row = inv_restock_table.row( tr )
    var no = row.data().no
    inv_restock_table_data[no - 1].pass1 = (inv_restock_table_data[no - 1].pass1 + 1) % 2
    if(inv_restock_table_data[no - 1].pass1 == 0)
    {
        inv_restock_table_data[no - 1].pass2 = 0
        inv_restock_table_data[no - 1].verified = 0
    }
    drawInvRestockTable()
})
$('#inv_restock_table').on('click','.chkApproved',function(){
    var tr = $(this).closest('tr')
    var row = inv_restock_table.row( tr )
    var no = row.data().no
    inv_restock_table_data[no - 1].pass2 = (inv_restock_table_data[no - 1].pass2 + 1) % 2
    drawInvRestockTable()
})
$('.btn-verified').click(() => {
    inv_restock_table_data.forEach((element, key) => {
        if(element.pass1 == 1)
        {
            inv_restock_table_data[key].verified = 1
        }
    });
    drawInvRestockTable()
})
$('.btn-approved').click(function(){
    let data = []
    inv_restock_table_data.forEach(element => {
        if(element.pass2 == 1)
        {
            data.push({id:element.id, type:element.i_type})
        }
    });
    
    if(data.length < 1)
    {
        swal("Warning", 'No Selected Packages to be approved', "warning")
        return false
    }

    swal({
        title: "Are You Sure",
        text: "Are You going to approve this Inventory Item?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
    }, function () {
        $.ajax({
            url:'../invrestock/approve',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify({data: data}),
            type:'post',
            async:false,
            success:(res) => {
                let word1 = parseInt(res.approved) < 2 ? '' : 's'
                let word2 = parseInt(res.approved) < 2 ? 'has' : 'have'
                swal('Success', '(' + res.approved + ') Package' + word1 + ' ' + word2 + ' been Approved', "success")
                inv_restock_table_data = res.inventory
                drawInvRestockTable()

            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
})
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
        $('#invoice_table').dataTable().fnDestroy()
        createTable($("#reservation").val());
    })

    $("#reservation_problematic").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
    }).on("change", function() {
        $('#problematic_table').dataTable().fnDestroy()
        createProblematicTable($("#reservation_problematic").val());
    })

    createTable($("#reservation").val());
    createProblematicTable($("#reservation_problematic").val());
    createInvRestockTable()
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
