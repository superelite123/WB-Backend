let clocking_harvesters = windowvar.clocking_harvesters
$('#btn_clock_in').click(function(){
    let user_id = $('#harvester').val()
    if(user_id == '0')
    {
        swal("Select the Correct User", "", "info")
        return false
    }
    let flag = false
    clocking_harvesters.forEach(element => {
        if(element.user_id == user_id)
            flag = true
    });

    if(flag)
    {
        swal("You are already clocked in!", "", "info")
        setTimeout(() => { location.reload() }, 1000);

        return false
    }

   swal({
        title: "Are You Sure",
        text: "You are about to clock in",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        closeOnCancel: false,
        showLoaderOnConfirm: false
        },
        function (res) {
            if(!res)
            {
                setTimeout(() => { location.reload();swal.close() }, 1000);
                return false
            }
            $.get({
                url:'_set_clock_in',
                async:false,
                data:'user_id='+user_id + '&status=1',
                success:(res) => {
                    swal("You are clocked in now!", "", "success")
                    clocking_harvesters = res
                    createTable()
                    //location.reload()
                    setTimeout(() => { location.reload(); }, 1000);
                },
                error:(e) => {
                    swal("Error has been happened!", "", "danger")
                }
            })
    })
})

let createTable = () => {
    let html = ''
    let cnt = 1
    if(clocking_harvesters.length == 0)
        html = '<tr><td style="text-align:center" colspan=4>No Clocked In User</td></tr>'

    clocking_harvesters.forEach(element => {
        html += '<tr user_id="' + element.user_id + '">'
        html += '<td>' + element.user.firstname + element.user.lastname + '</td>'
        html += '<td>' + element.start_time + '</td>'
        html += '<td><button class="btn btn-info btn_clock_out">Clock Out</button></td>'
        html += '</tr>'
    })

    $('#tbl_clocked > tbody').html(html)
}

const switchTab = (mode) =>
{
    let url = window.location.href.split('?')[0];
    if (url.indexOf('?') > -1)
    {
        url += '&mode=' + mode
    }
    else
    {
        url += '?mode=' + mode
    }
    window.location.href = url;
}

$('#tbl_clocked tbody').on('click', '.btn_clock_out', function () {
    let tr = $(this).parents('tr');
    let user_id = parseInt(tr.attr('user_id'));
    swal({
        title: "Are You Sure",
        text: "You are about to clock out",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: false
        },
        function () {
            $.get({
                url:'_set_clock_in',
                async:false,
                data:'user_id='+user_id + '&status=0',
                success:(res) => {
                    swal("You are clocked out now!", "", "success")
                    // clocking_harvesters = res
                    // createTable()
                    location.reload()
                },
                error:(e) => {
                    swal("Error has been happened!", "", "danger")
                }
            })
    })
})

$(() => {
    createTable()
    setInterval(() => {
        let d = new Date()
        let html = ''
        html +=d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds()
        $('#now_time').html(html)
    }, 1000);
    $('.select2').select2()
    $("body").addClass('fixed');
})
