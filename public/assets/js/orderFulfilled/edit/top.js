$('#updateTopInfo').click(() => {
    let clientId = $('#clients').val()
    let distributorId = $('#distributors').val()
    let note = $('#orderNote').val()

    if(clientId == 0)
    {
        alert('You need to select the client')
        return false
    }
    topInfo.clientId = clientId
    topInfo.distributorId = distributorId
    topInfo.note = note
    swal({
        title: "Are You Sure",
        text: "Are You going to update the Sales Order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
      }, function () {
        $.post({
            url:'_update_top_info',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(topInfo),
            success:(res) => {
                $('.top-panel').html(res)
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
})
