$('#btnMerge').click(() => {
    let mergeElements = []
    let newInventory = []
    //save checked
    Inventory.forEach((element,index) => {
        if(element.checked == 1)
        {
            mergeElements.push(index)
        }
    })
    if(mergeElements.length < 2)
    {
        alert('Select the Inventory to merge')
        return false
    }
    let s = Inventory[mergeElements[0]].strain,p = Inventory[mergeElements[0]].pType
    let mergedCount = Inventory[mergeElements[0]].isSingle == 0?1:0
    let mergedIndex = mergedCount == 1?mergeElements[0]:-1
    for(let i = 1; i < mergeElements.length; i ++)
    {
        if(Inventory[mergeElements[i]].strain != s || Inventory[mergeElements[i]].pType != p)
        {
            alert('You can join Inventory with Strain and Product Category')
            return false
        }
        if(Inventory[mergeElements[i]].isSingle == 0)
        {
            mergedCount ++
            mergedIndex = mergeElements[i]
        }
    }

    if(mergedCount > 1)
    {
        alert('You can not merge two merged Inventory')
        return
    }
    let mergeMetrc = $('#mergeMetrcTag').val()
    let unit_price = parseFloat($('#mergeUnitPrice').val())
    let discount_id = $('#mergeDiscount').val()

    if(mergeMetrc == '')
    {
        alert('Enter the Metrc for New Merge Item')
        return false
    }

    if(mergedIndex == -1){
        unit_price = 0
        mergeElements.forEach(element => {
            unit_price += parseFloat(Inventory[element].unit_price)
        })
        //unit_price = unit_price / mergeElements.length
        console.log(unit_price)
        unit_price = unit_price.toFixed(2)
    }
    if(unit_price == 0 )
    {
        alert('You have to enter the unit price')
        return false
    }
    if(mergedIndex == -1)
    {
        Inventory.push({
            strain:s,
            pType:p,
            strainLabel:Inventory[mergeElements[0]].strainLabel,
            pTypeLabel:Inventory[mergeElements[0]].pTypeLabel,
            child_items:[],
            units:Inventory[mergeElements[0]].units * mergeElements.length,
            tax_note:'',
            isNew:1
        })
        mergedIndex = Inventory.length - 1
    }
    Inventory[mergedIndex].metrc = mergeMetrc
    Inventory[mergedIndex].unit_price = unit_price
    Inventory[mergedIndex] = {...Inventory[mergedIndex],...getDiscount(discount_id)}
    mergeElements.forEach(element => {
        if(element != mergedIndex)
        {
            Inventory[mergedIndex].child_items.push(Inventory[element])
            Inventory[mergedIndex].qty = Inventory[mergedIndex].child_items.length
        }
    })

    Inventory[mergedIndex].isSingle = 0
    Inventory[mergedIndex].mergeStatus = 0
    Inventory[mergedIndex].units = Inventory[mergedIndex].units / Inventory[mergedIndex].qty
    Inventory[mergedIndex] = {...Inventory[mergedIndex],...calcRowInfo(Inventory[mergedIndex])}
    Inventory[mergedIndex].checked = 0
    Inventory[mergedIndex].deleted = 0
    let checkedCnt = 0
    Inventory.forEach(element => {
        if(element.checked == 1)
        {
            checkedCnt ++
        }
    })
    for(let i = 0; i < checkedCnt; i ++)
    {
        Inventory.forEach((element,index,object) => {
            if(element.checked == 1)
            {
                Inventory.splice(index,1)
            }
        });
    }
    createTable()
})
inventory_table.on("keyup", ".new-metrc", function(){
    big_cnt =$(this).attr('bigId')
    sub_cnt = $(this).attr('subId')
    let metrc = $(this).val()
    clearTimeout(typingTimer)
    let t = sub_cnt == -1?2000:doneTypingInterval
    typingTimer = setTimeout(scannedMetrc.bind(null,big_cnt,sub_cnt,metrc), t)
})
inventory_table.on("keydown", ".new-metrc", function(){
    clearTimeout(typingTimer)
})
let scannedMetrc = (bigId,subId,metrc) => {
    if(subId != -1)
    {
        Inventory[bigId].child_items[subId].newMetrc = metrc
    }
    else{
        Inventory[bigId].metrc = metrc
        Inventory[bigId].mergeStatus = 0
        createTable()
    }
    noticeMetrcScanning()
}
var detailRows = [];
let createTable = () => {
    $('#inventory_table').dataTable().fnDestroy()
    let tableData = []
    Inventory.forEach((element,index) => {
        element.no              = index + 1
        element.DT_RowId = index + 1
        let checked = element.checked == 1?'checked':0
        element.chk             = '<input class="chk" type="checkbox" ' + checked + ' />'
        element.btn_remove      = "<button class='btn btn-danger btn-xs btn-remove-row'>"
        if(element.child_items.length == 0)
        {
            element.btn_remove     += "<i class='fa fa-undo' aria-hidden=true'>&nbsp;</i>Restock</button>"
        }
        else
        {
            element.btn_remove     += "<i class='fa fa-undo' aria-hidden=true'>&nbsp;</i>Parent Restock</button>"
        }
        element.btn_edit        = "<button class='btn btn-info btn-xs btn-edit-row'>"
        element.btn_edit       += "<i class='fas fa-edit'>&nbsp;</i>edit</button>"
        element.inputNewMetrc   = ''
        element.btnPlus         = '<button class="btn btn-info btn-xs"><i class="glyphicon glyphicon-plus"></i></button>'
        element.btnMinus        = '<button class="btn btn-info btn-xs"><i class="glyphicon glyphicon-minus"></i></button>'
        if(element.child_items.length == 0)
        {
            element.btnPlus = ''
            element.btnMinus = ''
            element.inputNewMetrc = ''
        }
        if(element.mergeStatus == 1)
        {
            element.inputNewMetrc = '<input bigId="' + (index) + '" subId="-1" class="form-control new-metrc" single="1" value="" />'
        }
        if(element.deleted != 1)
        {
            tableData.push(element)
        }
    })
    invoice_table = $('#inventory_table').DataTable({
        "data":tableData,
        rowId: 'DT_RowId',
        "columns":
        [
            {
                "className": 'details-control',
                "orderable": false,
                "data":      "btnPlus"
            },
            { "data": "chk" },
            { "data": "no" },
            { "data": "strainLabel", },
            { "data": "pTypeLabel"},
            { "data": "metrc" },
            { "data": "qty" },
            { "data": "units" },
            { "data": "weight" },
            { "data": "unit_price" },
            { "data": "cpu" },
            { "data": "discount" },
            { "data": "discount_label" },
            { "data": "base_price" },
            { "data": "extended" },
            { "data": "tax_note" },
            { "data": "adjust_price" },
            { "data": "inputNewMetrc" },
            { "data": "btn_edit" },
            { "data": "btn_remove" },
        ],
        "rowCallback": function( row, data, dataIndex){
            if(data.mergeStatus == 1)
            {
                $(row).css('color','#00c0ef');
            }
            if(data.mergeStatus == 2)
            {
                $(row).css('background-color','#00c0ef');
            }
        },
        "order": [[1, 'asc']],
        "scrollX":true,
    })
}
inventory_table.on('click', '.btn-remove-row', function () {
    let tr = $(this).closest('tr');
    let row = invoice_table.row( tr );
    let data = row.data()
    let flag = true
    if(data.mergeStatus == 1 && data.isSingle == 0)
    {
        alert('This Merged Inventory has been change\n You need to undo changed item\n and then retry Restock')
        return
    }

    if(data.isSingle == 0)
    {
        findObjWithAttr(Inventory,'no',data.no).child_items.forEach(element => {
            if(element.newMetrc == '')
                flag = false
        })
        if(data.isNew == 1)
            flag = true
    }
    if(!flag)
    {
        alert('Enter the All Metrc Tags')
        return false
    }
    if(data.isNew == 1)
        Inventory.splice(findIndexWithAttr(Inventory,'no',data.no),1)
    else
        Inventory[findIndexWithAttr(Inventory,'no',data.no)].deleted = 1

    $.growl({ title: "Restocked One Inventory", message: "You restocked one Inventory in this Order<br>You can restore that by refresh" });
    createTable()

})
inventory_table.on('click', '.btn-edit-row', function () {
    let tr = $(this).closest('tr');
    let row = invoice_table.row( tr );
    let data = row.data()
    selectedInventory = findIndexWithAttr(Inventory,'no',data.no)
    $('#editMetrcTag').val(data.metrc)
    $('#editUnitPrice').val(data.unit_price)
    $('#editDiscount').val(data.discount_id).change()
    $('#modal-edit-row').modal('show')
})
inventory_table.on('click', '.btn-remove-child', function () {
    let tr = $(this).parents('tr')
    let bigId = tr.attr('bigId')
    let subId = tr.attr('subId')
    let child = Inventory[bigId].child_items[subId]
    if(child.isNew == 1)
    {
        Inventory[bigId].child_items.splice(subId,1)
    }
    else
    {
        if(child.newMetrc == '')
        {
            alert('Enter the New Metrc Tag to delete this item')
            return
        }
        //set as deleted
        Inventory[bigId].child_items[subId].deleted = 1
        //prevent undefined error
        Inventory[bigId].child_items[subId].child_items = []
        //set as single item
        Inventory[bigId].child_items[subId].isSingle = 1
        //assign to end of inventory
        Inventory[Inventory.length] = Inventory[bigId].child_items[subId]
        //remove from child
        Inventory[bigId].child_items.splice(subId,1)
    }
    Inventory[bigId].units = Inventory[bigId].units / Inventory[bigId].qty
    Inventory[bigId].qty = Inventory[bigId].child_items.length
    Inventory[bigId].mergeStatus = 1

    Inventory[bigId] = {...Inventory[bigId],...calcRowInfo(Inventory[bigId])}
    if(Inventory[bigId].child_items.length == 1)
    {
        Inventory[bigId] = {...Inventory[bigId],...Inventory[bigId].child_items[0]}
        Inventory[bigId].isSingle       = 1
        Inventory[bigId].mergeStatus    = 0
        Inventory[bigId].child_items    = []
    }
    createTable()
})
inventory_table.on('click', '.chk', function () {
    let tr = $(this).closest('tr')
    let row = invoice_table.row( tr )

    Inventory[findIndexWithAttr(Inventory,'no',row.data().no)].checked = $(this).prop('checked') == true?1:0
})
inventory_table.on('click', 'td.details-control', function () {
    let tr = $(this).closest('tr');
    let row = invoice_table.row( tr );
    var idx = $.inArray( tr.attr('id'), detailRows );
    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html(row.data().btnPlus)
        detailRows.splice( idx, 1 );
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show();
        tr.addClass('shown');
        $(this).html(row.data().btnMinus)
        if ( idx === -1 ) {
            detailRows.push( tr.attr('id') );
        }
    }
})
let row_details_format = (d) => {
    let html = ''
    html += '<table class="table table-striped table-bordered">'
    html += '<thead><th>No</th><th>Metrc Tag</th><th>New Metrc for Delete</th><th>Input</th></thead>'
    html += '<tbody>'
    let items = d.child_items
    items.forEach((item,cnt) => {
        if(item.deleted == 1)
        {
            html += '<tr style="color:red" bigId="' + (d.no - 1) + '" subId="' + cnt + '">'
        }
        else
        {
            html += '<tr bigId="' + (d.no - 1) + '" subId="' + cnt + '">'
        }
        html += '<td>' + (parseFloat(cnt) + 1) + '</td>'
        html += '<td>' + item.metrc  + '</td>'
        html += '<td>' + item.newMetrc  + '</td>'
        html += '<td><input bigId="' + (d.no - 1) + '" subId="' + cnt + '" type="text" single="0" class="form-control new-metrc"></td>'
        html += '<td><button class="btn btn-danger btn-xs btn-remove-child"><i class="fas fa-undo">&nbsp;</i>restock</button></td>'
        html += '</tr>'
    })
    html += '</tbody></table>'
    return html
}
$('#chkTax').click(() => {
    topInfo.tax_allow = $('#chkTax').prop('checked') == true?1:0
})
$('#btnSaveRowInfo').click(() => {
    let metrc = $('#editMetrcTag').val()
    let unit_price = $('#editUnitPrice').val()
    let discount_id = $('#editDiscount').val()

    Inventory[selectedInventory].unit_price = unit_price
    Inventory[selectedInventory].metrc      = metrc
    Inventory[selectedInventory] = {...Inventory[selectedInventory],...getDiscount(discount_id)}
    Inventory[selectedInventory].units = Inventory[selectedInventory].units / Inventory[selectedInventory].qty
    Inventory[selectedInventory] = {...Inventory[selectedInventory],...calcRowInfo(Inventory[selectedInventory])}
    $('#modal-edit-row').modal('hide')
    createTable()
})
$('#btnSubmit').click(() => {
    let fulfilled = true
    Inventory.forEach((element,i) => {
        if(element.isSingle == 0 && element.mergeStatus == 1)
        {
            alert('You have to enter the merged Metrc Tag in blue line')
            fulfilled = false
        }
    })
    if(!fulfilled) return false

    swal({
        title: "Are You Sure",
        text: "Are You going to update the Order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
      }, function () {
        $.ajax({
            url:'_store',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify({items:Inventory,id:Invoice.id}),
            type:'post',
            async:false,
            success:(res) => {
                location.reload()
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    });
})
$(function(){
    createTable()
    $('.select2').select2()
  .ajaxStart(function () {
    $loading.show();
  })
  .ajaxStop(function () {
    $loading.hide();
  })
})
$(document).ajaxStart(function() { Pace.restart(); });
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
