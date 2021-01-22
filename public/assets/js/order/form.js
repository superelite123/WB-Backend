//Get data from Laravel Controller
var Invoice_item        = windowvar.invoice_item
var mode                = windowvar.mode
var invoice_id          = windowvar.id
var shipping_method     = windowvar.shipping_method
var clients             = windowvar.clients
let sel_client          = windowvar.sel_client
let strains             = windowvar.strains
let p_types             = windowvar.p_types
let promos              = windowvar.promos
//let cPersons            = windowvar.contact_persons
var tax                 = parseFloat(windowvar.tax)
let tax_allow           = windowvar.tax_allow == null?0:windowvar.tax_allow
let avaliable_qty       = 0
let taxexempt           = 0
//isNew Flag
let isNew = windowvar.isNew
shipping_method.ok = true
let customerCreditTotal = windowvar.customerCreditTotal;
let creditNoteForDeduct = 0;
//invoice_row
var inserted_data = Array();

//product List
var Strain_list       = $("#strain");
var P_type_list       = $("#p_type");
var QtyInput          = $("#qty");
var Unit_priceInput   = $("#unit_price");
var Sub_totalInput    = $("#sub_total");
var DiscountInput     = $("#discount");
var LessDiscountInput = $("#less_discount");
var TaxInput          = $("#tax");
var Adjust_priceInput = $("#adjust_price");
const submitBtnCreditNoteLabel = "Create SO (available credit = $"

//--------------------------tax allow part---------------------------------
    $('#tax_allow').click(() => {
        tax_allow = $('#tax_allow').prop('checked') == true?1:0
        if(tax_allow == 1)
            TaxInput.val(0)
        TaxInput.prop('disabled', $('#tax_allow').prop('checked'));
        CreateTable()
    })
//--------------------------/.tax allow part-------------------------------

//--------------------------Customer Part-----------------------------------
    $('.addCustomerBtn').click(() => {
        var customer_window = window.open('../customers')
        customer_window.onbeforeunload = function(){
            create_customer_list()
        }
    })

    $('#client').change(function(){
        $.ajax({
            url:'_customer_credit_note_total',
            type:'post',
            data:'id=' + $(this).val(),
            success:(res) => {
                listnerForCustomers($(this).val(),res.total)
            },
            error:() => {
                listnerForCustomers($(this).val(),0)
            }
        })

    })
    const listnerForCustomers = (customerID,totalPrice) => {
        if(customerID == 0)
        {
            Unit_priceInput.val(0)
            return false;
        }
        let client = clients[findIndexWithAttr(clients,'client_id',customerID)]
        let term = client.term != null?client.term.term:'No Term'
        sel_client = customerID
        $('.term_content').html(term)
        let salesrep = client.salesrep != null?client.salesrep:0
        $('#salesperson').val(salesrep).change()
        customerCreditTotal = totalPrice
        changeSubmitBtnLabel()
    }

    let setPrice = (basePrice) => {
        // let p_type = P_type_list.val()
        // let p_type_obj = p_types[findIndexWithAttr(p_types,'producttype_id',p_type )]
        // let promo = p_type_obj != null?p_type_obj.promovalue:null
        // if(promo != null)
        // {
        //     Unit_priceInput.prop('disabled', true)
        //     Unit_priceInput.val(promo)
        // }
        // else
        // {
        //     Unit_priceInput.prop('disabled', false)
        //     Unit_priceInput.val(0)
        // }
        // const strain = Strain_list.val()
        // const strain_obj = strains[findIndexWithAttr(strains,'itemname_id',strain )]
        // console.log(strain_obj)
        // const base_price = strain_obj != null?parseFloat(strain_obj.base_price):null
        // console.log(base_price)
        if(basePrice != null && basePrice > 0)
        {
            Unit_priceInput.prop('disabled', true)
            Unit_priceInput.val(basePrice)
        }
        else
        {
            Unit_priceInput.prop('disabled', false)
            Unit_priceInput.val(0)
        }
    }

    let create_customer_list = () => {
        $.ajax({
            url:'_form_customer_list',
            type:'post',
            success:(res) => {
                clients = res
            },
            error:(e) => {
                console.log(e.statusText)
                alert('Problem in commucating with server');
            }
        })
        let selected = ""
        let item_selector_html = '<option value="0"></option>'
        clients.forEach(function(item,index){
            if(item.client_id == sel_client) selected = 'selected';
            else selected = '';

            item_selector_html += '<option value="' + item.client_id + '" ' + selected +'>' + item.clientname + '</option>';
        })
        item_selector_html += '</select>'
        $('#client').html(item_selector_html)
    }
//--------------------------./Customer Part----------------------------------
//--------------------------Build Request Part-------------------------------------
    Strain_list.change(function(){
        OnChangeStrainOrPType()
        QtyInput.focus()
        // DiscountInput.val(0)
        validation_qty()
        // set_adjust_price(calc_adjust_price())
    })
    P_type_list.change(function(){
        OnChangeStrainOrPType()
        QtyInput.focus()
        validation_qty()
    })
    let OnChangeStrainOrPType = () => {
        let strain = Strain_list.val()
        let p_type = P_type_list.val()
        if(strain == 0 || p_type == 0)
        {
            return false
        }

        calc_units()
        let res;
        $.ajax({
            url:'_form_avaliable_qty',
            type:'post',
            async:false,
            data:'strain=' + strain + '&p_type=' + p_type + '&id='+invoice_id,
            success:(r) => {
                res = r
                inserted_data.forEach(element => {
                    if(element.strain == strain && element.p_type == p_type)
                    {
                        res.qty -= element.qty;
                    }
                })
                setPrice(r.basePrice)
            }
        })
        setQty(res.qty)
        $('#avaliable_weight').html(res.weight)
        taxexempt = res.taxexempt

    }
    let setQty = (qty) => {
        avaliable_qty = qty
        $('#avaliable_qty').html(qty)
        validation_qty()
    }
    let calc_units = () => {
        if(P_type_list.val() == 0)
            return false
        let p_unit = p_types[findIndexWithAttr(p_types,'producttype_id',P_type_list.val())].units
        p_unit = p_unit == null?1:p_unit
        $('#units').val(QtyInput.val() * p_unit)
        let base_price = parseFloat(Unit_priceInput.val())
        let cpu = 0
        if(base_price > 0)
        {
            cpu = base_price * parseFloat(QtyInput.val()) / parseFloat($('#units').val())
        }
        cpu = cpu.toFixed(2)
        $('#cpu').val(cpu)
    }
    let validation_qty = () => {
        if(avaliable_qty < $('#qty').val())
        {
            //$('#qty_error').html('*Quantity can not big than Avaliable Quantity')
            $('#qty').css('border-color', 'red')
            return false;
        }
        else
        {
            $('#qty_error').html('')
            $('#qty').css('border-color', '')
            return true
        }

    }
    let validation_cost = () => {
        // if(Unit_priceInput.val() < 0)
        // {
        //     $('#unit_price_error').html('*Price cannot be 0')
        //     $('#unit_price_error').css('border-color', 'red')
        //     return false;
        // }
        // else
        // {
        //     $('#unit_price_error').html('')
        //     $('#unit_price_error').css('border-color', '')
        //     return true
        // }
        return true;
    }
    let validation_tax = () => {
        if(parseFloat(TaxInput.val()) > parseFloat(LessDiscountInput.val()))
        {
            TaxInput.val(LessDiscountInput.val())
            $('#tax_error').html('*Tax cannot be big than Less Discount')
            $('#tax_error').css('border-color', 'red')
            return false;
        }
        else
        {
            $('#tax_error').html('')
            $('#tax_error').css('border-color', '')
            return true
        }
    }
//--------------------------./Build Request Part-----------------------------------
//--------------------------/Add Row-----------------------------------
    $("#add_row").click(function() {
        if(Strain_list.val() == 0)
        {
            alert('Select the Strain')
            return false
        }
        if(P_type_list.val() == 0)
        {
            alert('Select the Product Type')
            return false
        }
        if(Unit_priceInput.val() <= 0)
        {
            alert('Enter the Correct Price')
            return false
        }
        if(!validation_qty())
        {
            alert('Enter the Validate Quantity')
            return false
        }

        if(!validation_tax())
        {
            alert('Enter the Validate Tax')
            return false
        }
        let discount = get_discount(get_discount_id())
        let data = {}
        data.strain = Strain_list.val()
        data.p_type = P_type_list.val()
        data.description   = strains[findIndexWithAttr(strains,'itemname_id',data.strain)].strain
        data.description  += ',' + p_types[findIndexWithAttr(p_types,'producttype_id',data.p_type)].producttype
        data.unit_price    = parseFloat($("#unit_price").val()).toFixed(2)
        //check Strain Base Price
        let strainObj = strains[findIndexWithAttr(strains,'itemname_id',data.strain)]
        if(Unit_priceInput.val() < strainObj.base_price)
        {
            alert("This Strain's Price can not be less than " + strainObj.base_price)
            return false
        }

        data.sub_total     = parseFloat($("#sub_total").val()).toFixed(2)
        data.qty           = parseFloat($("#qty").val())
        data.units         = $("#units").val()
        data.cpu           = 0
        if(data.unit_price > 0)
        {
            data.cpu = data.sub_total / data.units
        }
        data.cpu = data.cpu.toFixed(2)
        data.discount_type = discount.type
        data.discount_label= discount.label
        data.discount_id   = discount.id
        data.discount_pro  = discount.pro
        data.discount      = parseFloat($("#discount").val()).toFixed(2)
        data.e_discount    = parseFloat($("#e_discount").val()).toFixed(2)
        data.tax           = parseFloat($('#tax').val()).toFixed(2)
        data.taxexempt     = taxexempt
        data.tax_note      = $('#tax_note').val()
        data.sub_total     = data.qty * data.unit_price
        data.sub_total     = data.sub_total.toFixed(2)
        data.less_discount = data.sub_total - data.discount - data.e_discount
        data.adjust_price  = parseFloat(data.less_discount) + parseFloat(data.tax)
        data.adjust_price  = data.adjust_price.toFixed(2)
        data.less_discount = data.less_discount.toFixed(2)
        check_item_duplicate(data).then(() => {
            inserted_data.push(data)
            CreateTable();
            ListenerForItemList();
            avaliable_qty -= data.qty;
            setQty(avaliable_qty)
        },() => {
            alert('This item is alreay exist');
        })
    });

    var check_item_duplicate = (data) => {
        return new Promise((resolve,reject) => {
            inserted_data.forEach(element => {

                if(element.strain == data.strain && element.p_type == data.p_type)
                {
                    //reject()
                }
            });

            resolve();

        })
    }
//--------------------------./Add Row-----------------------------------
//---------------------------Discount-----------------------------------

    $('#order_discount').change(() => {
        refresh_order_discount()
        calc_adjust_price()
    })
    $('#row_discount').change(() => {
        calc_adjust_price()
    })

    /**
     * 1.24 Added reset discount for entire order
     * called when 'entire discount' selector is changed
     */
    let refresh_order_discount = () => {
        let discount = get_discount(get_discount_id())
        inserted_data.forEach(element => {
            if(element.discount_type == 0)
            {
                element.discount_type = discount.type
                element.discount_label= discount.label
                element.discount_id   = discount.id
                element.discount_pro  = discount.pro
                element.discount      = element.sub_total * element.discount_pro / 100
                element.less_discount = element.sub_total - element.discount
                element.adjust_price  = element.less_discount + element.tax
            }
        })
        CreateTable()
    }

    /**
     * 1.24 Added Discount Checking
     * type
     *  1:row discount
     *  0:order discount
     * pro
     *  5%->percentage
     */
    let get_discount = (dis) => {

        let discount = []
        discount['type'] = '0'
        discount['label'] = ''
        discount['pro']  = 0
        discount['id']  = 0
        discount['obj']  = promos[ findIndexWithAttr(promos,'promoid',dis.id) ]
        if(discount['obj'] != null)
        {
            discount['id']    = discount['obj'].promoid
            discount['pro']   = parseFloat(discount['obj'].multiplier)
            discount['label'] = discount['obj'].name
            discount['type']  = dis.type
        }
        return discount
    }
    let get_discount_id = () => {
        /**
         * row discount is priority
         * and then entire discount
         */
        let row_discount =   $('#row_discount').val()
        let order_discount = $('#order_discount').val()
        let result = []
        result['type'] = 0
        result['id']   = 0

        if(row_discount != 0)
        {
            result['type'] = 1
            result['id'] = row_discount
        }
        else
        {
            if(order_discount != 0)
            {
                result['type'] = 0
                result['id'] = order_discount
            }
        }

        return result;
    }
//---------------------------./Discount---------------------------------
var calc_adjust_price = () => {
    var data = {}

    let cost     = Unit_priceInput.val()
    let qty      = QtyInput.val()
    let tax      = parseFloat(TaxInput.val())
    let discount = get_discount(get_discount_id())

    data.sub_total     = cost * qty;
    data.discount      = data.sub_total * discount.pro / 100.0
    data.less_discount = data.sub_total - data.discount// - data.e_discount
    data.adjust_price  = data.less_discount + tax

    data.sub_total     = data.sub_total.toFixed(2)
    data.discount      = data.discount.toFixed(2)
    data.less_discount = data.less_discount.toFixed(2)
    data.adjust_price  = data.adjust_price.toFixed(2)
    set_adjust_price(data)
    calc_units()
    return data;
}

let set_adjust_price = (data) => {
    $('#discount').val(data.discount)
    Sub_totalInput.val(data.sub_total)
    $('#cpu').val(data.cpu)
    LessDiscountInput.val(data.less_discount)
    Adjust_priceInput.val(data.adjust_price)
}

//----------------------Input Realtime calculation------------------------
QtyInput.on('input',function(){
    if($(this).val() < 1)
        $(this).val(1)
    calc_adjust_price()
    validation_qty()
})
$('#units').on('input',function(){
    if($(this).val() < 1)
        $(this).val(1)
})
Unit_priceInput.on('input',function(){
//    if($(this).val() < 0.000001)
 //       $(this).val(0)
    calc_adjust_price()
    validation_qty()
    validation_cost()
});
TaxInput.on('input',function(){
    if($(this).val() < 0.000001)
        $(this).val(0)
    validation_tax()
    calc_adjust_price()
});
//----------------------./Input Realtime calculation------------------------

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
    let base_price = 0
    let base_price_for_tax = 0
    let base_price_for_promotion = 0
    var discounted = 0
    let e_discount = 0
    let taxed = 0
    let adjust_price = 0

    $.each(inserted_data,function(index,element) {

        base_price += parseFloat(element.sub_total)
        if(element.taxexempt != 1)
        {
            base_price_for_tax += parseFloat(element.sub_total)
        }
        discounted += parseFloat(element.discount)
        e_discount += parseFloat(element.e_discount)
        adjust_price += parseFloat(element.adjust_price)
    })
    base_price_for_promotion = base_price_for_tax
    taxed = (base_price - discounted) * 0.27
    taxed = tax_allow == 1?0:taxed
    adjust_price += taxed
    //base price
    $("#total_base_price").text(base_price.toFixed(2))
    //total discounte
    $("#total_discounted").text(discounted.toFixed(2))
    //total extra discount
    $("#total_e_discounted").text(e_discount.toFixed(2))
    //total promotion
    $("#total_promotion").text((base_price - base_price_for_promotion).toFixed(2))
    //total discount
    $("#total_extended").text((base_price - discounted - e_discount).toFixed(2))
    //CA Exercise Tax
    $("#total_tax").text(taxed.toFixed(2))
    //Adjust Total Price
    $("#total_adjust_price").text(adjust_price.toFixed(2))

    return adjust_price
}
var create_row = (index,data) => {
    var html = "";

    html += "<tr item_id='" + index +"'>"
    html += "<td>" + (index + 1) + "</td>"
    html += "<td>" + data.description + "</td>"
    html += "<td>" + data.qty + "</td>"
    html += "<td>" + data.units + "</td>"
    html += "<td>" + data.unit_price + "</td>"
    html += "<td>" + data.sub_total + "</td>"
    html += "<td>" + data.cpu + "</td>"
    html += "<td>" + data.discount + "</td>"
    let temp = "<td>No Discount</td>"
    html += "<td>" + data.discount_label + "</td>"
    html += "<td>" + data.e_discount + "</td>"
    html += "<td>" + data.less_discount + "</td>"
    html += "<td>" + (data.tax_note == null?'':data.tax_note) + "</td>"
    html += "<td>" + data.adjust_price + "</td>"
    html += "<td><button class='btn btn-info btn-xs btn_item_edit'>"
    html += "<i class='fa fa-edit' aria-hidden='true'></i>&nbsp;edit</button></td>"
    html += "<td><button class='btn btn-danger btn-xs btn_item_remove'>"
    html += "<i class='fa fa-trash' aria-hidden='true'></i>&nbsp;Remove</button></td>"
    html += "</tr>"
    return html;
}

var ListenerForItemList = () =>{
    QtyInput.val(1)
    $('#units').val(1)
    setPrice()
    Sub_totalInput.val(0)
    $('#row_discount').val('0').change()
    DiscountInput.val(0)
    LessDiscountInput.val(0)
    TaxInput.val(0)
    $('#tax_note').val('')
    Adjust_priceInput.val(0)
};

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

    if($("#client").val() == '0')
    {
        alert('please select the Customer');
        swal("please select the Customer", "", "warning")
        return;
    }
    if(customerCreditTotal > 0)
    {
        $('#modal_credit_note').modal('show')
    }
    else
    {
        showConfirmDialog()
    }
});
$('#btnSaveCreditNote').on('click',() => {
    let creditNote = parseFloat($('#txtCreditNote').val())
    if(creditNote > customerCreditTotal)
    {
        alert('You can not enter big value than original value')
        $('#modal_credit_note').hide()
        return false
    }
    const subTotal = parseFloat($("#total_extended").text())
    if(creditNote > subTotal)
    {
        creditNote = subTotal
    }
    console.log(creditNote)
    creditNoteForDeduct = creditNote
    $('#modal_credit_note').hide()
    showConfirmDialog()
})
const showConfirmDialog = () => {
    swal({
        title: "Are You Sure",
        text: "Are You going to save the Sales order?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: true,
        showLoaderOnConfirm: true
      }, function () {
        submit_invoice();
    });
}
var submit_invoice = () => {
    let term = clients[findIndexWithAttr(clients,'client_id',$('#client').val())].term
    term = term == null?-1:term.term_id
    var post_data = {
        'number':$("#number").val(),
        'customer_id':$("#client").val(),
        'date': $("#date").val(),
        'note': $('#note').val(),
        'fulfillmentnote':$('#fulfillmentnote').val(),
        'distuributor_id':0,
        'total':calc_total(),
        'tax_allow':tax_allow,
        'items':inserted_data,
        'term_id':term,
        'salesperson_id':$("#salesperson").val(),
        'id':invoice_id,
        'mode':mode,
        'shipping_method':shipping_method,
        'creditNoteForDeduct':creditNoteForDeduct
    };
    //console.log(post_data);
    //return false;
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
            swal("", "An error has occured while saving Invoice!", "warning")
        },

    });
}

$("#confirm_modal").on("hidden.bs.modal", function () {
    location.href="create"
});


$("#add_shipping_method").click(function(){
    $("#modal_shipping_method").modal();
})

//----------------------------------start inserted items---------------------------------------------------
$("#inserted_table > tbody").on("click", ".btn_item_edit", function(){
    var item_id = parseInt($(this).parents('tr').attr('item_id'))
    let data = inserted_data[item_id]
    Strain_list.val(data.strain).change()
    P_type_list.val(data.p_type).change()
    QtyInput.val(data.qty)
    $('#units').val(data.units)
    Unit_priceInput.val(data.unit_price)
    Sub_totalInput.val(data.sub_total)
    let row_discount_id = 0
    if(data.discount_type == 1)
    {
        row_discount_id = data.discount_id
    }
    $('#row_discount').val(row_discount_id).change()
    DiscountInput.val(data.discount)
    LessDiscountInput.val(data.less_discount)
    TaxInput.val(data.tax)
    $('#tax_note').val(data.tax_note)
    Adjust_priceInput.val(data.adjust_price)

    inserted_data.splice(item_id,1);
    CreateTable();
    $(this).parents("tr").remove();
})
$("#inserted_table > tbody").on("click", ".btn_item_remove", function(){
    var item_id = parseInt($(this).parents('tr').attr('item_id'));
    if(Strain_list.val() == inserted_data[item_id].strain && P_type_list.val() == inserted_data[item_id].p_type)
    {
        avaliable_qty += parseFloat(inserted_data[item_id].qty)
        setQty(avaliable_qty)
    }
    inserted_data.splice(item_id,1);
    CreateTable();
    $(this).parents("tr").remove();
})
//---------------------------------- end inserted items------------------------------------------
ListenerForItemList();
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
//-----------------------end keyup event chain--------------------------
$(function(){
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });
$("body").addClass('fixed');
    $('.select2').select2();

    $('.datepicker').datepicker({
        autoclose: true
    })
    $('#aInbTable').DataTable()

    changeSubmitBtnLabel();
})

var initOp = () => {

    if(Invoice_item == null)
    {
        return;
    }
    for(var i =0; i < Invoice_item.length; i ++)
    {
        var data = {};
        data.strain        = Invoice_item[i].strain
        data.p_type        = Invoice_item[i].p_type
        data.description   = strains[findIndexWithAttr(strains,'itemname_id',data.strain)] != undefined?strains[findIndexWithAttr(strains,'itemname_id',data.strain)].strain:'Deleted'

        let temp2  = p_types[findIndexWithAttr(p_types,'producttype_id',data.p_type)]
        temp2 = temp2 != undefined?temp2.producttype:'Deleted'
        data.description  += ',' + temp2
        data.unit_price    = parseFloat(Invoice_item[i].unit_price)
        data.qty           = Invoice_item[i].qty
        data.units         = Invoice_item[i].units
        data.cpu           = 0
        if(data.unit_price > 0)
        {
            data.cpu = data.unit_price / parseFloat(data.units)
        }
        data.cpu           = data.cpu.toFixed(2)
        data.discount      = Invoice_item[i].discount
        data.e_discount     = parseFloat(Invoice_item[i].e_discount)
        data.sub_total     = data.qty * data.unit_price
        data.less_discount = data.sub_total - data.discount - data.e_discount
        data.less_discount = data.less_discount.toFixed(2)
        data.tax           = Invoice_item[i].tax
        data.taxexempt     = Invoice_item[i].taxexempt
        data.tax_note      = Invoice_item[i].tax_note
        data.adjust_price  = parseFloat(data.less_discount) + data.tax
        let discount_param = []
        discount_param['id']   = Invoice_item[i].discount_id
        discount_param['type'] = Invoice_item[i].discount_type
        let discount = get_discount(discount_param)
        data.discount_label = discount.label
        data.discount_id    = discount.id
        data.discount_pro   = discount.pro
        data.discount_type  = discount.type
        inserted_data.push(data);
    }

    CreateTable();
    $("#save_shipping_method").click();
    $("#modal_shipping_method").modal('hide');
};
initOp();
const changeSubmitBtnLabel = () =>
{
    //set Submit label
    if(customerCreditTotal > 0)
        $('.makeBtn').text(submitBtnCreditNoteLabel + customerCreditTotal+')')
    else
        $('.makeBtn').text(submitBtnLabel)
}
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
