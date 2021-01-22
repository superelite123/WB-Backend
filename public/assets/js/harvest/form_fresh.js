//from server
let tare = parseFloat(windowvar.tare)
let harvest_id = windowvar.harvest_id
let batch_total_weight = windowvar.total_weight
let item_type = windowvar.item_type

let items = []
let tbl_main_table = $("#containerTable tbody")

//click Submit Button
$(".makeBtn").click(function(){

    if(!items[items.length - 1].printed)
    {
        show_input_validation('You have to print last record!')
        return
    }

    swal({
        title: "Are You Sure",
        text: "You are about to Process this Curing?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
        }, function () {
            submit_curing()
    })
})
//Submit
let submit_curing = () => {

    let submit_data = {
        id:harvest_id,
        items:items
    }

    submit_data.items.forEach(element => {
        delete element.printed
    })

    $.ajax({
        url:'store_fresh',
        type:'post',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(submit_data),
        async:false,
        success:(res) => {
            if(res == 1)
                ask_target()
                
        },
        error:(e) => {
            swal("A problem has occured while processing the Harvest", "", "danger")
            console.log(e)
        }
    })
}

let ask_target = () => {
    swal({
        title: "Fresh Item is processed Successfully",
        text: "What do you want",
        type: "success",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Go to Inventory on Hold",
        cancelButtonText: "Go to Harvest List",
        closeOnConfirm: false,
        closeOnCancel: false
      },
      function(isConfirm) {
        if (isConfirm) {
            location.href="/holdingInventory"
        } else {
            location.href="list"
        }
    });
    
}

let create_table = () => {
    let html = ""
    if(items.length == 0)
    {
        html = "<tr><td colspan='7'>No Inserted Data!</td></tr>"
    }
    else
    {
        let cnt = 1
        items.forEach(item => {
            html += "<tr item_id='" + (cnt - 1) + "'>"
            html += "<td>" + cnt + "</td>"
            html += "<td>" + item.weight + "</td>"
            html += "<td>" + item.tare + "</td>"
            html += "<td>" + item.metrc + "</td>"
            if(item.printed)
                html += "<td><button class='btn btn-success print_btn'><i class='fas fa-check'></i>&nbsp;Print</td>"
            else
                html += "<td><button class='btn btn-info print_btn'><i class='fas fa-print'></i>&nbsp;Print</td>"
            html += "<td><button class='btn btn-danger remove_btn'><i class='fas fa-trash-alt'></i>&nbsp;Trash</td>"
            html += "</tr>"
            cnt ++
        })
    }

    tbl_main_table.html(html)
    //set scroll to bottom
    $(".table_panel").scrollTop($('.table_panel').prop("scrollHeight"))
    $(".close").click()
    $("#weight").focus()
}

//clear input field
let clear_input_field = () => {
    $("#weight").val('')
    $("#metrc").val('')
}
//add row
$("#add_row").click(() => {
    let temp = {
        weight:parseFloat($("#weight").val()),
        tare:tare,
        metrc:$("#metrc").val(),
        printed:false,
    }
    //check weight
    if(isNaN(temp.weight))
    {
        show_input_validation('Enter the Validate Weight!')
        return
    }
    
    //check metrc
    if(temp.metrc == "")
    {
        show_input_validation('Enter the Metrc Tag!')
        return
    }

    //check metrc unique
    let flag = true
    items.forEach(item => {
        if(item.metrc == temp.metrc)
        {
            flag = false
        }
    })

    if(!flag)
    {
        show_input_validation('This Metrc Tag already exist!')
        return
    }

    //check last's printed
    if(items.length >= 1)
    {
        if(!items[items.length - 1].printed)
        {
            show_input_validation('You have to print previous record!')
            return
        }
    }
    //check total weight
    let present_total_weight = 0;
    items.forEach(item => {
        present_total_weight += item.weight
    })
    present_total_weight += temp.weight
    if(present_total_weight > batch_total_weight)
    {
        show_input_validation('Over Total Weight!')
        return
    }

    //push one!
    items.push(temp);
    //redraw table
    create_table()
    clear_input_field()
    $("#weight").focus()
})
$("#weight").keyup(function(e) {
    if (e.keyCode === 13) {
        e.preventDefault()
        $("#metrc").focus()
    }
})
$("#metrc").keyup(function(e) {
    if (e.keyCode === 13) {
        e.preventDefault()
        $("#add_row").click()
    }
})
//print,remove button click listener
    $('#containerTable tbody').on('click','.print_btn',function(){
        let tr = $(this).parents('tr')
        let id = parseInt(tr.attr('item_id'));
        let barcode_data = {
            batch_id:'Batch Harvest ID:' + $(".sp_batchId").text(),
            strain:'Strain:' + $(".sp_strain").text(),
            metrc:'Metrc Tag:' + items[id].metrc,
            type:'Type:' + item_type,
            net_weight:'Net Weight:' + items[id].weight,
            tare:'Tare:' + tare,
            total:'Total:' + (items[id].weight + tare),
        }
        //submit
        $.ajax({
            url:'_get_curning_barcode',
            type:'post',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(barcode_data),
            async:false,
            success:(res) => {
                new Promise((fulfill) => {
                    $(".barcode_panel").html(res)
                    fulfill()
                }).then(() => {
                    let _style = '' +
                    '<style type="text/css">' +
                    'div,img {' +
                    'width:250px;' +
                    'height:30px;' +
                    'text-align:center;' +
                    'text-weight:bold;' +
                    '}' +
                    '</style>'
                    newWin= window.open("")
                    newWin.document.write(_style + $('.barcode_panel').html())
                    newWin.print()
                    newWin.close()
                })
            },
            error:(e) => {
                show_input_validation('Error Occur while printing the barcode')
            }
        })
        items[id].printed = true
        create_table()
    })
    $('#containerTable tbody').on('click','.remove_btn',function(){
        let tr = $(this).parents('tr')
        let id = parseInt(tr.attr('item_id'));
        items.splice(id,1)
        create_table()
    })

//
let show_input_validation = (msg) => {
    $("#msg_field").html(msg)
    $("#input_warning").show()
}
$(".close").click(function(){
    $("#input_warning").hide()
})
$(document).ready(function() {
    create_table()
    $("body").addClass('fixed');
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});