function confirmSendToDryWeight (id) {
    swal({
        title: "Are You Sure",
        text: "Send this Harvest to Dry Weight List?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
        }, function () {
            $.ajax({
                url:'_sendToDryWeightlist',
                data:'id=' + id,
                async:false,
                type:'post',
                success:(res) => {
                    if(res == 1)
                    {
                        ask_target()
                    }
                }
            })
    })
}

let ask_target = () => {
    swal({
        title: "Harvest Sent to Dry Weight List Successfully",
        text: "What do you want",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Go to Dry Weight Processing",
        cancelButtonText: "Return to Harvest Dynamics",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
            location.href="harvest/list_dry"
        } else {
            location.reload()
        }
    });
    
}