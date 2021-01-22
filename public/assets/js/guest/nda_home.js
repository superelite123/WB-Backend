let validateEmail = (email) => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
$('#inEmail').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        checkEmail()
    }
});
let getSignature = function() {
    var dataUrl = canvas.toDataURL();
    dataUrl = dataUrl.replace(/^data:image\/(png|jpg);base64,/, "");
    return dataUrl
}
let _checkEmail = () => {
    return new Promise(function(fulfill){
        let email = $('#inEmail').val()
        if(!validateEmail(email))
        {
            alert('Please Enter the Validate Email Address')
            $('#inEmail').focus()
            return false
        }
        $.get({
            url:'_nda_email_check/' + email,
            success:(res) => {
                fulfill(res)
            },
            error:(e) =>
            {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
}
let handleNewVisitor = () => {
    
    swal({
        title: "User not in System",
        text: "Please Sign Up Again",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function (confirm) {
        if(confirm)
        {
            location.href='nda_page'
        }
    })
}
let checkEmail = () => {
    _checkEmail().then(function(res){
        if(res == '1')
        {
            $.growl.notice({ message: "Please sign to continue" })
            $('.signature-panel').show()
        }
        if(res != '1')
        {    
            handleNewVisitor()
        }
    })
}
let onSubmit = () => {
    _checkEmail().then((res) => {
        if(res == '1')
        {
            $('.signature-panel').show()
            _submit()
        }
        else{
            handleNewVisitor()
        }
    })
}
let _submit = () => {
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
    postData.email = $('#inEmail').val()
    swal({
        title: "Are You Sure",
        text: "You are about to agree to NDA Agreement",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
      }, function () {
        $.ajax({
            url:'_store_nad_e',
            type:'post',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(postData),
            success:(res) => {
                setTimeout(function() {
                    swal({
                        title: "Thank You",
                        text: "We saved your signed NDA and have emailed you a copy. Your Photo ID has been encrypted and will digitally destroy upon sign out.",
                        type: "success"
                    }, function() {
                        location.href='nda_index'
                    });
                }, 200);
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })
}
$(() => {
    var clearBtn = document.getElementById("sig-clearBtn");
    clearBtn.addEventListener("click", function(e) {
        getSignature()
        clearCanvas()
        ctx.lineWidth = 4
    }, false)
    $('#inEmail').focus()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});