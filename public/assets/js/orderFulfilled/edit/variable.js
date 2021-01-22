let Invoice             = windowvar.invoice
let Inventory           = windowvar.inventory
let Discounts           = windowvar.discounts
let ChkTaxAllow         = $('#tax_allow')
let ListClient          = $('#customers')
let ListDistributor     = $('#distributors')
let TxtOrderNote        = $('#orderNote')
let inventory_table     = $('#inventory_table')
let doneTypingInterval  = 500
let typingTimer
let newInventory        = null
let selectedInventory   = -1
let topInfo = {
    clientId:Invoice.customer_id,
    distributorId:Invoice.distuributor_id,
    note:Invoice.note,
    tax_allow:Invoice.tax_allow,
    id:Invoice.id
}
var findObjWithAttr =  function(array, attr, value){
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] == value) {
            return array[i];
        }
    }
    return null;
};
var findIndexWithAttr =  function(array, attr, value){
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] == value) {
            return i;
        }
    }
    return -1;
};
let noticeMetrcScanning = () => {
    $.growl.notice({ message: "One Metrc Tag is scanned" });
}
let getDiscount = (discount_id) =>
{
    let result = {}
    let discount    = findObjWithAttr(Discounts,'promoid',discount_id)
    result.discount_pro   = discount != null?discount.multiplier:0
    result.discount_id    = discount != null?discount.promoid:0
    result.discount_label = discount != null?discount.name:''
    
    return result
}
/**
 * @calc Row Info from 
 * Input
 *  qty,units,base price,discount,w
 * Result:
 *  + cpu,Sub Total,Extended,Adjust Totoal
 */
let calcRowInfo =  (data) => {
    let newData = {}
    newData.units           = data.qty * data.units;
    newData.cpu             = 0
    newData.base_price      = data.qty * data.unit_price
    newData.discount        = newData.base_price * data.discount_pro / 100
    newData.extended        = newData.base_price - newData.discount
    newData.adjust_price    = newData.extended

    if(newData.units > 0)
    {
        newData.cpu = newData.base_price / parseFloat(newData.units)
    }
    if(data.isSingle == 0)
    {
        newData.weight = 0
        data.child_items.forEach(element => {
            newData.weight += parseFloat(element.weight)
        })
    }
    
    newData.base_price     = newData.base_price.toFixed(2)
    newData.discount       = newData.discount.toFixed(2)
    newData.extended       = newData.extended.toFixed(2)
    newData.adjust_price   = newData.adjust_price.toFixed(2)
    newData.cpu            = newData.cpu.toFixed(2)
    return newData
}