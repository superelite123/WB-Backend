let inventory_tbody = $('#inventory_table tbody')
let doneTypingInterval = 10
let typingTimer
inventory_tbody.on("keyup", ".new-metrc", function(){
    let tr = $(this).parents('tr')
    big_cnt = parseInt(tr.attr('bigId'))
    sub_cnt = parseInt(tr.attr('subId'))
    metrc = $(this).val()
    console.log(metrc)
    clearTimeout(typingTimer)
    typingTimer = setTimeout(scannedMetrc.bind(null,big_cnt,sub_cnt,metrc), doneTypingInterval)
})
inventory_tbody.on("keydown", ".new-metrc", function(){
    clearTimeout(typingTimer)
})
let scannedMetrc = (bigId,subId,metrc) => {
    invoice.items[bigId].childItems[subId].newMetrc = metrc
}
let createTable = () => {
    let html = ''
    invoice.items.forEach((element,cnt1) => {
        html += '<tr>'
        html += '<td>' + (cnt1 + 1) + '</td>'
        html += '<td>' + element.strainLabel + '</td>'
        html += '<td>' + element.pTypeLabel + '</td>'
        html += '<td>' + element.asset.metrc_tag + '</td>'
        html += '<td>' + element.asset.qtyonhand + '</td>'
        html += '</tr>'
        if(element.childItems.length > 0)
        {
            html += '<tr><td colspan=5>'
            html += '<table class="table table-striped table-bordered">'
            html += '<thead><th>No</th><th>Metrc Tag</th><th>Input</th></thead>'
            html += '<tbody>'
            let cnt2 = 0
            element.childItems.forEach((item,cnt2) => {
                html += '<tr bigId="' + cnt1 + '" subId="' + cnt2 + '"><td>' + (cnt2 + 1) + '</td>'
                html += '<td>' + item.asset.metrc_tag  + '</td>'
                html += '<td><input type="text" class="form-control new-metrc"></td></tr>'
            })
            html += '</tbody></table></td></tr>'
        }
    })
    inventory_tbody.html(html)
}
$('#btnSubmit').click(() => {
    let fulfilled = true
    invoice.items.forEach((element,i) => {
        element.childItems.forEach((item,j) => {
            if(item.newMetrc == undefined || item.newMetrc == '')
            {
                fulfilled = false
            }
        })
    })

    if(!fulfilled)
    {
        alert('You need to update all Metrcs')
        return
    }
    swal({
        title: "Are You Sure",
        text: "Are you sure you want to delete invoice, stock will return to inventory",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
      }, function () {
        $.ajax({
            url:'_delete_store',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(invoice),
            type:'post',
            async:false,
            success:() => {
                location.href='../home'
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
})
$(function(){
    createTable()
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});