//Get data from Laravel Controller
var Items = windowvar.items
var Invoice_item = windowvar.invoice_item
var mode = windowvar.mode
var invoice_id = windowvar.id
var shipping_method = windowvar.shipping_method
var tax = parseFloat(windowvar.tax)
var clients = windowvar.clients
shipping_method.ok = true
console.log(Items)
//invoice_row
var inserted_data = Array();

//product List
var Item_list         = $("#item");
var QtyInput          = $("#qty");
var Unit_priceInput   = $("#unit_price");
var Sub_totalInput    = $("#sub_total");
var DiscountInput     = $("#discount");
var LessDiscountInput = $("#less_discount");
var TaxInput          = $("#tax");
var Adjust_priceInput = $("#adjust_price");

//-----------------------start keyup event chain------------------------
QtyInput.keyup(function(e){
    if (event.keyCode === 13) {
        event.preventDefault();
        Unit_priceInput.focus();
    }
})
Unit_priceInput.keyup(function(e){
    if (event.keyCode === 13) {
        event.preventDefault();
        DiscountInput.focus();
    }
})
DiscountInput.keyup(function(e){
    if (event.keyCode === 13) {
        event.preventDefault();
        Adjust_priceInput.focus();
    }
})

Adjust_priceInput.keyup(function(e){
    if (event.keyCode === 13) {
        event.preventDefault();
        $("#add_row").click();
    }
})

$("#allow_tax").click(function(){
    
    if($(this).prop('checked') == true)
    {
        TaxInput.val(tax)
    }
    else
    {
        TaxInput.val(0)
    }
    
    calc_adjust_price()
})

var reloadThisPage = () => {
    window.location = 'create';
}
//-----------------------end keyup event chain--------------------------

//use arrow function follow ES6 grammar
var calc_adjust_price = () => {
    var data = {}

    data.item_id      = Item_list.val()
    if(data.item_id == "0")
    {
        ListenerForItemList()
        return;
    }
    data.description  = Items[ findIndexWithAttr(Items,'fgasset_id',data.item_id) ].description
    data.type  = Items[ findIndexWithAttr(Items,'fgasset_id',data.item_id) ].type
    data.unit_price   = parseFloat($("#unit_price").val())
    data.qty          = parseFloat($("#qty").val())
    data.discount     = parseFloat($("#discount").val())
    data.tax          = parseFloat($("#tax").val())
    data.tax_note     = $('#tax_note').val()
    // if(data.unit_price == 0)
    // {
    //     Sub_totalInput.val(0)
    //     DiscountInput.val(0)
    //     LessDiscountInput.val(0)
    //     Adjust_priceInput.val(0)

    //     return 1;
    // }
    
    data.sub_total     = data.qty * data.unit_price
    data.less_discount = data.sub_total - data.discount
    data.adjust_price  = data.less_discount + data.tax
    
    // if(data.less_discount <= 0)
    // {
    //     return 2;
    // }

    Sub_totalInput.val(data.sub_total.toFixed(2));
    LessDiscountInput.val(data.less_discount.toFixed(2))
    Adjust_priceInput.val(data.adjust_price.toFixed(2));
    return data;
};

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
};
var calc_total = () => {
    
    var sub_total = 0;
    var less_discounted = 0;
    let taxed = 0
    let adjust_price = 0;

    $.each(inserted_data,function(index,element) {

        sub_total += element.qty * element.unit_price;
        less_discounted += (element.qty * element.unit_price) - element.discount
        taxed += parseFloat(element.tax);
        adjust_price += ((element.qty * element.unit_price) - element.discount) + parseFloat(element.tax)
    });

    $("#bottom_sub_total").text(sub_total.toFixed(2))
    $("#less_discounted").text(less_discounted.toFixed(2))
    $("#taxed").text(taxed.toFixed(2))
    $("#total_price").text(adjust_price.toFixed(2))
}
var create_row = (index,data) => {
    var html = "";
    let less_discount = data.sub_total - data.discount
    
    html += "<tr item_id='" + index +"'>";
    html += "<td>" + (index + 1) + "</td>";
    html += "<td>" + data.description + "</td>";
    html += "<td>" + data.qty + "</td>";
    html += "<td>" + parseFloat(data.unit_price).toFixed(2) + "</td>"
    html += "<td>" + parseFloat(data.discount).toFixed(2) + "</td>";
    html += "<td>" + parseFloat(data.sub_total).toFixed(2) + "</td>";
    html += "<td>" + parseFloat(data.less_discount).toFixed(2) + "</td>";
    html += "<td>" + parseFloat(data.tax).toFixed(2) + "</td>";
    html += "<td>" + data.tax_note + "</td>";
    html += "<td>" + parseFloat(data.adjust_price).toFixed(2) + "</td>";
    html += "<td><button class='btn btn-info btn-xs btn_item_edit'>edit</button></td>";
    html += "<td><button class='btn btn-danger btn-xs btn_item_remove'>remove</button></td>";
    html += "</tr>";
    return html;
}

var ListenerForItemList = () =>{
    Unit_priceInput.val(0);
    QtyInput.val(1);
    Sub_totalInput.val(0);
    DiscountInput.val(0);
    LessDiscountInput.val(0);
    TaxInput.prop('checked',false)
    Adjust_priceInput.val(0);
};

Item_list.change(function(){
    ListenerForItemList();
    QtyInput.focus();
});

QtyInput.on('input',function(){
    if($(this).val() < 1)
        $(this).val(1)
    calc_adjust_price()
});

Unit_priceInput.on('input',function(){
    if($(this).val() < 0.000001)
        $(this).val(0)
    calc_adjust_price()
});

DiscountInput.on('focusin', function(){
    $(this).data('val', $(this).val())
});

DiscountInput.on('input',function(){  
    if($(this).val() < 0.000001)
        $(this).val(0)  
    calc_adjust_price();
});

$("#add_row").click(function() {
    var insert_data = calc_adjust_price();
    
    // if(insert_data == 1)
    // {
    //     alert('Enter the Valid Cost');
    //     return;
    // }

    // if(insert_data == 2)
    // {
    //     alert("Discount Value can not be big than Sub total")
    //     return;
    // }

    check_item_duplicate(insert_data).then(() => {
        inserted_data.push(insert_data);
        CreateTable();
        ListenerForItemList();
    },() => {
        alert('This item is alreay exist');
    })  
});

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

$("#save_shipping_method").click(function(){
    // == ''
    var shipment_date    = $("#shipment_date").val();
    var shipping_carrier = $("#shipping_carrier").val();
    var expected_date    = $("#expected_date").val();
    var actual_date      = $("#actual_date").val();
    var trackingid       = $("#trackingid").val();
    
    // if(shipping_carrier == 0)
    // {
    //     swal("You need to fill All field to save the shipping", "", "warning")
    //     shipping_method.ok =false;
    //     return;
    // }
    
    shipping_method.trackingid       = trackingid;
    shipping_method.shipment_date    = shipment_date;
    shipping_method.shipping_carrier = shipping_carrier;
    shipping_method.expected_date    = expected_date;
    shipping_method.actual_date      = actual_date;
    shipping_method.ok               = true;
    $("#modal_shipping_method").modal('toggle');

    $("#add_shipping_method").html('<i class="fa fa-fw fa-pen"></i>EDIT SHIPPING METHOD');
})

$(".makeBtn").click(function(){
    
    if(inserted_data.length == 0)
    {
        swal("You need to fill All field to save the Invoice", "", "warning")
        return;
    }
    
    if($("#salesperson").val() == '0')
    {
        swal("please select the salesPerson", "", "warning")
        return;
    }

    if($("#distuributor").val() == '0')
    {
        swal("please select the Distuributor", "", "warning")
        return;
    }
    
    if($("#client").val() == '0')
    {
        alert('please select the Customer');
        swal("please select the Customer", "", "warning")
        return;
    }

    if(!shipping_method.ok)
    {
        swal("You have to enter the Shipping Method", "", "warning")
        return;
    }

    swal({
        title: "Are You Sure",
        text: "Are You going to save the Invoice?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        submit_invoice();
      });
});

$('#client').change(function(){
    if($(this).val() == 0) return
    let term = clients[findIndexWithAttr(clients,'client_id',$(this).val())].term
    $('.term_content').html(term == null?'No Term':term.term)
})

var submit_invoice = () => {
    let term = clients[findIndexWithAttr(clients,'client_id',$('#client').val())].term
    term = term == null?-1:term.term_id
    var post_data = {
        'number':$("#number").val(),
        'customer_id':$("#client").val(),
        'date': $("#date").val(),
        'note': $('#note').val(),
        'distuributor_id':$('#distuributor').val(),
        'total':calc_invoice_total(),
        'items':inserted_data,
        'term_id':term,
        'salesperson_id':$("#salesperson").val(),
        'id':invoice_id,
        'mode':mode,
        'shipping_method':shipping_method
    };
    
    $.ajax({
        url:'store',
        headers:{"content-type" : "application/json"},
        data: JSON.stringify(post_data),
        type:'post',
        success:function(res){
            swal({
                title: "Options.",
                text: "How would you like to proceed",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                cancelButtonClass:'btn-default',
                confirmButtonText: "Go to Pending Approval List",
                cancelButtonText: "Create New Order",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    location.href="pending_list"
                } else {
                    location.reload()
                }
            })
        },
        error:function(e)
        {
            waitingDialog.hide();
            swal("", "An error has occured while saving Invoice!", "danger")
        },
         
    });
}

$("#confirm_modal").on("hidden.bs.modal", function () {
    location.href="create"
});

$("#emailing").click(() => {
    if(invoice_id == null)
    {
        swal('The Invoice does not exist','danger')
        return
    }

    waitingDialog.show('Sending Email...')

    $.ajax({
        url:'email',
        type:'post',
        data:'id=' + invoice_id,
        success:function(res){
            waitingDialog.hide();
            if(res == '1')
            {
                swal("Email sent Successfully", "", "success")
            }
        },
        error:function(e){
            
        },
    })
}) 
$("#printing").click(() => {

    location.href = "view?id=" + invoice_id + "&print=true"
}) 
$("#listing").click(() => {
    location.href="list"
}) 

$("#add_shipping_method").click(function(){
    $("#modal_shipping_method").modal();
})

var check_post_valid = () => {

}

//----------------------------------start inserted items---------------------------------------------------
// $("#inserted_table > tbody").on("input", "input", function(){
//     let tr = $(this).parents('tr')
//     let item_subtotal      = tr.find('td:eq(5)')
//     let item_ajust_price   = tr.find('td:eq(9)')
//     let item_less_discount = tr.find('td:eq(6)')

//     let qty        = parseFloat(tr.find('input[name="edit_item_qty"]').val())
//     let unit_price = parseFloat(tr.find('input[name="edit_item_unit_price"]').val())
    
//     let discount   = parseFloat(tr.find('input[name="edit_item_discount"]').val())
//     let tax        = parseFloat(tr.find('td:eq(7)').val())

//     if(qty == 0 || unit_price == 0)
//     {
//         alert('can not use 0 value item\n enter the valid info for items');
//         swal("can not use 0 value item\n enter the valid info for items", "", "danger")
//         return;
//     }
    
//     let less_discount = qty * unit_price - discount
//     let adjust_price = less_discount + tax

//     item_subtotal.text(qty * unit_price)
//     item_less_discount.text(less_discount)
//     item_ajust_price.text(adjust_price)
// });

$("#inserted_table > tbody").on("click", ".btn_item_remove", function(){
    var item_id = parseInt($(this).parents('tr').attr('item_id'));
    inserted_data.splice(item_id,1);
    CreateTable();
    $(this).parents("tr").remove();
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
    $(this).parents("tr").find("td:eq(3)").html('<input type="number" name="edit_item_unit_price" style="width:50px" value="'+row_data.unit_price+'">');
    $(this).parents("tr").find("td:eq(4)").html('<input type="number" name="edit_item_discount" style="width:50px" value="'+row_data.discount+'">');
    
    $(this).parents("tr").find("td:eq(7)").html('<input type="number" name="edit_item_tax" style="width:50px" value="'+row_data.tax+'">');
    $(this).parents("tr").find("td:eq(8)").html('<input type="text" name="edit_item_tax_note" style="width:150px" value="'+row_data.tax_note+'">');
    $(this).parents("tr").find("td:eq(10)").prepend("<button class='btn btn-info btn-xs btn_item_update'>Update</button>");
    $(this).parents("tr").find("td:eq(11)").prepend("<button class='btn btn-warning btn-xs btn_item_cancel'>Cancel</button>");
    
    $(this).toggle();
    $(this).parents('tr').find(".btn_item_remove").toggle()
    $('.select2').select2()
});
$("#inserted_table > tbody").on('click', '.btn_item_update',function(){
    let tr = $(this).parents('tr');
    let id = parseInt(tr.attr('item_id'));

    var data = {};

    data.item_id      = tr.find('select[name="edit_item_items"]').val()
    data.description  = Items[ findIndexWithAttr(Items,'fgasset_id',data.item_id) ].description
    data.type         = Items[ findIndexWithAttr(Items,'fgasset_id',data.item_id) ].type
    data.unit_price   = parseFloat(tr.find('input[name="edit_item_unit_price"]').val())
    data.qty          = parseFloat(tr.find('input[name="edit_item_qty"]').val())
    data.discount     = parseFloat(tr.find('input[name="edit_item_discount"]').val())
    data.tax          = parseFloat(tr.find('input[name="edit_item_tax"]').val())
    data.tax_note     = tr.find('input[name="edit_item_tax_note"]').val()
    data.sub_total     = data.qty * data.unit_price;
    data.less_discount = data.sub_total - data.discount

    if(data.qty == 0 || data.unit_price == 0)
    {
        alert('can not 0 value item\n please enter the valid info for items');
        return;
    }

    if(data.less_discount <= 0)
    {
        return alert('enter the correct the valid discount');
        return false;
    }
    
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
    tr.find('td:eq(3)').text(data.unit_price.toFixed(2));
    tr.find('td:eq(4)').text(data.discount.toFixed(2));
    tr.find('td:eq(5)').text(data.sub_total.toFixed(2));
    tr.find('td:eq(6)').text(data.less_discount.toFixed(2));
    tr.find('td:eq(7)').text(data.tax.toFixed(2));
    tr.find('td:eq(8)').text(data.tax_note);
    tr.find('td:eq(9)').text(data.adjust_price.toFixed(2));

    tr.find(".btn_item_update").remove();
    tr.find(".btn_item_cancel").remove();
    tr.find(".btn_item_edit").toggle();
    tr.find(".btn_item_remove").toggle();
}
//---------------------------------- end inserted items------------------------------------------
ListenerForItemList();

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
        data.type         = Items[ findIndexWithAttr(Items,'fgasset_id',data.item_id) ].type
        data.unit_price = Invoice_item[i].unit_price;
        data.qty = Invoice_item[i].qty;
        data.discount = Invoice_item[i].discount;
        data.sub_total = data.qty * data.unit_price;
        data.less_discount = data.sub_total - data.discount
        data.tax = parseFloat(Invoice_item[i].tax);
        data.tax_note = Invoice_item[i].tax_note != null?Invoice_item[i].tax_note:'No Note';
        data.adjust_price = data.less_discount + data.tax;

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
