let sign_img_data = null
var dClearButton
var dSignaturePad
let collectMoney = () => {
  //check image data
  if (dSignaturePad.isEmpty()) {
    return alert("Please provide a signature first.");
  }
  //check d name
  let d_sign_name = $("#d_sign_name").val()
  if(d_sign_name == "")
  {
    alert("Enter Your NAME")
    return
  }

  //check serial
  let d_cash_seiral = $('#cash_serial').val()
  if(d_cash_seiral == '')
  {
      alert('Enter the Cash Serial Bag');
      return
  }
  //get Amount and Date
  let amountSubTotal  = $('#inputTotalCollect').val()
  let amountTax       = $('#inputTaxCollect').val()
  if(amountTax <= 0 && amountSubTotal <= 0)
  {
    swal('Warning', 'Can not collect $0', "warning")
    return false
  }
  let cDate           = $('#inputCollectionDate').val()

  var img_data = dSignaturePad.toDataURL('image/png');
  img_data = img_data.replace(/^data:image\/(png|jpg);base64,/, "");
  let data = {
    id:invoice_id,
    amountSubTotal:amountSubTotal,
    amountTax:amountTax,
    cDate:cDate,
    signImage:img_data,
    dPersoname:d_sign_name,
    cash_serial:d_cash_seiral
  }
  swal({
    title: "Sub Total:" + amountSubTotal + '  Tax:' + amountTax,
    text: "You are about to Collect Money",
    type: "info",
    showCancelButton: true,
    closeOnConfirm: false,
    showLoaderOnConfirm: true
  }, function () {
    $.ajax({
      url:'_collect_money',
      type:'post',
      headers:{"content-type" : "application/json"},
      data: JSON.stringify(data),
      success:(res) => {
        swal('Thanks!', 'We saved your payment', "success")
        location.reload()
      },
      error:(e) => {
        swal(e.statusText, e.responseJSON.message, "error")
      }
    })
  })
}
/**
 * Handle selecting delivery option
 */
$('#btnSaveDOption').click(() => {
  $.get({
    url:'../../set_order_delivery_status/' + invoice_id + '/' + $('#dOptions').val(),
    success:(res) => {
      $('#modalDeliveryOption').modal('hide')
      if(res == 1)
        swal('Success', 'Success on Deliver', "success")
      if(res == 2)
        swal('Notice', 'This Order has been set as Partial Rejection', "info")
      if(res == 3)
        swal('Notice', 'This Order has been set as Rejection', "info")
      //set imgae data to img tag
      document.getElementById('sign_img').src = sign_img_data
      //hide noimage label
      $('#no_img_label').hide()
      signaturePad.clear()
    },
    error:(e) => {
      swal(e.statusText, e.responseJSON.message, "error")
    }
  })
})
let onDeleteP = (id) => {
 if(!confirm('You are going to Delete this Payment?'))
 {
   return false
 }
 $.get({
   url:'_delete_payment/' + id,
   success:() => {
    alert('Success on Deleting')
    location.reload()
   },
   error:(e) => {
     alert("Error occured with server");
   }
 })
}
$(() => {
    dSignaturePad = new SignaturePad(document.getElementById('dSignature-pad'), {
      backgroundColor: 'rgba(255, 255, 255, 0)',
      penColor: 'rgb(0, 0, 0)'
    });
    dClearButton = document.getElementById('dClear');
    $('#dClear').click(() => {
      dSignaturePad.clear();
    })
  })
  function printPage() {

    window.print();

  }
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
