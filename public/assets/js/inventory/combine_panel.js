let invoice_table = null
let combine_table = null
let combineData = []
$('#btn_combine').click(() => {
    let diff_strain = false;
    let combine_data = combine_table.data()
    if(combine_data.length < 2)
    {
        $.growl.error({ message: "Combine Data is empty" })
        return false;
    }
    let first_strain = combine_data[0].strainname
    let data = []
    let strainEqualCheck = true
    combine_data.each(function(element){
        if(first_strain != element.strainname)
        {
            strainEqualCheck = false
        }
        data.push(element)
    });
    if(!strainEqualCheck)
    {
        $.growl.error({ message: "Strain is need to same about all items" })
        return false
    }
    let metrc = $('#metrc').val()
    if(metrc == '')
    {
        $.growl.error({ message: "Enter the Metrc Tag" })
        return 
    }
    let p_type = $('#p_type').val()
    
    let post_data = {
        data:data,
        metrc:metrc,
        p_type:p_type
    }

    swal({
        title: "Are You Sure",
        text: "Are You going to combine Inventory Items?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        submit_combine(post_data);
    });
})

let submit_combine = (data) => {
    $.ajax({
        url:'_combine',
        type:'post',
        async:false,
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(data),
        success:(res) => {
            swal.close()
            combineData = []
            createCombineTable()
            createInventoryTable()
            $.growl.notice({ message: "Success on Combine" })
        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })
}

let createInventoryTable = () => {
    $('#invoice_table').dataTable().fnDestroy()
    let date_range = $("#reservation").val().split(' ')
    invoice_table = $('#invoice_table').DataTable({
        "processing":true,
        "serverSide":true,
        "ajax":{
            "url":"getInventory",
            "dataType":"json",
            "type":"POST",
            "data":{s_date:date_range[0],e_date:date_range[2]},
            "dataSrc": function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                    
                    json.data[i]['rChk'] = '<button class="btn btn-info btn-sm btn_combine"><i class="fas fa-arrow-down"></i></button>';
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
$('#invoice_table tbody').on('click', '.btn_combine', function () {
    let tr   = $(this).closest('tr')
    let row  = invoice_table.row( tr )
    let data = row.data()
    row.remove().draw()
    let flag = true
    combineData.forEach(element => {
        if(element.metrc_tag == data.metrc_tag)
        {
            alert('Same Inventory');
            flag = false
        }
    })
    if(!flag)
    return false
    data.no = combineData.length
    combineData.push(data)
    createCombineTable()
})
let createCombineTable = () => {
    $('#combine_table').dataTable().fnDestroy()
    combine_table = $('#combine_table').DataTable({
        "data":combineData,
        "columns": 
        [
            { "data": "hBatch" }, 
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
        'scrollX':true,
        'columnDefs': [{
            'targets': 0,
            'searchable':false,
            'orderable':false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<button class="btn btn-info btn-sm btn_uncombine"><i class="fas fa-arrow-up"></i></button>';
            }
         }],
        "order": [[0, 'asc']],
        'responsive': true
    });
}
$('#combine_table tbody').on('click', '.btn_uncombine', function () {
    let tr   = $(this).closest('tr')
    let row  = combine_table.row( tr )
    let data = row.data()
    
    combineData.splice(data.no,1)
    createCombineTable()
    invoice_table.row.add(data).draw().order()
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
    createCombineTable()
    $('.select2').select2();
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});