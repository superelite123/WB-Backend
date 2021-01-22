let inventoryTable = null
let splitTable = null
let selItem = null
let splitData = []
const addRow = () => {
    if(selItem == null)
    {
        alert("Select the Item to split")
        return false;
    }
    let data = {}
    data.h_batch = selItem.hBatch
    data.metrc_tag = $('#inputMetrc').val()
    data.qtyonhand   = parseFloat($('#inputQty').val())
    data.weight = parseFloat($('#inputWeight').val())
    data.p_type = $('#inputPType').val()
    data.p_type_lbl = $('#inputPType option:selected').text()
    data.strain_lbl = selItem.strain
    if(!validation_input(data))
    {
        return false
    }

    let sum_weight = data.weight
    let flag = true
    splitData.forEach(element => {
        if(element.metrc_tag == data.metrc_tag)
        {
            alert('This Metrc Tag already added')
            flag = false
        }
        sum_weight += element.weight
    });
    if(!flag) return false
    if(sum_weight > selItem.weight)
    {
        alert('Sum Weight is over Parent Weight')
        return false
    }
    data.no = splitData.length
    splitData.push(data)
    // createSplitTable()
}
let validation_input = async (data) => {
    let result = true
    if(data.metrc_tag == '')
    {
            $('#metrc_error').html('*Enter the Metrc Tag')
            $('#metrc').css('border-color', 'red')
            $('#metrc').focus()
            result = false
    }
    else
    {
        $('#metrc_error').html('')
        $('#metrc').css('border-color', '')
    }
    if(isNaN(data.qtyonhand))
    {
            $('#qty_error').html('*Enter the Quantity')
            $('#qty').css('border-color', 'red')
            $('#qty').focus()
            result = false
    }
    else
    {
        $('#qty_error').html('')
        $('#qty').css('border-color', '')
    }
    if(isNaN(data.weight))
    {
            $('#weight_error').html('*Enter the Weight')
            $('#weight').css('border-color', 'red')
            $('#weight').focus()
            result = false
    }
    else
    {
        $('#weight_error').html('')
        $('#weight').css('border-color', '')
    }
    await $.ajax({
        url:'_check_metrc_duplicate',
        data:'metrc=' + data.metrc_tag,
        type:'get',
        success:(res) => {
            if(res == '0'){
                return true
            }
            else{
                $.growl.error({ message: "Duplicate Metrc Tag" }); 
                $('#metrc_error').html('*Metrc Tag Duplicate')
                $('#metrc').css('border-color', 'red')
                $('#metrc').focus()
                return false
            }
        },
        error:(e) => {
            return false
        }
    })
}
let createInventoryTable = () => {
    $('#inventoryTable').dataTable().fnDestroy()
    let date_range = $("#reservation").val().split(' ')
    inventoryTable = $('#inventoryTable').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"getInventory",
            "dataType":"json",
            "type":"POST",
            "data":{s_date:date_range[0],e_date:date_range[2]},
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                  json.data[i]['rChk'] = '<input type="radio" class="split_parent" name="split_parent">';
                }
                return json.data;
            }
        },
        "columns": [
            { "data": "rChk" }, 
            { "data": "hBatch" }, 
            { "data": "metrc_tag" }, 
            { "data": "strain" }, 
            { "data": "pType" },
            { "data": "upc" },
            { "data": "coa" },
            { "data": "qty" }, 
            { "data": "weight" },
            { "data": "um" },
            { "data": "hDate" },
        ],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
        ],
        'scrollX':true
    });
}
let createSplitTable = () => {
    $('#splitTable').dataTable().fnDestroy()
    splitTable = $('#splitTable').DataTable({
        "data":splitData,
        "columns": 
        [
            { "data": "h_batch" },
            { "data": "h_batch" }, 
            { "data": "metrc_tag" }, 
            { "data": "strain_lbl" }, 
            { "data": "p_type_lbl" },
            { "data": "qtyonhand" }, 
            { "data": "weight" },
        ],
        'columnDefs': [{
            'targets': 0,
            'searchable':false,
            'orderable':false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<button class="btn btn-danger btn-sm btnRemoveRow"><i class="fas fa-trash-alt"></i></button>';
            }
         }],
        "order": [[0, 'asc']],
        'responsive': true
    });
}
$('#inventoryTable tbody').on('click', '.split_parent', function () {
    let tr   = $(this).closest('tr')
    let row  = inventoryTable.row( tr )
    let data = row.data()
    // if(selItem != null && data.strain != selItem.strain && splitData.data > 0)
    // {
    //     if(!confirm('If you select another Inventory\n we can not save present work'))
    //     {
    //         return false
    //     }
    // }
    // if(selItem != null && data.strain != selItem.strain)
    //     splitData = []
    // selItem = data
    $('#fgasset_id').val(data.fgasset_id)
    $('#i_type').val(data.i_type)
    $('#splitBatchId').html(data.hBatch)
    $('#splitMetrc').html(data.metrc_tag)
    $('#splitStrain').html(data.strain)
    $('#splitType').html(data.pType)
    $('#splitQty').html(data.qty)
    $('#splitWeight').html(data.weight)
    createSplitTable()
})
$('#splitTable tbody').on('click', '.btnRemoveRow', function () {
    let tr   = $(this).closest('tr')
    let row  = splitTable.row( tr )
    let data = row.data()
    splitData.splice(data.no,1)
    console.log(splitData)
    createSplitTable()
})
function checkForm()
{
    if($('#fgasset_id').val() == 0)
    {
        alert("Select the Item to split")
        return false;
    }

    return true;
}
$('#btnSplit').click(() => {
    // if(!addRow())
    // {
    //     return false
    // }
    // let postData = {
    //     fgasset_id:selItem.fgasset_id,
    //     i_type:selItem.i_type,
    //     splitData:splitData
    // }
    // $.ajax({
    //     url:"_split",
    //     headers:{"content-type" : "application/json"},
    //     data: JSON.stringify(postData),
    //     type:'post',
    //     success:(res) => {
    //         $.growl.notice({ message: "Success on Split" })
    //         createInventoryTable()
    //         splitData = []
    //         createSplitTable()
    //     },
    //     error:(e) => {
    //         swal(e.statusText, e.responseJSON.message, "error")
    //     }
    // })
})
$(() => {
    $("#reservation").daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: SD.s_date,
        endDate: SD.e_date
      }).on("change", function() {
        createInventoryTable()
    })
    createInventoryTable()
    //createSplitTable()
    $('.select2').select2();
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});