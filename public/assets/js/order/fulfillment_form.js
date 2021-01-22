let invoice_id = windowvar.invoice_id
let req_items  = windowvar.req_items
let inventory  = windowvar.inventory
/**
 * detect typing finished
 * */
let doneTypingInterval = 2000
let typingTimer
let inventory_tbody = $('#inventory_tbody')
inventory_tbody.on("keyup", ".input_scanned", function(){
    let tr = $(this).parents('tr')
    big_cnt = parseInt(tr.attr('big_id'))
    sub_cnt = parseInt(tr.attr('sub_id'))
    metrc = tr.find('input[name="input_scanned"]').val()
    clearTimeout(typingTimer)
    typingTimer = setTimeout(fnScannedMetrc.bind(null,big_cnt,sub_cnt,metrc), doneTypingInterval)
})
inventory_tbody.on("keydown", ".input_scanned", function(){
    clearTimeout(typingTimer)
})

inventory_tbody.on("click", ".btn_print", function(){
    let tr = $(this).parents('tr')
    let big_cnt = parseInt(tr.attr('big_id'))
    let sub_cnt = parseInt(tr.attr('sub_id'))
    let metrcs = []

    if(inventory[big_cnt].merge_info.status == 1)
    {
        metrcs.push(inventory[big_cnt].merge_info.metrc)
        // inventory[big_cnt].items.forEach(element => {
        //     metrcs.push(element.scanned_metrc)
        // });
    }
    else{
        let metrc = inventory[big_cnt].items[sub_cnt].scanned_metrc
        if(metrc == '')
        {
            alert('can not print empty metrc')
            return false
        }
        metrcs.push(metrc)
    }
    $.ajax({
        url:'_print_barcode',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify({metrcs:metrcs}),
        type:'post',
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

        }
    })
})

inventory_tbody.on("click", ".btn_combine", function(){
    let tr = $(this).parents('tr')
    let big_id = parseInt(tr.attr('big_id'))
    let fulfill_flag = true
    let nevercombines = []
    inventory[big_id]['items'].forEach(element => {
        if(element.status != 1 && element.status != 2)
        {
            fulfill_flag = false
        }
        if(element.upc != null)
        {
            if(element.upc.nevercombine == 1)
            {
                nevercombines.push(element.description)
            }
        }
    })

    if(nevercombines.length > 0)
    {
        let msg = ''
        nevercombines.forEach(element => {
            msg += element + ','
        });
        msg += 'are set as nevercombine'
        swal("Notice!", msg, "info");
        return false
    }

    if(fulfill_flag)
    {
        swal({
            title: "Combine",
            text: "Enter the New Metrc:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            inputPlaceholder: "Enter the New Metrc"
          }, function (inputValue) {
            if (inputValue === false) return false;
            if (inputValue === "") {
                swal("Warning!", "You need to enter the Metrc Tag", "warning")
                return false
            }
            let duplicated = false
            inventory.forEach(element => {
               if(inputValue == element.merge_info.metrc) duplicated = true
               element.items.forEach(item => {
                if(inputValue == item.metrc_tag) duplicated = true
                });
            });
            if(duplicated)
            {
                swal("Warning!", "This Metrc Tag already exist", "warning")
                return false
            }
            fnMerge({'big_id':big_id,'metrc':inputValue,operation:0})
            swal("Nice!", "You entered: " + inputValue, "success");
        })
    }
    else
    {
        swal("Notice!", "You need to fulfill all items", "info");
    }
})
inventory_tbody.on("click", ".btn_plus", function(){
    let tr = $(this).parents('tr')
    let big_id = parseInt(tr.attr('big_id'))
    $('#sub_tbl_' + big_id).toggle()
    if($('#sub_tbl_' + big_id).is(":visible")){
        $(this).parents('td').html('<button class="btn btn-xs btn-info btn_plus"><i class="fas fa-minus"></i></button>')
    }
    else
    {
        $(this).parents('td').html('<button class="btn btn-xs btn-info btn_plus"><i class="fas fa-plus"></i></button>')
    }
})
inventory_tbody.on("click", ".btn_unmerge", function(){
    let tr = $(this).parents('tr')
    let big_id = parseInt(tr.attr('item_id'))
    inventory[big_id].merge_info.status=0
    fnCreateInventoryTable()
})
let fnMerge = (data) =>
{
    inventory[data.big_id].merge_info.status=1
    inventory[data.big_id].merge_info.metrc=data.metrc
    fnCreateInventoryTable()
}
let fnScannedMetrc = (big_cnt,sub_cnt,metrc) => {
    //Check Match
    let item = inventory[big_cnt]['items'][sub_cnt]

    if(metrc == item.metrc_tag)
    {
        $.ajax({
            url:'_check_metrc_info',
            data:'metrc=' + metrc,
            type:'post',
            success:(res) => {
                inventory[big_cnt]['items'][sub_cnt].status = 1
                inventory[big_cnt]['items'][sub_cnt].fgasset_id = res.id
                inventory[big_cnt]['items'][sub_cnt].scanned_metrc = metrc
                inventory[big_cnt]['items'][sub_cnt].coa = res.coa;
                inventory[big_cnt]['items'][sub_cnt].i_type = res.i_type;
                fnCreateInventoryTable()
                fnCreateReqTable()
            },
            error:(e) => {
                swal("Error is happened while communicating with server", "", "error")
            }
        })
    }
    else
    {
        fnCheckMetrcInfo(metrc).then((res) => {
            if(res == -1)
            {
                swal("This Metrc Tag doesn't exist", "", "warning")
                return false
            }
            if(res == -2)
            {
                swal("This Metrc Tag already exist", "", "warning")
                return false
            }
            let compare_res = 2
            if(inventory[big_cnt].strain == res.strain && inventory[big_cnt].p_type == res.p_type )
            {
                compare_res = 2
            }
            else
            {
                compare_res = 3
            }

            inventory[big_cnt]['items'][sub_cnt].status = compare_res
            if(compare_res == 2)
            {
                inventory[big_cnt]['items'][sub_cnt].fgasset_id = res.id;
                inventory[big_cnt]['items'][sub_cnt].coa = res.coa;
                inventory[big_cnt]['items'][sub_cnt].i_type = res.i_type;
            }
            inventory[big_cnt]['items'][sub_cnt].scanned_metrc = metrc
            fnCreateInventoryTable()
            fnCreateReqTable()
        })
    }
}
let fnCheckMetrcInfo = (metrc) => {
    return new Promise((fulfill) => {
        let unique = true
        inventory.forEach(element => {
            if(element.merge_info.status == 1)
            {
                if(metrc == element.merge_info.metrc)
                {
                    unique = false
                    fulfill(-2)
                }
            }

            element.items.forEach(item => {
                if(item.metrc_tag == metrc || item.scanned_metrc == metrc)
                {
                    unique = false
                    fulfill(-2)
                }
            })
        });
        $.ajax({
            url:'_check_metrc_info',
            data:'metrc=' + metrc,
            type:'post',
            success:(res) => {
                fulfill(res)

            },
            error:(e) => {
                swal("Error is happened while communicating with server", "", "error")
            }
        })
    })
}
let fnCreateReqTable = () => {
    let html = ''
    let cnt = 1;
    req_items.forEach(element => {
        let myInventory = inventory[findIndexWithAttr(inventory,'item_id',element.id)].items
        let fulfilled = 0
        for(let i = 0; i < myInventory.length; i ++)
        {
            if(myInventory[i].status == 1 || myInventory[i].status == 2)
            {
                fulfilled ++
            }

            if(myInventory[i].status == 3)
            {
                fulfilled = -1
                break;
            }
        }

        html += '<tr item_id="' + (cnt - 1) + '">'
        html += '<td>' + cnt + '</td>'
        html += '<td>' + element.strain_label + '</td>'
        html += '<td>' + element.type_label + '</td>'
        html += '<td>' + element.qty + '</td>'
        html += '<td>' + element.units + '</td>'
        html += '<td>' + element.base_price + '</td>'
        html += '<td>' + element.cpu + '</td>'
        html += '<td>' + element.discount + '</td>'
        html += '<td>' + element.discount_type + '</td>'
        html += '<td>' + element.e_discount + '</td>'
        html += '<td>' + element.extended + '</td>'
        html += '<td>' + element.tax + '</td>'
        html += '<td>' + element.adjust_price + '</td>'
        html += '<td>' + element.tax_note + '</td>'
        if(fulfilled == element.qty)
        {
            html += '<td class="green_row checked_row"><i class="fas fa-check"></i></td>'
        }
        else
        {
            if(fulfilled == -1)
            {
                html += '<td class="red_row checked_row"><i class="fa fa-exclamation-triangle"></i></td>'
            }
            else
            {
                html += '<td class="blue_row checked_row"><i class="fa fa-hourglass"></i>&nbsp;&nbsp;' + fulfilled + '</td>'
            }
        }

        html += '</tr>'
        cnt ++;
    });

    $('#req_tbody').html(html)
}
let fnCreateInventoryTable = () => {
    let html = ''
    let cnt = 1;
    let big_cnt = 0
    let sub_cnt = 0

    inventory.forEach(items => {
        let combine_flag = items['items'].length > 1?true:false
        if(items.merge_info.status == 1)
        {
            html += '<tr class="green_row" big_id="' + big_cnt + '">'
            html += '<td>' + (cnt) + '</td>'
            html += '<td><button class="btn btn-xs btn-info btn_plus"><i class="fas fa-plus"></i></button></td>'
            let req_item = req_items[findIndexWithAttr(req_items,'id',items.item_id)]
            html += '<td>' + req_item.strain_label + ',' + req_item.type_label + '</td>'
            html += '<td>' + items.merge_info.metrc + '</td>'
            html += '<td>' + items['items'].length + '</td>'
            let weight = 0
            items['items'].forEach(element => {
                weight += parseFloat(element.weight)
            });
            html += '<td>' + weight + '</td>'
            html += '<td></td>'
            html += '<td>' + items['items'][0].coa + '</td>'
            html += '<td><button class="btn btn-xs btn-info btn_print"><i class="fas fa-print">&nbsp;</i>Print</button></td>'
            html += '</tr>'
            html += '<tr>'
            html += '<td colspan="8">'
            html += '<table class="table table-bordered" id="sub_tbl_' + big_cnt + '" style="padding-left:50px;display:none">'
            html += '<tr item_id="' + big_cnt + '"><td colspan="8"><button class="btn btn-xs btn-info btn_unmerge">unmerge</button></td></tr>'
            cnt ++
        }

        items['items'].forEach(element => {
            let row_class = ''
            switch (element.status) {
                case 1:
                    row_class = 'green_row'
                    break;
                case 2:
                    row_class = 'blue_row'
                    break;
                case 3:
                    row_class = 'red_row'
                    break;
                default:
                    break;
            }
            html += '<tr class="' + row_class + '" big_id="' + big_cnt + '" sub_id="' + sub_cnt + '">'
            if(items.merge_info.status != 1)
                html += '<td>' + cnt + '</td>'
            else
            html += '<td>' + (sub_cnt+1) + '</td>'

            if(combine_flag && items.merge_info.status == 0)
            {
                let avaliable_flag = true
                items['items'].forEach(element => {
                    if(element.status != 1 && element.status !=2)
                    {
                        avaliable_flag = false
                    }
                })
                if(avaliable_flag)
                {
                    html += '<td><button class="btn btn-xs btn-success btn_combine">combine</button></td>'
                }
                else
                {
                    html += '<td><button class="btn btn-xs btn-info btn_combine">combine</button></td>'
                }

                combine_flag = false;
            }
            else
            {
                html += '<td></td>'
            }
            if(items.merge_info.status == 0 && items.items.length > 1)
            {
                html += '<td><i class="fas fa-asterisk" style="font-size:10px;color:#00acd6"></i>' + element.description + '</td>'
            }
            else
            {
                html += '<td>' + element.description + '</td>'
            }
            if(items.merge_info.status == 0)
            {
                html += '<td><input class="form-control input_scanned" name="input_scanned" value="' + element.scanned_metrc + '" /></td>'
            }
            else
            {
                html += '<td>' + element.scanned_metrc + '</td>'
            }
            html += '<td>' + element.qtyonhand + '</td>'
            html += '<td>' + element.weight + '</td>'
            html += '<td>' + element.harvested_date + '</td>'
            html += '<td>' + element.coa + '</td>'
            html += '<td><button class="btn btn-xs btn-info btn_print"><i class="fas fa-print">&nbsp;</i>Print</button></td>'
            html += '</tr>'

            if(items.merge_info.status != 1)
                cnt ++

            sub_cnt ++
        })

        if(items.merge_info.status == 1)
        {
            html += '</td></tr></table>'
        }
        sub_cnt = 0
        big_cnt ++
    })
    $('#inventory_tbody').html(html)
}
$('.fulfillBtn').click(() => {
    let flag = true
    inventory.forEach(element => {
        element.items.forEach(item => {
            if(item.status != 1 && item.status !=2 )
            {
                flag = false
            }
        })

        if(element.items.length < element.qty)
        {
            flag = false
        }
    })

    if(!flag)
    {
        swal("Warning!", "You need to fulfill all Request", "warning")
        return false
    }
    swal({
        title: "Are You Sure",
        text: "Are You going to fulfill this order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
      }, function () {
        submit_order(3)
    })

})
$('.unableBtn').click(() => {
    swal({
        title: "Are You Sure",
        text: "Are you sure you can not fulfill this order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
      }, function () {
        submit_order(2)
    })
})

let submit_order = (status=1) => {
    let post_data = {
        'id':invoice_id,
        'status':status,
        'inventory':inventory
    }

    $.ajax({
        url:'_fulfillment_store',
        headers:{"content-type" : "application/json"},
        data:JSON.stringify(post_data),
        type:'post',
        success:function(res){
            if(res == '3')
            {
                window.open("fulfilled_print_from_form/" + invoice_id, '_blank');
                location.href='fulfillment_list'
            }
            if(res == '2')
                location.href='fulfillment_list'
        },
        error:(e) => {
            swal(e.statusText, e.responseJSON.message, "error")
        }
    })
}
let findIndexWithAttr =  function(array, attr, value){
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] == value) {
            return i;
        }
    }
    return -1
}
$(() => {
    fnCreateReqTable()
    fnCreateInventoryTable()
    $('.toast').popover('show');
    $('.sidebar-toggle').click()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})
