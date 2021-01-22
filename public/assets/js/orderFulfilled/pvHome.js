/**
 * Verification Tables
 */
var createVerificationTable = () => {
    $('#verification_table').dataTable().fnDestroy()
    verification_table = $('#verification_table').DataTable({
        "data":verifies,
        "dataSrc": function ( json ) {
            for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                json[i].no = i + 1
                json[i].number += '&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge badge-info">' + json[i].items.length + '</span>'
            }
            return json
        },
        "columns":
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs"><i class="glyphicon glyphicon-plus"></i></button>'
            },
            { "data": "no" },
            { "data": "number" },
            { "data": "number2" },
            { "data": "clientname" },
            { "data": "companyname" },
            { "data": "total" },
            { "data": "creationDate" },
            { "data": "date" },
        ],
        'scrollX':true
    });
}
$('#verification_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = verification_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs"><i class="glyphicon glyphicon-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format_v(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs"><i class="glyphicon glyphicon-minus"></i></button>')
    }
})
var row_details_format_v = (d) => {
    // `d` is the original data object for the row
    var data = d.items;
    data = verifies[d.no - 1].items
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>'
    html += '<th>No</th>'
    html += '<th>Type</th>'
    html += '<th>Collected amount</th>'
    html += '<th>Collected By</th>'
    html += '<th>Cash Bag Serial</th>'
    html += '<th>Sign</th>'
    html += '<th>Date</th>'
    html += '<th></th>'
    html += '</thead>'

    html += "<tbody>"
    for(var i = 0; i < data.length; i ++)
    {
        let type = data[i].type == 2?'Sub Total':'Tax'
        if(data[i].verfied != 1)
        {
            html += '<tr>'
            html += '<td>' + (i + 1) + '</td>'
            html += '<td>' + type + '</td>'
            html += '<td>$' + data[i].amount + '</td>'
            html += '<td>' + data[i].d_personame + '</td>'
            html += '<td>' + data[i].cash_serial + '</td>'
            html += '<td><img class="stockimg" username="' + data[i].d_personame + '" alt="" style="width:100px;height:100px" src="' + signFileUrl + '/' + data[i].sign_filename + '"></td>'
            html += '<td>' + data[i].created_at + '</td>'
            html += '<td><button class="btn btn-info btn-xs v_btn" b_no="' + (d.no - 1) + '" s_no="' + i + '" b_id="' + d.id + '" s_id="' + data[i].id + '"><i class="fas fa-edit">&nbsp;</i>Verify</button></td>'
            html += '</tr>'
        }

    }

    html += "</tbody></table>";
    return html;
}
$('#verification_table tbody').on('click', '.v_btn', function(e){
    let b_id = $(this).attr('b_id')
    let s_id = $(this).attr('s_id')
    const b_no = $(this).attr('b_no')
    const s_no = $(this).attr('s_no')
    let tr = $(this).closest('tr')
    let amount = parseFloat(prompt('Enter the Verification amount'))
    if(isNaN(amount))
    {
        alert('Enter the Correct Amount')
        return false
    }

    swal({
        title: 'Verify Payment',
        text: "You are going to verify the Invoice Payment?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: false
        },
        function () {
            $.get({
                url:'_verify_payment/' + b_id + '/' + s_id + '/' + amount,
                success:(res) => {
                    $.growl.notice({ message: "One Payment is Verified" })
                    tr.remove()
                    verifies[b_no].items[s_no].verfied = 1;
                },
                error:(e) => {
                    swal(e.statusText, e.responseJSON.message, "error")
                }
            })
    })
})
$('#verification_table tbody').on('click','.stockimg', function() {
    $('.imagepreview').attr('src', $(this).attr('src'))
    $('#signModalTitle').html($(this).attr('username') + "'s Signature")
    $('#imagemodal').modal('show')
})


$(function(){
    $("body").addClass('fixed')
    createVerificationTable()
    $('.select2').select2()
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
