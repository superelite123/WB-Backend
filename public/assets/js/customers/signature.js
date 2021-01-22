$(() => {
    var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
      backgroundColor: 'rgba(255, 255, 255, 0)',
      penColor: 'rgb(0, 0, 0)'
    });
    var saveButton = document.getElementById('save');
    var cancelButton = document.getElementById('clear');
    
    saveButton.addEventListener('click', function(event) {
      
      if (signaturePad.isEmpty()) {
          return alert("Please provide a signature first.");
        }
        
        if(!confirm('Are your going to save this as your signature to thi Invoice/n'))
        {
            return false;
        }
        var data = signaturePad.toDataURL('image/png');
        var img_data = data.replace(/^data:image\/(png|jpg);base64,/, "");
        
        $.ajax({
          url: 'save_sign',
          data: { img_data:img_data,id:invoice_id },
          type: 'post',
          dataType: 'json',
          async:false,
          success: function (response) {
            alert('we saved your signature');
             if(response == '1') location.reload();
          },
          error:function(e)
          {
            console.log(e);
          }
        });
        
    });
    
    cancelButton.addEventListener('click', function(event) {
      signaturePad.clear();
    });
  })
  function printPage() {
  
    window.print();
  
  }
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
    