@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Plant Room Builder')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/harvest/statistic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/icheck/all.css') }}">
@stop
@section('content')
<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>
<div class="box box-success">
    <!--Box Header-->
    <div class="box-header with-border">
        <h1>Plant Room Statistics</h1>
        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <!--Box Body-->
    <div class="box-body">
        <div class="row col-md-12 top_bar">
            <div class="col-md-3">
                <label>BatchID:&nbsp;</label>
                <span class="sp_batchId">{{ $harvest->harvest_batch_id }}</span>
            </div>
            <div class="col-md-2">
                <label>Strain:&nbsp;</label>
                <span class="sp_strain">{{ $harvest->Strain->strain }}</span>
            </div>
            <div class="col-md-2">
                <label>Flower Room Location:&nbsp;</label>
                <span class="sp_strain">{{ $harvest->Room->name }}</span>
            </div>
            <div class="col-md-2">
                <label>Matrix Type:&nbsp;</label>
                <span class="sp_strain">4*{{ $room->matrix_col }}</span>
            </div>
            <div class="col-md-1">
                <div class='circle circle_red'></div>
                <span for="">0-400</span>
            </div>
            <div class="col-md-1">
                    <div class='circle' style='background:yellow'></div>
                    <span for="">401-800</span>
                </div>
            <div class="col-md-1">
                <div class='circle circle_green'></div>
                <span for="">801+</span>
            </div>
        </div>
        <div class="row col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Table View</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Data Matrix</a></li>
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                                <div class="col-md-4 scrollDiv" >
                                    <canvas id="table1" style="border:1px solid #c3c3c3;">
                                        Your browser does not support the canvas element.
                                    </canvas>
                                </div>
                                <div class="col-md-4 scrollDiv">
                                    <canvas id="table2" style="border:1px solid #c3c3c3;">
                                        Your browser does not support the canvas element.
                                    </canvas>
                                </div>
                                <div class="col-md-4 scrollDiv">
                                    <canvas id="table3" style="border:1px solid #c3c3c3;">
                                        Your browser does not support the canvas element.
                                    </canvas>
                                </div>
                        </div>
                        
                    </div>
                    <div class="tab-pane" id="tab_2">
                        <table class="table table-bordered" id="satistic_table">
                            <thead>
                                <th>Harvest Batch ID</th>
                                <th>Total Weight</th>
                                <th>Unit Of Weight</th>
                                <th>Flower Room Location</th>
                                <th>Strain</th>
                                <th>License</th>
                                <th>Creation Date</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $harvest->harvest_batch_id }}</td>
                                    <td>{{ $harvest->total_weight }}</td>
                                    <td>{{ $harvest->UnitOfWeight->name.'-'.$harvest->UnitOfWeight->abbriviation }}</td>
                                    <td>{{ $harvest->Room->name }}</td>
                                    <td>{{ $harvest->Strain->strain }}</td>
                                    <td>{{ $harvest->License->license }}</td>
                                    <td>{{ $harvest->created_at }}</td>
                                </tr>
                                <tr class='th_tr'>
                                    <td>Qty in Range</td>
                                    <td>Weight Range In Gram</td>
                                    <td>% of Total</td>
                                    <td>Weight Range In Grams</td>
                                    <td>Qty in Range</td>
                                    <td>% Of Total</td>
                                </tr>
                                <!--0-400-->
                                <tr>
                                    <td>{{ $items[0] }}</td>
                                    <td>0-100</td>
                                    <td>{{  $items[0] / $total_count * 100 }}%</td>
                                    <td rowspan="4">0-400</td>
                                    <td rowspan="4">{{ $items[0] + $items[1] + $items[2] + $items[3] }}</td>
                                    <td rowspan="4">{{ ($items[0] + $items[1] + $items[2] + $items[3]) / $total_count * 100 }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[1] }}</td>
                                    <td>100-200</td>
                                    <td>{{  $items[1] / $total_count * 100 }}%</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[2] }}</td>
                                    <td>200-300</td>
                                    <td>{{  $items[2] / $total_count * 100 }}%</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[3] }}</td>
                                    <td>300-400</td>
                                    <td>{{  $items[3] / $total_count * 100 }}%</td>
                                </tr>
                                <!--/.0-400-->
                                <!--400-800-->
                                <tr>
                                    <td>{{ $items[4] }}</td>
                                    <td>400-500</td>
                                    <td>{{  $items[4] / $total_count * 100 }}%</td>
                                    <td rowspan="4">401-800</td>
                                    <td rowspan="4">{{ $items[4] + $items[5] + $items[6] + $items[7] }}</td>
                                    <td rowspan="4">{{ ($items[4] + $items[5] + $items[6] + $items[7]) / $total_count * 100 }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[5] }}</td>
                                    <td>500-600</td>
                                    <td>{{  $items[5] / $total_count * 100 }}%</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[6] }}</td>
                                    <td>600-700</td>
                                    <td>{{  $items[6] / $total_count * 100 }}%</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[7] }}</td>
                                    <td>700-800</td>
                                    <td>{{  $items[7] / $total_count * 100 }}%</td>
                                </tr>
                                <!--800-1000-->
                                <tr>
                                    <td>{{ $items[8] }}</td>
                                    <td>800-900</td>
                                    <td>{{  $items[8] / $total_count * 100 }}%</td>
                                    <td rowspan="3">800-1000+</td>
                                    <td rowspan="3">{{ $items[8] + $items[9] + $items[10] }}</td>
                                    <td rowspan="3">{{ ($items[8] + $items[9] + $items[10]) / $total_count * 100 }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[9] }}</td>
                                    <td>900-1000</td>
                                    <td>{{  $items[9] / $total_count * 100 }}%</td>
                                </tr>
                                <tr>
                                    <td>{{ $items[10] }}</td>
                                    <td>1000+</td>
                                    <td>{{  $items[10] / $total_count * 100 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <div id=tip style='display:none'>Tooltip</div>
</div>
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/statistic.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop