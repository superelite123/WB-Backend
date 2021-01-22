//Get data from Laravel Controller
var Items = windowvar.items
var Invoice_item = windowvar.invoice_item
var invoice_id = windowvar.id
var clients = windowvar.clients
//invoice_row
var inserted_data = Array();
var reloadThisPage = () => {
    window.location = 'create';
}
//-----------------------end keyup event chain--------------------------

var calc_invoice_total = () => {
    var total = 0;
    for(var i = 0; i < inserted_data.length; i ++)
    {
        total += inserted_data[i].adjust_price;
    }

    return total;
};

var findIndexWithAttr =  function(array, attr, value){
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] == value) {
            return i;
        }
    }
    return -1;
};

var CreateTable = () => {
    var html = "";
    
    $.each(inserted_data,function(index,element) {
        html += create_row(index,element);
    });

    $("#inserted_table > tbody > tr").remove();
    $("#inserted_table > tbody").html(html);
    calc_total();
    console.log(inserted_data)
};
var calc_total = () => {
    
    var sub_total = 0;
    var less_discounted = 0;
    let taxed = 0
    let adjust_price = 0;
    
    let temp_sub = 0
    let temp_less = 0
    
    $.each(inserted_data,function(index,element) {
        temp_sub = element.qty * element.unit_price
        temp_less = temp_sub - element.discount
        temp_adjust = temp_less + parseFloat(element.tax)
        
        sub_total += temp_sub
        less_discounted += temp_less
        taxed += parseFloat(element.tax);
        adjust_price += temp_less + parseFloat(element.tax)
    });

    $("#bottom_sub_total").text(sub_total)
    $("#less_discounted").text(less_discounted)
    $("#taxed").text(taxed)
    $("#total_price").text(adjust_price)
}
var create_row = (index,data) => {
    var html = "";
    let less_discount = data.sub_total - data.discount
    
    html += "<tr item_id='" + index +"'>";
    html += "<td>" + (index + 1) + "</td>";
    html += "<td>" + data.description + "</td>";
    html += "<td>" + data.qty + "</td>";
    html += "<td>" + data.unit_price + "</td>"
    html += "<td>" + data.discount + "</td>";
    html += "<td>" + data.sub_total + "</td>";
    html += "<td>" + data.less_discount + "</td>";
    html += "<td>" + data.tax + "</td>";
    html += "<td>" + data.tax_note + "</td>";
    html += "<td>" + data.adjust_price + "</td>";
    if(data.packed)
    {
        html += '<td colspan="2" style="color:#00a65a;font-weight:bold;font-size:19px">Packed!</td>'
    }
    else
    {
        html += "<td><button class='btn btn-info btn_item_edit'><i class='fas fa-edit'>&nbsp;</i>edit</button></td>";
        html += "<td><button class='btn btn-Success btn_item_remove'><i class='fas fa-box'>&nbsp;</i>Pack me</button></td>";
    }
    html += "</tr>";
    return html;
}

var check_item_duplicate = (data) => {
    return new Promise((resolve,reject) => {
        inserted_data.forEach(element => {
        
            if(element.item_id == data.item_id)
            {
                reject()
            }
        });

        resolve();
    
    })
}

$(".makeBtn").click(function(){
    
    if(inserted_data.length == 0)
    {
        swal("You need to fill all fields to save the Invoice", "", "warning")
        return;
    }

    for(let i = 0; i < inserted_data.length; i ++)
    {
        if(!inserted_data[i].packed)
        {
            swal('You need to pack all item','','info')
            return false        
        }
    }
    
    swal({
        title: "Are You Sure",
        text: "Are You going to process Order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
      }, function () {
        submit_invoice();
      });
})

var submit_invoice = () => {
    var post_data = {
        'items':inserted_data,
        'id':invoice_id,
    };
    
    $.ajax({
        url:'fulfill_store',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        type:'post',
        success:function(res){
            if(parseInt(res) > 0)
            {
                window.location = 'list';
            }
        },
        error:function(e)
        {
            waitingDialog.hide();
            swal("Error!", "An error occured while saving Invoice!", "danger")
        },
         
    });
}

//----------------------------------start inserted items---------------------------------------------------

$("#inserted_table > tbody").on("click", ".btn_item_remove", function(){
    var item_id = parseInt($(this).parents('tr').attr('item_id'))
    swal({
        title: "Notice",
        text: "You are going to packed this Item?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
      }, function () {
        inserted_data[item_id].packed = true
        CreateTable()
    });
});

$("#inserted_table > tbody").on("click", ".btn_item_edit", function(){
    
    let item_selector_html = "<select class='form-control select2' name='edit_item_items'>";
    let id = $(this).parents("tr").attr('item_id');
    let row_data = inserted_data[id];
    var selected = ""
    Items.forEach(function(item,index){
        if(row_data.item_id == item.fgasset_id) selected = 'selected';
        else selected = '';

        item_selector_html += '<option value="' + item.fgasset_id + '" ' + selected +'>' + item.description + '</option>';
    })
    item_selector_html += '</select>';

    $(this).parents("tr").find("td:eq(1)").html(item_selector_html);

    $(this).parents("tr").find("td:eq(2)").html('<input type="number" name="edit_item_qty" style="width:50px" value="' + row_data.qty + '">');

    $(this).parents("tr").find("td:eq(10)").prepend("<button class='btn btn-success btn_item_update'>Update</button>");
    $(this).parents("tr").find("td:eq(11)").prepend("<button class='btn btn-warning btn_item_cancel'>Cancel</button>");
    
    $(this).toggle();
    $(this).parents('tr').find(".btn_item_remove").toggle();
    $('.select2').select2()
});
$("#inserted_table > tbody").on('click', '.btn_item_update',function(){
    let tr = $(this).parents('tr');
    let id = parseInt(tr.attr('item_id'));

    var data = inserted_data[id];
    
    data.item_id      = tr.find('select[name="edit_item_items"]').val()
    data.qty          = parseFloat(tr.find('input[name="edit_item_qty"]').val())
    data.description  = Items[ findIndexWithAttr(Items,'fgasset_id',data.item_id) ].description;
    data.unit_price   = Invoice_item[id].unit_price;
    data.discount     = Invoice_item[id].discount;
    data.tax            = parseFloat(Invoice_item[id].tax);

    if(data.qty == 0)
    {
        alert('can not 0 value item\n please enter the valid info for items');
        return;
    }
    data.sub_total    = data.qty * data.unit_price;
    data.less_discount = data.sub_total - data.discount
    data.adjust_price = data.less_discount + data.tax;
    inserted_data[id] = data;
    update_cancel_row(tr,data);
    calc_total();
})
$("#inserted_table > tbody").on('click','.btn_item_cancel',function(){
    let tr = $(this).parents('tr');
    let id = parseInt(tr.attr('item_id'));

    var data = inserted_data[id];
    update_cancel_row(tr,data);
})

var update_cancel_row = (tr,data) => {
    tr.find('td:eq(1)').text(data.description);
    tr.find('td:eq(2)').text(data.qty);
    tr.find('td:eq(3)').text(data.unit_price);
    tr.find('td:eq(4)').text(data.discount);
    tr.find('td:eq(5)').text(data.sub_total);
    tr.find('td:eq(6)').text(data.less_discount);
    tr.find('td:eq(7)').text(data.tax);
    tr.find('td:eq(9)').text(data.adjust_price);

    tr.find(".btn_item_update").remove();
    tr.find(".btn_item_cancel").remove();
    tr.find(".btn_item_edit").toggle();
    tr.find(".btn_item_remove").toggle();
}
//---------------------------------- end inserted items------------------------------------------

$(function(){
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });
    $("body").addClass('fixed');
    $('.select2').select2();

    $('.datepicker').datepicker({
        autoclose: true
    })
})

var initOp = () => {

    if(Invoice_item == null)
    {
        return;
    }

    for(var i =0; i < Invoice_item.length; i ++)
    {
        var data = {};
        data.item_id = Invoice_item[i].item_id;
        data.description = Items[ findIndexWithAttr(Items,'fgasset_id',data.item_id) ].description;
        data.unit_price = Invoice_item[i].unit_price;
        data.qty = Invoice_item[i].qty;
        data.discount = Invoice_item[i].discount;
        data.tax_note = Invoice_item[i].tax_note != null?Invoice_item[i].tax_note:'No Note';
        data.sub_total = data.qty * data.unit_price;
        data.less_discount = data.sub_total - data.discount
        data.tax = parseFloat(Invoice_item[i].tax);
        data.adjust_price = data.less_discount + data.tax;
        data.packed = false
        inserted_data.push(data);
    }

    CreateTable();
    $("#save_shipping_method").click();
    $("#modal_shipping_method").modal('hide');
};
initOp();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
