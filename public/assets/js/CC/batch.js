var batch_id;
var weight_remain = 0;
var send_fg = false;

var msg_invalid_qty = "Quantity must be greater than 0";
var msg_invalid_weight = "weight must be greater than 0";
var msg_big_weight = "weight used must be less than Weightremain";
var msg_select_type = "please select the asset type";

var input_qty = $("#qty");
var input_weight = $("#w_remain");
var input_type = $("#type_id");

var error_message = $("#error_message");
var panel_error_message = $("#invalid_value");

//show assets_generate_modal
function assets_generate_modal(batchid,weightremain,um_batch)
{
    send_fg = false;
    
    $("#show_modal").click();
    input_qty.focus();

    $("#um_batch").text("unit used: " + um_batch);
    
    input_weight.attr('placeholder','Enter the weight reference for ' + um_batch);
    
    panel_error_message.hide();
    $("#weight_remain").text('Weight remaining is ' + weightremain);
    batch_id = batchid;
    weight_remain = parseFloat(weightremain);
    
}
$("#send_fg").click(function(){
    send_fg = $(this).prop('checked');
});
$("#close_alert").click(function(){
    panel_error_message.hide();
});

$('.saveBtn').click(function(){
    
    var qty = input_qty.val();

    var w_remain = input_weight.val();
    
    var type_id = input_type.val();

    if(qty <= 0 || w_remain <= 0)
    {
        if(qty <= 0)
        {
            error_message.text(msg_invalid_qty);
        }
        else
        {
            error_message.text(msg_invalid_weight);
        }
        
        panel_error_message.show();
        return;
    }

    if(w_remain > weight_remain)
    {
        error_message.text(msg_big_weight);
        panel_error_message.show();
        return;
    }

    if(type_id == 0)
    {
        $("#error_message").text(msg_select_type);
        panel_error_message.show();
        return;
    }

    if(confirm('Do you really generate the Assets?'))
    {
        if(send_fg)
        {
            if(!confirm('Do you really send these to Finished Goods directly?'))
            {
                return;
            }

        }
        var send_fg_val = send_fg?1:0;
        $.ajax({
            url:'assets_generate',
            data:'batch_id='+batch_id+'&qty='+qty+"&w="+w_remain+"&type_id="+type_id + "&send_fg=" + send_fg_val,
            type:'post',
            async:false,
            success:function(res)
            {
                if(res == 1)
                {
                    location.reload();
                }
            }
        });
    }


});

$('.select2').select2();

