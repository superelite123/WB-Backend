let s_date = windowvar.start_date
let e_date = windowvar.end_date
let harvest_table

let createTable = () => {
   let date_range = $("#reservation").val()
   let mode = $("#mode").val()
    harvest_table = $('#history_table').DataTable({
        "ajax": {
            url: "get_history_data",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range
                d.mode = mode
            },
            dataSrc: function ( json ) {
                
                for ( let i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1

                    if(json[i].dry == null)
                    {
                        json[i].dynamics = '<font style="color:#00acd6;font-weight:bold">' + json[i].dynamics + '</font>'
                        json[i].status = '<font style="color:#00acd6;font-weight:bold"><i class="fas fa-hourglass"></i>  Pending On Dynamics</font>';
                    }
                    else
                    {
                        if(json[i].curning == null)
                        {
                            json[i].dry = '<font style="color:#00acd6;font-weight:bold">' + json[i].dry + '</font>'
                            json[i].status = '<font style="color:#00acd6;font-weight:bold"><i class="fas fa-hourglass"></i>  Pending On Dry</font>';
                        }
                        else
                        {
                            if(json[i].holding == null)
                            {
                                json[i].curning = '<font style="color:#00acd6;font-weight:bold">' + json[i].curning + '</font>'
                                json[i].status = '<font style="color:#00acd6;font-weight:bold"><i class="fas fa-hourglass"></i>  Pending On Curning</font>';
                            }
                            else
                            {
                                if(json[i].fg == null)
                                {
                                    json[i].holding = '<font style="color:#00acd6;font-weight:bold">' + json[i].holding + '</font>'
                                    json[i].status = '<font style="color:#00acd6;font-weight:bold"><i class="fas fa-hourglass"></i>  Pending On Holding</font>';
                                }
                                else
                                {
                                    json[i].fg = '<font style="color:#00a65a;font-weight:bold">' + json[i].fg + '</font>'
                                    json[i].status = '<font style="color:#00a65a;font-weight:bold"><i class="fas fa-check"></i>  Finished Good</font>';
                                }
                            }
                        }
                    }
                }
                return json;
                }
        },
        "columns": 
        [
            { "data": "no" }, 
            { "data": "harvest_batch_id" }, 
            { "data": "dynamics" }, 
            { "data": "dry" }, 
            { "data": "curning" }, 
            { "data": "holding" }, 
            { "data": "fg" },
            { "data": "status" },
        ],
        "scrollX": true,
        "order": [[0, 'asc']]
    });   
}
$("#mode").change(() => {
    $('#history_table').dataTable().fnDestroy()
    createTable()
})
$(function(){
    
    createTable();

    $("body").addClass('fixed')

    $('.select2').select2()

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
        $('#history_table').dataTable().fnDestroy()
        createTable()
    })
    
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});