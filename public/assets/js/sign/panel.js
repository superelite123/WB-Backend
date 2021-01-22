let sign_img_data = null
var dClearButton
var dSignaturePad
var signaturePad
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
        //   alert('Enter the Cash Serial Bag');
        //   return
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
        cash_serial:d_cash_seiral,
        clientData:null
    }

    //check client sign input data
    if(validationClient() == 0)
    {
        let imgData = signaturePad.toDataURL('image/png');
        imgData = imgData.replace(/^data:image\/(png|jpg);base64,/, "");
        const sign_date = $("#sign_date").val()
        const sign_name = $("#sign_name").val()
        data.clientData = {
            img_data:imgData,
            sign_date:sign_date,
            sign_name:sign_name
        }
    }
    console.log(data)
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
let completeRejection = () => {
    swal({
        title: "Complete Rejection",
        text: "Type 'DELETE' on below Input",
        type: "input",
        showCancelButton: true,
        closeOnConfirm: false,
        inputPlaceholder: "Type DELETE word"
      }, function (inputValue) {
        if (inputValue === false) return false;
        if (inputValue === "") {
          swal.showInputError("You need to write DELETE!");
          return false
        }
        if(inputValue != 'DELETE')
        {
            swal.showInputError("You need to write DELETE!");
            return false
        }
        $.ajax({
            url:'../../order_fulfilled/_complete_rejection/' + invoice_id,
            type:'get',
            success:(res) => {
                location.href='../../signature/home'
            },
            error:(e) => {

            }
        })
    });
}

let partialRejection = () => {
    location.href='../../order_fulfilled/edit/' + invoice_id
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
const validationClient = () => {
    const sign_date = $("#sign_date").val()
    const sign_name = $("#sign_name").val()
    if(signaturePad == null || signaturePad.isEmpty())
    {
        return 1;
    }
    if(sign_date == "" || sign_name == "")
    {
        return 2;
    }
    return 0;
}
$(() => {
    if(document.getElementById('signature-pad') != null)
    {
        signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
          });
    }
    dSignaturePad = new SignaturePad(document.getElementById('dSignature-pad'), {
      backgroundColor: 'rgba(255, 255, 255, 0)',
      penColor: 'rgb(0, 0, 0)'
    });
    var saveButton = document.getElementById('save');
    var cancelButton = document.getElementById('clear');
    dClearButton = document.getElementById('dClear');
    if(saveButton != null)
    {
        saveButton.addEventListener('click', function(event) {
            const validatiaonResult = validationClient()
            //display alert
            if(validatiaonResult != 0)
            {
                if(validatiaonResult == 1)
                    alert("Please provide a signature first.")
                if(validatiaonResult == 2)
                    alert("Enter the NAME and Date")
                return false
            }
            const sign_date = $("#sign_date").val()
            const sign_name = $("#sign_name").val()
            swal({
                title: 'Confirm',
                text: "Your Signature will now be saved to this invoice",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function () {
                var data = signaturePad.toDataURL('image/png');
                sign_img_data = data
                var img_data = data.replace(/^data:image\/(png|jpg);base64,/, "");
                $.ajax({
                    url: '_save_sign',
                    data: { img_data:img_data,id:invoice_id,sign_date:sign_date,sign_name:sign_name },
                    type: 'post',
                    dataType: 'json',
                    async:false,
                    success: function (response) {
                        swal('Thanks!', 'We saved your signature', "success")
                        location.href='../../signature/home'
                    },
                    error:function(e)
                    {
                        console.log(e);
                    }
                });
            })
        });
    }
    if(saveButton != null)
    {
        cancelButton.addEventListener('click', function(event) {
        signaturePad.clear();
        });
    }
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
