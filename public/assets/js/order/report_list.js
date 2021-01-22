let s_date = windowvar.start_date;
let e_date = windowvar.end_date;
let chks = $(":checkbox")
let table_json_data =[]
let base_chart_param
let weight_chart_param
let discount_chart_param
let tax_chart_param
let chart_param = {
    type: 'stackedcolumn3d',
    dataFormat: 'json',
    renderAt: 'chart_container_strain',
    width: '100%',
    height: '400',
    dataSource: {
      "chart": {
        "theme": "fusion",
        "caption": "Strain",
        "subCaption": "Strain Yields in Date Range",
        "xAxisName": "Strain",
        "yAxisName": "Total Plants Harvested",
        "numberPrefix": "",
        "lineThickness": "3",
        "flatScrollBars": "1",
        "scrollheight": "10",
        "numVisiblePlot": "12",
        "showHoverEffect": "1",
        "exportEnabled": "1"
      },
      "categories": [{
        "category": [],
      }],
      "dataset": [{
        "data": []
      }]
    }
}

$("#export_btn").on('click', function(event) {
    var res = invoice_table.rows().data()
    console.log(res)
    convertToCSV(res).then(function(result){
        let filename = 'Invoice Data ' + $("#reservation").val();
        exportCSVfile(filename,result);
    })
});
$("#export_snap_btn").on('click', function(event) {
    var res = invoice_table.rows().data()
    console.log(res)
    convertToSnapCSV(res).then(function(result){
        let filename = 'Invoice Data ' + $("#reservation").val();
        exportCSVfile(filename,result);
    })
});
var convertToCSV = (objArray) => {

    return new Promise(function(next_operation){
        
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = ''
        var str1  = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,"
            str1 += "Customer Terms,Paid,Delivered,Minus Date,Payment Due Date\r\n"
        let str2  = "Qty,Base Price,Discount,Promotion Value,Sub Total,Sub Total Collected,Rem Sub Total,"
            str2 += "CA Tax,CA Tax Collected,Rem Tax,Total Due\r\n";
        
        for (var i = 0; i < array.length; i++) {

            var line1 = str1;
            var line2 = str2;
            let paid      = array[i].paid != null?'Paid':'No Paid'
            let delivered = array[i].delivered != null?'Delivered':'No Delivered'
            line1 += array[i].date + ',';
            line1 += '\"' +  array[i].customername + '\",';
            line1 += '\"' + array[i].customer.address1 + '\",';
            line1 += array[i].number + ',';
            line1 += array[i].customer.licensenumber + ','
            line1 += array[i].termLabel + ','
            line1 += paid + ','
            line1 += delivered  + ','
            line1 += array[i].diff_date  + ','
            line1 += array[i].pay_date  + ','
            line1 += '\r\n';
            line2 += array[i].total_info.qty + ',';
            line2 += array[i].base_price + ',';
            line2 += array[i].discount + ',';
            line2 += array[i].promotion + ',';
            line2 += array[i].extended + ',';
            line2 += array[i].p_extended + ',';
            line2 += array[i].r_extended + ',';
            line2 += array[i].tax + ',';
            line2 += array[i].p_tax + ',';
            line2 += array[i].r_tax + ',';
            line2 += array[i].adjust_price + ',';
            line2 += '\r\n';
            var sub_array = array[i].items;
            var sub_result = ' ,Description,Qty,Units,Unit Price,Discount,Discount Type,Sub Total,'
            sub_result += 'Less Disocunt,Excise TAX,Line Note\r\n';

            if(sub_array != null)
            {
                for (var j = 0; j < sub_array.length; j++) {
                    var newline = '  ';
                    
                    newline += ' ,' + sub_array[j].description;
                    newline += ' ,' + sub_array[j].qty;
                    newline += ' ,' + sub_array[j].units;
                    newline += ' ,' + sub_array[j].unit_price;
                    newline += ' ,' + sub_array[j].discount;
                    newline += ' ,' + sub_array[j].discount_label;
                    newline += ' ,' + sub_array[j].base_price;
                    newline += ' ,' + sub_array[j].extended;
                    newline += ' ,' + sub_array[j].tax;
                    newline += ' ,' + sub_array[j].tax_note;

                    sub_result += newline + '\r\n';
                }
            }   

            line1 += line2
            line1 += sub_result+ '\r\n';
            str += line1
        }
        next_operation(str);
    });
}
var convertToSnapCSV = (objArray,mode) => {

    return new Promise(function(next_operation){
        
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = 
            str  = "Creating Date,Customer Name,Customer Address,INV,Customer LIC,"
            str += "Customer Terms,Paid,Delivered,Minus Date,Payment Due Date,"
            str += "Qty,Base Price,Discount,Promotion Value,Sub Total,Sub Total Collected,Rem Sub Total,"
            str += "CA Tax,CA Tax Collected,Rem Tax,Total Due\r\n";
        
        for (var i = 0; i < array.length; i++) {

            var line1 = '';
            let paid      = array[i].paid != null?'Paid':'No Paid'
            let delivered = array[i].delivered != null?'Delivered':'No Delivered'
            line1 += array[i].date + ',';
            line1 += '\"' +  array[i].customername + '\",';
            line1 += '\"' +  array[i].customer.address1 + '\",';
            line1 += array[i].number + ',';
            line1 += array[i].customer.licensenumber + ','
            line1 += array[i].termLabel + ','
            line1 += paid + ','
            line1 += delivered  + ','
            line1 += array[i].diff_date  + ','
            line1 += array[i].pay_date  + ','
            line1 += array[i].total_info.qty + ',';
            line1 += array[i].base_price + ',';
            line1 += array[i].discount + ',';
            line1 += array[i].promotion + ',';
            line1 += array[i].extended + ',';
            line1 += array[i].p_extended + ',';
            line1 += array[i].r_extended + ',';
            line1 += array[i].tax + ',';
            line1 += array[i].p_tax + ',';
            line1 += array[i].r_tax + ',';
            line1 += array[i].adjust_price + ',';
            line1 += '\r\n';
            str += line1
        }
        next_operation(str);
    });
}
var exportCSVfile = (filename,csv) =>{
    var exportedFilenmae = filename + '.csv' || 'export.csv';

    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, exportedFilenmae);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", exportedFilenmae);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}

chks.click(function(){
    $('input:checkbox').not(this).prop('checked',false)
    get_list()
})

let get_list = () => {
    let op_type = -1
    chks.each(function() {
        if($(this).prop('checked')) op_type = $(this).attr('op_type')
    })
    $.ajax({
        url: "_get_report_list",
        type: 'POST',
        data: "date_range=" + $("#reservation").val() + '&flag=' + op_type,
        async:false,
        success:(res) => {
            convert_ajax_table_data(res.orders)
            createChart(res.chart_data)
            console.log(res.chart_data)
        }
    })
}
let convert_ajax_table_data = (res) => {
    let json = res
    for ( var i=0, ien=json.length ; i<ien ; i++ ) {
        json[i].no = i + 1
        json[i].customername  = json[i].total_info.customername
        json[i].base_price    = json[i].total_info.base_price
        json[i].discount      = json[i].total_info.discount
        json[i].extended      = json[i].total_info.extended
        json[i].p_extended    = json[i].total_financial.pSubTotal
        json[i].r_extended    = json[i].total_financial.rSubTotal
        json[i].promotion     = json[i].total_info.promotion
        json[i].tax           = json[i].total_info.tax
        json[i].p_tax         = json[i].total_financial.pTax
        json[i].r_tax         = json[i].total_financial.rTax
        json[i].adjust_price  = json[i].total_info.adjust_price
        json[i].payment       = json[i].total_info.payment
        json[i].pay_date      = json[i].total_info.pay_date
        json[i].term          = json[i].total_info.term
        json[i].termLabel          = json[i].total_info.termLabel
        if(json[i].diff_date == null) json[i].diff_date =0
    }
    table_json_data = json
    createTable()
}
let createTable = () => {
    $('#invoice_table').dataTable().fnDestroy()
    invoice_table = $('#invoice_table').DataTable({
        "data": table_json_data,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            let sub_total = 0;
            let discount_total = 0;
            let tax_total = 0;
            for(let i = start; i < end; i ++)
            {
                let child = data[i].items;
                sub_total += parseFloat(data[i].base_price)
                discount_total += parseFloat(data[i].discount)
                tax_total += parseFloat(data[i].tax)
            }
            
            $( api.column( 2 ).footer() ).html(
                'Sub Total:' + sub_total.toFixed(2)
            );
            // Update footer
            $( api.column( 3 ).footer() ).html(
                'Discount Total:' + discount_total.toFixed(2)
            );
            $( api.column( 4 ).footer() ).html(
                'Tax Total:' + tax_total.toFixed(2)
            );
        },
        "columns": 
        [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>'
            },
            { "data": "no" }, 
            { "data": "customername" }, 
            { "data": "number" }, 
            { "data": "date" }, 
            { "data": "base_price" }, 
            { "data": "discount" }, 
            { "data": "promotion" },
            { "data": "extended" },
            { "data": "p_extended" },
            { "data": "r_extended" },
            { "data": "tax" },
            { "data": "p_tax" },
            { "data": "r_tax" },
            { "data": "adjust_price" },
            { "data": "paid" },
            { "data": "payment" },
            { "data": "term" },
            { "data": "diff_date" },
            { "data": "pay_date" },
        ],
        "order": [[1, 'asc']],
        'responsive': true,
        'scrollX': true
    });
}
let createChart = (chart_data) => {
    base_chart_param.dataSource.chart.caption     = 'Base Price Yields in Date Range'
    base_chart_param.dataSource.chart.subCaption  = 'Base Price Yields in Date Range'
    base_chart_param.dataSource.chart.xAxisName   = 'Total Order Base Price'
    base_chart_param.dataSource.chart.yAxisName   = 'Order'
    base_chart_param.dataSource.categories        = [{'category':chart_data.d_labels}]
    base_chart_param.dataSource.dataset           = [{'data':chart_data.bases}]
    new FusionCharts(base_chart_param).render()
    
    weight_chart_param.dataSource.chart.caption     = 'Weight Yields in Date Range'
    weight_chart_param.dataSource.chart.subCaption  = 'Weight Yields in Date Range'
    weight_chart_param.dataSource.chart.xAxisName   = 'Total Order Weight'
    weight_chart_param.dataSource.chart.yAxisName   = 'Order'
    weight_chart_param.dataSource.categories        = [{'category':chart_data.labels}]
    weight_chart_param.dataSource.dataset           = [{'data':chart_data.weights}]
    new FusionCharts(weight_chart_param).render()
    
    discount_chart_param.dataSource.chart.caption     = 'Discount Yields in Date Range'
    discount_chart_param.dataSource.chart.subCaption  = 'Discount Yields in Date Range'
    discount_chart_param.dataSource.chart.xAxisName   = 'Total Order Discount'
    discount_chart_param.dataSource.chart.yAxisName   = 'Order'
    discount_chart_param.dataSource.categories        = [{'category':chart_data.d_labels}]
    discount_chart_param.dataSource.dataset           = [{'data':chart_data.discounts}]
    new FusionCharts(discount_chart_param).render()
    
    tax_chart_param.dataSource.chart.caption     = 'TAX Yields in Date Range'
    tax_chart_param.dataSource.chart.subCaption  = 'TAX Yields in Date Range'
    tax_chart_param.dataSource.chart.xAxisName   = 'Total Order TAX'
    tax_chart_param.dataSource.chart.yAxisName   = 'Order'
    tax_chart_param.dataSource.categories        = [{'category':chart_data.d_labels}]
    tax_chart_param.dataSource.dataset           = [{'data':chart_data.taxs}]
    new FusionCharts(tax_chart_param).render()
}
let row_details_format = (d) => {
    // `d` is the original data object for the row
    var data = d.items
    var html = '<table class="table table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    html += '<thead>';
    html += '<th>No</th>'
    html += '<th>Description</th>';
    html += '<th>Qty</th>';
    html += '<th>Units</th>';
    html += '<th>Weight</th>';
    html += '<th>Unit_price</th>';
    html += '<th>CPU</th>';
    html += '<th>Discount</th>';
    html += '<th>Sub Total</th>';
    html += '<th>Extended</th>';
    html += '<th>Line Note</th>';
    html += '<th>Adjust Price</th>';
    html += '</thead>';
    
    html += "<tbody>";
    for(var i = 0; i < data.length; i ++)
    {
        html += '<tr>';
        html += '<td>' + (i + 1) + '</td>';
        html += '<td>' + data[i].description + '</td>';
        html += '<td>' + data[i].qty + '</td>';
        html += '<td>' + data[i].units + '</td>';
        html += '<td>' + data[i].weight + '</td>';
        html += '<td>' + data[i].unit_price + '</td>';
        html += '<td>' + data[i].cpu + '</td>';
        html += '<td>' + data[i].discount + '</td>';
        html += '<td>' + data[i].base_price + '</td>';
        html += '<td>' + data[i].extended + '</td>';
        html += '<td>' + data[i].tax_note + '</td>';
        html += '<td>' + data[i].adjust_price + '</td>';
        html += '</tr>';
    }

    html += "</tbody></table>";
    return html;
}
$('#invoice_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = invoice_table.row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-plus"></i></button>')
    }
    else {
        // Open this row
        row.child( row_details_format(row.data()) ).show();
        tr.addClass('shown');
        $(this).html('<button class="btn btn-info btn-xs btn-edit"><i class="glyphicon glyphicon-minus"></i></button>')
    }
})
function clone(obj) {
    if (null == obj || "object" != typeof obj) return obj;
    var copy = obj.constructor();
    for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
    }
    return copy;
}

$(function(){
    
    //init chart parameter
    base_chart_param = Object.assign({}, chart_param)
    base_chart_param.renderAt = 'chart_container_base'
    
    //weight chart param
    weight_chart_param = Object.assign({}, chart_param)
    weight_chart_param.type = 'msbar3d'
    weight_chart_param.renderAt = 'chart_container_weight'
    //discount chart param
    discount_chart_param = Object.assign({}, chart_param)
    discount_chart_param.renderAt = 'chart_container_discount'
    
    //tax chart param
    tax_chart_param = Object.assign({}, chart_param)
    tax_chart_param.renderAt = 'chart_container_tax'
    get_list()

    $("#reservation").daterangepicker({
        format: 'dd.mm.yyyy',
        startDate: s_date,
        endDate: e_date
      }).on("change", function() {
          get_list()
    })
})
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});