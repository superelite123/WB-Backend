let s_date = windowvar.s_date
let e_date = windowvar.e_date
let harvest_table
let picker_modal = $("#modal_picker")

$("#btn_new").click(() => {
    picker_modal.modal('show')
})
//
$(".saveBtn").click(() => {
    let matrix_type = null
    //check matrix type
    if($('input[name=matrix_type]:checked').val() != undefined)
        matrix_type = $('input[name=matrix_type]:checked').val()
    //check the room id
    let room_id = $("#rooms").val()

    if(room_id == 0)
    {
        
        show_input_validation('select the Room')
        return
    }
    if(matrix_type == null)
    {
        show_input_validation('select the matrix type')
        return
    }

    location.href="form_room_builder?id=-1&r_id=" + room_id + "&matrix_type=" + matrix_type
})

let show_input_validation = (msg) => {
    console.log(console.log('room'))
    $("#modal_error_message").html(msg)
    $("#modal_alert_panel").show()
}
$("#modal_close_alert").click(function(){
    $("#modal_alert_panel").hide()
})

let createTable = (date_range) => {
    $('#harvest_table').dataTable().fnDestroy()
    harvest_table = $('#harvest_table').DataTable({
        "ajax": {
            url: "_get_room_builder_table_data",
            type: 'POST',
            "data": function ( d ) {
                d.date_range=date_range;
            },
            dataSrc: function ( json ) {
                
                for ( let i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1
                    json[i].name = json[i].room_name.name
                    json[i].user_name = json[i].user.name
                    json[i].matrix_type = "4 * " + json[i].matrix_col
                    json[i].edit_btn = '<button class="btn btn-info btn-xs btn-edit edit_btn">Edit</button>'
                }
                return json;
            }
        },
        "columns": 
        [
            { "data": "no" }, 
            { "data": "name" }, 
            { "data": "user_name" }, 
            { "data": "matrix_type" }, 
            { "data": "created_at" },
            { "data": "edit_btn" },
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
     
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                var weight_total = api
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                }, 0 );
                // Update footer
                $( api.column( 2 ).footer() ).html(
                    weight_total
                );
            },
        "scrollX": true,
        "order": [[0, 'asc']]
    });
}

$('#harvest_table tbody').on('click', '.edit_btn', function () {
    var tr = $(this).closest('tr');
    var row = harvest_table.row( tr );
    var id = row.data().id;
    
    location.href="form_room_builder?id=" + id;
})

$(() => {

    createTable($("#reservation").val());

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
    }).on("change", function() {
        
        createTable($("#reservation").val());
    })

    //radio 
      //Flat red color scheme for iCheck
      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      })
    //.radio
    $('.select2').select2();
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});