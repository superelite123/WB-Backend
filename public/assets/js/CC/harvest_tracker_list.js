var harvest_table;
var createTable = () => {
    harvest_table = $('#tracker_table').DataTable({
        "ajax": {
            url: "get_tracker_list",
            type: 'POST',
            "data": function ( d ) {
            },
            dataSrc: function ( json ) {
                
                for ( var i=0, ien=json.length ; i<ien ; i++ ) {
                    json[i].no = i + 1;
                }
                return json;
                }
        },
        "columns": 
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "no" }, 
            { "data": "harvest_batch_id" }, 
            { "data": "strain" }, 
            { "data": "producttype" }, 
            { "data": "location" }, 
            { "data": "allocatedweight" }, 
            { "data": "datelastmodified" }, 
            ],
        "scrollX": true,
        "order": [[1, 'asc']]
    });   
}

$(() => {
    createTable()
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});