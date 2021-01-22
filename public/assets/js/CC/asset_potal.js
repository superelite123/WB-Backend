var get_asset_items_promise = function(group_id){

    return new  Promise(function(resolve){
        $.ajax({
            url:'_asset_potal_get_assets',
            data:'group_id='+group_id,
            type:'post',
            async:false,
            success:function(res)
            {
                resolve(res);
            }
        });
    })
}

var crete_view_table = (res) => {
    
    return new Promise(function(next_opertaion){
        var assets = JSON.parse(res);
        var html = "";
        var count = 1;
        
        assets.forEach(element => {
            
            html += "<tr>";
            html += "<td>" + count + "</td>";
            html += "<td>" + element.batch_id + "</td>";
            html += "<td>" + element.group_id_barcode + "<p style='text-align:center;padding-right:35px;'>" +element.group_id + "</p></td>";
            html += "<td>" + element.barcode_id + "<p style='text-align:center;padding-right:35px;'>" +element.asset_id + "</p></td>";
            html += "<td>" + element.weight + "</td>";
            html += "<td>" + element.type_label + "</td>";
            html += "<td>" + element.created_at.slice(0,10) + "</td>";
            html += "</tr>";

            count ++;
        })
        
        $("#view_assets_table > tbody").html(html)
        next_opertaion()
    })
}

var crete_print_table = (res) => {
    return new Promise(function(next_opertaion){
        var assets = JSON.parse(res);
        var html = "";
        var count = 1;
        var group_id_barcode;
        assets.forEach(element => {
            
            html += "<tr>";
            html += "<td>" + count + "</td>";
            html += "<td>" + element.batch_id + "</td>";
            html += "<td>" + element.barcode_id + "<p style='text-align:center;margin-top:0px'>" +element.asset_id + "</p></td>";
            html += "<td style='text-align:center'>" + element.weight + "</td>";
            html += "<td>" + element.type_label + "</td>";
            html += "<td>" + element.created_at.slice(0,10) + "</td>";
            html += "</tr>";

            count ++;

            group_id_barcode = "<p style='text-align:center;padding-right:35px;'>" + element.group_id_barcode + "<br>" + element.group_id + "</p>";
        })

        $("#print_group_id").html(group_id_barcode)
        console.log(group_id_barcode)
        $("#print_manifest_table > tbody").html(html)

        next_opertaion()
    })
}

var create_print_labels_panel = (res) => {
    return new Promise(function(next_opertaion){
        var assets = JSON.parse(res);
        var html = "";

        assets.forEach(element => {
            html += "<div class='print_cell'>";
            html += element.barcode_id;
            html += "<p>"; 
            html += element.asset_id + "</p>";
            html += "</div>";
            html += "<br clear='all' style='page-break-before:always' />";
        })

        $("#print_labels_panel").html(html);
        next_opertaion()
    })
}

var view = (group_id) => {
    get_asset_items_promise(group_id).then(crete_view_table).then($("#modal-view").modal());    
};

var print_manifest = (group_id) => {
    get_asset_items_promise(group_id).then(crete_print_table).then(function(){
        printData(document.getElementById('print_manifest_panel'))
    })
};

var print_labels = (group_id) => {
    get_asset_items_promise(group_id).then(create_print_labels_panel).then(function(){
        printData(document.getElementById('print_labels_panel'))
    });
}

var printData = (printElement) => {
    var htmlToPrint = '' +
        '<style type="text/css">' +
        'table th, table td {' +
        'border:1px solid #e2e2e2;' +
        '}' +
        '</style>';
    htmlToPrint += printElement.outerHTML;
   newWin= window.open("");
   newWin.document.write(htmlToPrint);
   newWin.print();
   newWin.close();
}

//------------------------------inital operation---------------------------------------
$('#asset_group_list').DataTable();

$("body").addClass('fixed');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});