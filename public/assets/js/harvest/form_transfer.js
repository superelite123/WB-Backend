let items = []

$(".makeBtn").click(() => {
    if(items.length == 0)
    {
        swal("Warning!", "No Data to Move", "info")
        return
    }

    swal({
        title: "Are You Sure",
        text: "Do You really Move this Items to " + $("#rooms option:selected").text() + "?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
        }, function () {
            $.ajax({
                url:'store_transfer',
                headers:{"content-type" : "application/json"},
                data:JSON.stringify({items:items}),
                type:'post',
                async:false,
                success:(res) => {
                    ask_target()
                }
            })
    })
})

let ask_target = () => {
    swal({
        title: "Transfer successed!",
        text: "What do you want",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Go to Transfer History",
        cancelButtonText: "New Transfer",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
            location.href="transfer_history"
        } else {
            location.reload()
        }
    });
    
}

$("#rooms").change(() => {
    $("#barcode").focus()
    items.forEach(element => {
        element.room_id = $("#rooms").val();
    })
    createTempTable()
})
$("#add_row").click(function() {
    let room_id = $("#rooms").val()
    let barcode = $("#barcode").val()
    
    if(room_id == 0)
    {
        swal("Warning!", "Select the Room", "info")
        return
    }

    if(barcode == "")
    {
        swal("Warning!", "Scan the Barcode", "info")
        return
    }
    if(!check_unique(barcode))
    {
        swal("Warning!", "This item already is added", "info")
        return
    }
    //get item from barcode
    $.ajax({
        url:'get_item_from_barcode',
        type:'post',
        async:false,
        data:'barcode=' + barcode,
        success:(res) => {
            console.log(res)
            if(res.success == '1')
            {
                add_item(res)
            }
            
            if(res.success == '0')
            {
                show_no_search_result(barcode)
            }
            $("#barcode").val('')
        },
        error:(e) => {
            alert('the error occured')
        }

    })
})

let check_unique = (barcode) => {
    let flag = true;
    items.forEach(element => {
        if(element.barcode == barcode)
        flag = false
    })

    return flag
}

let add_item = (item) => {

    let temp = {
        barcode:$("#barcode").val(),
        record_id:item.id,
        room_id:$("#rooms").val(),
        type:item.type,
        type_name:item.type_name,
        current_location:item.current_location,
    }

    items.push(temp);

    createTempTable()
}

let createTempTable = () => {
    let html = ""
    let cnt = 0
    items.forEach(element => {
        html += "<tr>"
        html += "<td>" + (cnt + 1) + "</td>"
        html += "<td>"+element.barcode + "</td>"
        html += "<td>"+element.type_name + "</td>"
        html += "<td>"+element.current_location + "</td>"
        html += "<td>"+$("#rooms option:selected").text() + "</td>"
        html += "<td><button class='btn btn-danger remove_btn' id='" + cnt  +"'>Remove</button></td>"
        html += "</tr>"
        cnt ++
    })

    $("#temp_table tbody").html(html)
}
$("#temp_table > tbody").on("click", ".remove_btn", function(){
    let id = $(this).attr('id')
    items.splice(id,1)
    createTempTable()
})

//---------------alert related----------------------------
let show_no_search_result = (barcode) => {
    swal("Warning!", "There is no record for " + barcode, "info")
    return
}
//---------------/.alert related----------------------------
$(function(){
    $("body").addClass('fixed');
    $('.select2').select2();
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});