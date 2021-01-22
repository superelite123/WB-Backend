let signaturePad
let image
let onSubmit = () => {
    let data = signaturePad.toDataURL('image/png')
    let img_data = data.replace(/^data:image\/(png|jpg);base64,/, "")
    $('#signature_image').val(img_data)
    $('#nda_form').submit()
}
let getSignature = function() {
    var dataUrl = canvas.toDataURL();
    dataUrl = dataUrl.replace(/^data:image\/(png|jpg);base64,/, "");
    return dataUrl
}
let submitData = () => {
    let postData = {}
    let isSigned = canvas.getContext('2d')
                         .getImageData(0, 0, canvas.width, canvas.height).data
                         .some(channel => channel !== 0)
    if(!isSigned)
    {
        alert('Please sign at first')
        return;
    }
    postData.signImage = getSignature()
    //customerName
    if($('#customerName').val() == '')
    {
        alert('Enter Name');
        $('#customerName').focus()
        return
    }
    postData.customerName = $('#customerName').val()
    //title
    if($('#title').val() == '')
    {
        alert('Enter Title');
        $('#title').focus()
        return
    }
    postData.title = $('#title').val()
    //companyName
    if($('#companyName').val() == '')
    {
        alert('Enter Company Name');
        $('#companyName').focus()
        return
    }
    postData.companyName = $('#companyName').val()
    //email
    if($('#email').val() == '')
    {
        alert('Enter Email');
        $('#email').focus()
        return
    }
    if(!validateEmail($('#email').val()))
    {
        alert('Enter Valid Email');
        $('#email').focus()
        return
    }
    postData.email = $('#email').val()
    //state
    if($('#state').val() == null)
    {
        alert('Select State');
        $('#state').focus()
        return
    }
    postData.state = $('#state').val()
    //city
    if($('#city').val() == '')
    {
        alert('Enter City');
        $('#city').focus()
        return
    }
    postData.city = $('#city').val()
    //street
    if($('#street').val() == '')
    {
        alert('Enter Street');
        $('#street').focus()
        return
    }
    postData.street = $('#street').val()
    //zip
    if($('#zip').val() == '')
    {
        alert('Enter Zip');
        $('#zip').focus()
        return
    }
    postData.zip = $('#zip').val()
    //customerType
    if($('#customerType').val() == null)
    {
        alert('Select Customer Type');
        $('#customerType').focus()
        return
    }
    postData.customerType = $('#customerType').val()
    if(captureData == null)
    {
        alert('Please Provide Copy of Your ID')
        return false
    }
    postData.captureData = captureData.replace(/^data:image\/(png|jpg);base64,/, "")
    swal({
        title: "Are You Sure",
        text: "You are about to agree to NDA Agreement",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        $.ajax({
            url:'_store_nda',
            type:'post',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(postData),
            success:(res) => {
                setTimeout(function() {
                    swal({
                        title: "Thank you!",
                        text: "We saved your signed NDA and have emailed you a copy. Your Photo ID has been encrypted and will digitally destroy upon sign out.",
                        type: "success"
                    }, function() {
                       location.href='nda_index'
                    });
                }, 200)
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
}
let validateEmail = (email) => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
$(() => {
    var clearBtn = document.getElementById("sig-clearBtn");
    clearBtn.addEventListener("click", function(e) {
        getSignature()
        clearCanvas()
        ctx.lineWidth = 4
        //$('#sig-image').attr("src", "")
    }, false)
    $('.select2').select2()
    startup()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});