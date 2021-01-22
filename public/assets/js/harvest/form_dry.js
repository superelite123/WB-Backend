let harvest_id = windowvar.id
let input_weight = $("#weight")
let harvest_items = []
let cnt_items_toWeight = windowvar.item_count
let cnt_user_entered = 0
let mode = windowvar.mode
let total_weight = windowvar.total_weight

$(".makeBtn").click(function(){

    let post_data = {
        id:harvest_id,
        items:harvest_items,
        mode:mode
    }
    let t = 0;
    for(let i = 0; i < post_data.items.length; i ++)
        t += parseFloat(post_data.items[i].weight)
    
    if(t >= total_weight)
    {
        swal("Dry Weight can not be more than wet weight", "", "info")
        return;
    }
    if(post_data.items.length == 0)
    {
        swal("There are no items to re weight", "", "info")
        return;
    }

    if(cnt_items_toWeight - cnt_user_entered > 0)
    {
        swal("You need to enter all weights", "", "info")
        return;
    }

    swal({
        title: "Are You Sure",
        text: "You are about to Process this Harvest?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
        }, function () {
            submit_harvest(post_data)
        })

})
let submit_harvest = (post_data) => {
    $.ajax({
        url:'store_dry',
        headers:{"content-type" : "application/json"},
        data:JSON.stringify(post_data),
        type:'post',
        async:false,
        success:(res) => {
            ask_target()
        },
        error:() => {
            swal("A problem has occured while re weighting the Harvest", "", "danger")
        }
    })
}
let ask_target = () => {
    swal({
        title: "Harvest is Sent to Curing Successfully",
        text: "What do you want",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Go to Curing List",
        cancelButtonText: "Go to Dry Weight List",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
            location.href="curning"
        } else {
            location.href="list_dry"
        }
    });
    
}
$("#add_row").click(function(e) {
    e.preventDefault();
    var weight = input_weight.val()

    if(cnt_items_toWeight - cnt_user_entered-1 == 0)
    {
        //swal("Entering the Plant finished", "", "info")
    }

    if(weight == 0)
    {
        swal("can't enter the zero value", "", "info")
        return
    }

    harvest_items.push({weight:weight});

    cnt_user_entered ++
    $("#session_count").html(cnt_user_entered)
    $("#item_count").html(cnt_items_toWeight - cnt_user_entered)
    
    CreateTable();
    input_weight.val('');
    
    toggle_form(cnt_items_toWeight - cnt_user_entered == 0)
})

let toggle_form = (mode) => {
    input_weight.prop('disabled',mode)
    $("#add_row").prop('disabled',mode)
}

let CreateTable = () => {
    let html = "";
    
    let element;
    
    for(let i = harvest_items.length - 1; i >= 0 ; i --)
    {
        element = harvest_items[i]

        html += "<tr item_id='" + i +"'>"
        html += "<td>" + (i+1) + "</td>"
        html += "<td>"+element.weight+"</td>"
        html += "<td><button class='btn btn-info btn-xs btn_item_edit'>edit</button></td>"
        html += "<td><button class='btn btn-danger btn-xs btn_item_remove'>remove</button></td>"
        html += "</tr>"
    }

    $(".data-table tbody").html(html);
}

$("#temp_table > tbody").on("click", ".btn_item_remove", function(){
    let item_id = parseInt($(this).parents('tr').attr('item_id'))
    harvest_items.splice(item_id,1)
    CreateTable()
    $(this).parents("tr").remove()
    cnt_user_entered --
    cnt_items_toWeight ++
    toggle_form(false)
})

$("#temp_table > tbody").on("click", ".btn_item_edit", function(){
    let id = $(this).parents("tr").attr('item_id')
    let row_data = harvest_items[id];
    $(this).parents("tr").find("td:eq(1)").html('<input type="number" name="edit_item_weight" value="'+row_data.weight+'">');
    $(this).parents("tr").find("td:eq(2)").prepend("<button class='btn btn-info btn-xs btn_item_update'>Update</button>");
    $(this).parents("tr").find("td:eq(3)").prepend("<button class='btn btn-warning btn-xs btn_item_cancel'>Cancel</button>");
    
    $(this).toggle();
    $(this).parents('tr').find(".btn_item_remove").toggle();
})

$("#temp_table > tbody").on('click', '.btn_item_update',function(){
    let tr = $(this).parents('tr');
    let id = parseInt(tr.attr('item_id'));

    var data = {};
    data.weight = parseFloat(tr.find('input[name="edit_item_weight"]').val())

    if(data.weight == 0)
    {
        swal("can't enter the zero value", "", "info")
        return
    }
    

    harvest_items[id] = data;
    update_cancel_row(tr,data);
})

$("#temp_table > tbody").on('click','.btn_item_cancel',function(){
    let tr = $(this).parents('tr');
    let id = parseInt(tr.attr('item_id'));

    var data = harvest_items[id];
    update_cancel_row(tr,data);
})

let update_cancel_row = (tr,data) => {
    tr.find('td:eq(1)').text(data.weight);

    tr.find(".btn_item_update").remove();
    tr.find(".btn_item_cancel").remove();
    tr.find(".btn_item_edit").toggle();
    tr.find(".btn_item_remove").toggle();
}

input_weight.keyup(function(event) {
    if (event.keyCode === 13) {
     event.preventDefault();
     $("#add_row").click();
    }
});


$(function(){
    $("body").addClass('fixed');
    $('.select2').select2();
})


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});