let getBarcode = (id) => {
    $.ajax({
        data:'id=' + id,
        url:'_harvestTrackerBuilerBarcode',
        type:'post',
        async:false,
        success:(res) => {
            printBarcode(res)
        }
    })
}

let sendToHoldingInventory = (id) => {
    if(confirm('Send this to Holding Inventory'))
    {
        $.ajax({
            data:'id=' + id,
            url:'_harvestTrackerBuilerToHoldingInventory',
            type:'post',
            aysnc:false,
            success:(res) => {
                location.reload()
            }
        })
    }
}

let printBarcode = (res) => {
    new Promise((fulfill) => {
        $('#print_barcode_panel').html(res)
        fulfill()
    }).then(() => {
        let _style = '' +
        '<style type="text/css">' +
        'div,img {' +
        'width:100px;' +
        'height:50px;' +
        'text-align:center;' +
        '}' +
        '</style>'
        newWin= window.open("","_blank")
        newWin.document.write(_style + $('#print_barcode_panel').html())
        newWin.print()
        //newWin.close()
        return false
    })
}