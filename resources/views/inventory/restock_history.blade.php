@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Inventory Restock History')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h1>Inventory Restock History</h1>

        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-info pull-right"  style="margin-bottom:0.5rem" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
                </div>
                <div class="col-xs-12">
                    <table class="table table-bordered" id="inv_restock_table" style="width:100%">
                        <thead>
                            <th>Metrc Tag</th>
                            <th>SO Number</th>
                            <th>Sales Rep</th>
                            <th>Strain</th>
                            <th>Type</th>
                            <th>Restocked At</th>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item['metrc_tag'] }}</td>
                                    <td>{{ $item['lOrder'] }}</td>
                                    <td>{{ $item['lSalesrep'] }}</td>
                                    <td>{{ $item['strain'] }}</td>
                                    <td>{{ $item['type'] }}</td>
                                    <td>{{ $item['restocked_at'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</div>
@stop
@include('footer')
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
    <script>
        const data = <?php echo json_encode($data)?>;
        $('#inv_restock_table').dataTable()
        $("#export_btn").on('click', function(event) 
        {
            convertToCSV(data).then(
                function(result)
                {
                    let filename = 'Restock History';
                    exportCSVfile(filename,result);
                }
            )
        });
        const convertToCSV = (objArray) => 
        {

            return new Promise(
                function(next_operation)
                {

                    let array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
                    let str = "Metrc Tag,Account Manager,SO/INV,Date,Company,Strain,Product Type\r\n"

                    for (let i = 0; i < array.length; i++)
                    {
                        str += array[i].metrc_tag + ',';
                        str += '\"' + array[i].lSalesrep + '\",';
                        str += array[i].lOrder + ',';
                        str += array[i].restocked_at + ',';
                        str += '\"' + array[i].company + '\",';
                        str += array[i].strain + ',';
                        str += array[i].type + '\r\n';
                    }
                    next_operation(str);
                }
            );
        }

        const exportCSVfile = (filename,csv) =>
        {
            let exportedFilenmae = filename + '.csv' || 'export.csv';

            let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            if (navigator.msSaveBlob) 
            { 
                navigator.msSaveBlob(blob, exportedFilenmae);
            }
            else
            {
                let link = document.createElement("a");
                if (link.download !== undefined)
                { 
                    // feature detection
                    // Browsers that support HTML5 download attribute
                    let url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", exportedFilenmae);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }
    </script>
@stop
