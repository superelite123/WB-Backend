let items = [];
let id = null
$('.addBtn').on('click',() => {
    const item = {
        strain:parseInt($('#strain').val()),
        p_type:parseInt($('#pType').val()),
        price:parseFloat($('#price').val()) * $('#qty').val(),
        qty:parseInt($('#qty').val()),
    }
    item.price = item.price.toFixed(2)
    if(item.price < 0.00001 || item.qty < 1)
    {
        alert('Enter correct value');
        return false
    }
    let bDuplicated = false
    items.forEach(element => {
        if(element.strain == item.strain && element.p_type == item.p_type)
        {
            bDuplicated = true
        }
    });
    if(bDuplicated)
    {
        alert('Duplicated Item')
        return false
    }
    items.push(item)
    createTable()
})
const createTable = () => {
    let html = ''
    let total = 0
    if(items.length > 0)
    {

        items.forEach((element,index) => {
            const btnDelete = "<button class=\"btn btn-danger btn-sm btnRemoveChild\"><i class=\"fas fa-times\"></i></button>"
            html += "<tr item_id=\"" + index + "\">"
            html += '<td>' + (index + 1) + '</td>'
            html += '<td>' + getInventoryLabel(element) + '</td>'
            html += '<td>' + element.qty + '</td>'
            html += '<td>' + element.price + '</td>'
            html += '<td>' + btnDelete + '</t>'
            html += '</tr>'

            total += parseFloat(element.price)
        });
    }
    else
    {
        html += '<tr><td colspan=3 style="text-align:center"><h4>No Data</h4></td></tr>'
    }
    $('#tblInventory > tbody').html(html)
    $('#totalPrice').val(total.toFixed(2))
}
$('#tblInventory').on('click','.btnRemoveChild',function() {
    const index = parseInt($(this).parents('tr').attr('item_id'))
    items.splice(index,1)
    createTable()
})
$('#btnSubmit').on('click',() => {
    const reason = $('#reason_note').val() != null ? $('#reason_note').val() : 0;
    const detail = $('#detail').val();
    if(reason == 0 && detail == '')
    {
        alert('You should input Credit Reason or Further Detail')
        return false
    }
    const total = parseFloat($('#totalPrice').val());
    if(total > orderTotal)
    {
        alert('Credit Note\'s Total can\'t be bigger than Invoice Total')
        return false
    }
    const postData = {
        invoice_id:orderID,
        customer_id:customerID,
        total_price:total,
        items:items,
        reason_id: reason,
        detail: detail,
    }
    // if(postData.reason_id === null)
    // {
    //     alert('Select Credit Note Reason')
    //     return false
    // }
    if(postData.total_price < 0.0001)
    {
        alert('Enter correct value')
        return false
    }
    swal({
        title: "New Credit Note",
        text: "You are about to add credit Note",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url:'../store',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(postData),
            type:'post',
            success:(res) => {
                swal('Thanks!', 'Credit Note is addedd Successfully', "success")
                setTimeout(() => {
                    window.close()
                }, 1500);
                // id = res.id
                // $('.btn-email').prop('disabled', false)
                // $('.btn-pdf').prop('disabled', false)
                // $('#btnSubmit').prop('disabled', true)
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    })

})
$('.btn-pdf').click(() => {
    location.href="../download/" + id
})
$('.btn-email').click(() => {
    swal({
        title: "Email",
        text: "Enter Email Address",
        type: "input",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        inputPlaceholder: "please enter email address"
      }, function (inputValue) {
        if (inputValue === false) return false;
        if (inputValue === "") 
        {
          swal.showInputError("please correct email address");
          return false
        }
        let emails = inputValue.split(',').filter(email => validateEmail(email))
        console.log(emails)
        if(emails.length == 0)
        {
            swal.showInputError("No Valid Email Address!");
            return false
        }

        if(id == null)
        {
            swal.showInputError("No Selected Credit Note!");
            return false;
        }

        const post_data = {
            id:id,
            emails:emails
        }
        $.post({
            url:'../email',
            headers:{"content-type" : "application/json"},
            data: JSON.stringify(post_data),
            success:(res) => {
                swal('Success!', 'Email is sent Successfully', "success")
            },
            error:(e) => {
                swal(e.statusText, e.responseJSON.message, "error")
            }
        })
    });
})
const getInventoryLabel = (item) =>
{
    let strainLabel = "",pTypeLabel = ""
    strains.forEach(element => {
        if(element.itemname_id == item.strain)
        {
            strainLabel = element.strain
        }
    });
    pTypes.forEach(element => {
        if(element.producttype_id == item.p_type)
        {
            pTypeLabel = element.producttype
        }
    });
    return strainLabel + ',' + pTypeLabel;
}
let validateEmail = (email) => {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
$(() => {
    $('.select2').select2();
    createTable()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
