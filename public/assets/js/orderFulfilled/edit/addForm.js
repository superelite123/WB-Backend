$('#addNewInventory').click(() => {
    let metrc = $('#newMetrcTag').val()
    if(metrc == '')
    {
        $('#errorNewMetrc').html("*Enter the Metrc Tag")
        $('#errorNewMetrc').css('border-color', 'red')
        return false
    }
    if(newInventory == null)
    {
        alert('Enter the Valid Metrc Tag')
        return false
    }
    newInventory.metrc = metrc
    newInventory.isNew = 1
    newInventory.isSingle = 1
    newInventory.unit_price  = parseFloat($('#newUnitPrice').val())
    newInventory = {...newInventory,...getDiscount($('#newDiscount').val())}
    newInventory.child_items = []
    newInventory = {...newInventory,...calcRowInfo(newInventory)}
    Inventory.push(newInventory)
    createTable()
    $('#newMetrcTag').val('')
    newInventory = null
    console.log(Inventory)
})

$('#newMetrcTag').keyup(function(){
    clearTimeout(typingTimer)
    typingTimer = setTimeout(scannedNewMetrc.bind(null,$(this).val()), doneTypingInterval)
})
$('#newMetrcTag').keydown(function(){
    clearTimeout(typingTimer)
})
let scannedNewMetrc = (metrc) => {
    let isDuplicate = false
    Inventory.forEach(element => {
        if(element.metrc == metrc)
        {
            $.growl.error({ message: "Duplicate Metrc Tag" }); 
            $('#errorNewMetrc').html("*This Metrc Tag already exist")
            $('#errorNewMetrc').css('border-color', 'red')
            isDuplicate = true
            return false
        }
        element.child_items.forEach(child => {
            if(child.metrc == metrc)
            {
                $.growl.error({ message: "Duplicate Metrc Tag" }); 
                isDuplicate = true
                return false
            }
        })
    })

    if(isDuplicate) return false

    _checkMetrcInfo(metrc).then((res) => {
        
        if($.isEmptyObject(res)){
            $.growl.error({ message: "Metrc Tag&nbsp;" + metrc + "&nbsp;doesn't exist" });
            $('#errorNewMetrc').html("*This Metrc Tag doesn't exist")
            $('#errorNewMetrc').css('border-color', 'red')
            newInventory = null
            return false
        }
        noticeMetrcScanning()
        $('#newStainLabel').html(res.strainLabel)
        $('#newPTypeLabel').html(res.pTypeLabel)
        $('#errorNewMetrc').html("")
        $('#errorNewMetrc').css('border-color', '')
        newInventory = res
    })
}

let _checkMetrcInfo = (metrc) => {
    return new Promise((fulfill) => {
        $.ajax({
            url:'_check_metrc_info/' + metrc,
            type:'get',
            success:(res) => {
                fulfill(res)
            },
            error:() => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
}