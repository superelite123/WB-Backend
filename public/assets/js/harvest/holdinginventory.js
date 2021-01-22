let harvest_id = null
let confirmDialog = (id) => {
    harvest_id = id
    checkUpc(harvest_id).then(() => {
        swal({
            title: "Are You Sure",
            text: "You will be sending to Finished Inventory?",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: false
            }, function () {
                fnSubmit()
        })
    }).catch((res) => {
        show_alert(res)
    })
    
}

let confirmVaultDialog = (id) => {
    harvest_id = id
    checkUpc(harvest_id).then(() => {
        swal({
            title: "Are You Sure",
            text: "You will be sending to Inventory 1/Vault?",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: false
            }, function () {
                fnVaultSubmit()
        })
    }).catch((res) => {
        show_alert(res)
    })
    
} 

let ask_target = () => {
    swal({
        title: "Success",
        text: "Harvest is sent to Finished Goods Successfully",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Go to Finished Goods List",
        cancelButtonText: "Stay Here",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
            location.href="fginventory"
        } else {
            location.reload()
        }
    });
    
}
let fnSubmit = () => {
    $.ajax({
        url:'sendHoldingToFG',
        data:'id=' + harvest_id,
        type:'post',
        async:false,
        success:(res) => {
            ask_target()
        }
    })  
}
let fnVaultSubmit = () => {
    $.ajax({
        url:'sendHoldingToVault',
        data:'id=' + harvest_id,
        type:'post',
        async:false,
        success:(res) => {
            ask_target()
        }
    })  
}
let show_alert = (res) => {
    switch(res)
    {
        case '1':
            alert('You have to assign a UPC')
            break;
        case '2':
            alert('You have to assign the Coa')
            break;
        case '3':
            alert('You have to assign the Coa and UPC')
            break;
        default:
            alert('Unknown Error')
    }
}

let checkUpc = (id) => {
    return new Promise((fulfill,reject) => {
        $.ajax({
            url:'_checkUpc',
            data:'id=' + id,
            type:'post',
            async:false,
            success:(res) => {
                if(res == '0') fulfill()
                else reject(res)
            }
        })
    })
}