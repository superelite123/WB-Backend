@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Walnut to Deliver')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/datatable-fixedWidthColumn.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
    <div class="box box-info">
        <div class="box-header with-border">
        <h1>Combining Page</h1>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
        </div>
        <!-- /.box-header -->
        
        <div class="box-body">
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label>Harvest Period:</label>
    
                        <div class="input-group">
                            <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="reservation">
                        </div>
                        <!-- /.input group -->
                    </div>
                </div>
                <div class="col-xs-3"></div>
                <div class="col-xs-3">
                    <button class="btn btn-info pull-right"  style="margin-top:1.5em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-bordered nowrap" id="invoice_table">
                        <thead>
                            <th></th>
                            <th>Parent Harvest Batch ID</th>
                            <th>Metrc Tag</th>
                            <th>Strain</th>
                            <th>Type</th>
                            <th>Pre Buil UPC</th>
                            <th>COA</th>
                            <th>Qty</th>
                            <th>Weight</th>
                            <th>Um</th>
                            <th>Harvested Date</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <h3>Combine</h3>
                </div>
                <div class='col-md-12'>
                    <!--Metrc Input-->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Metrc Tag:</label>
                            <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <input type="text" class="form-control" id="metrc" placeholder='metrc' maxlength="24">
                            </div>
                            <span class="error"><p id="metrc_error" style='color:red'></p></span>
                            <!-- /.input group -->
                        </div>
                    </div>
                    <!--./Metrc Input-->
                    <!--P_type-->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Product Type</label>
                            <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <select class="form-control select2" style="width: 100%;" name="p_type" id="p_type">
                                @foreach($p_types as $p_type)
                                <option value="{{ $p_type->producttype_id }}">{{ $p_type->producttype }}</option>
                                @endforeach
                            </select>
                            </div>
                            <!-- /.input group -->
                            <span class="error"><p id="strain_error" style='color:red'></p></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class='btn btn-info' style='margin-top:25px' id='btn_combine'>Combine</button>
                    </div>
                    <!--./Promo-->
                </div>
                <div class="col-md-12 combine_table_panel">
                    <table class="table table-bordered table-striped nowrap" id="combine_table">
                        <thead>
                            <th></th>
                            <th>Parent Harvest Batch ID</th>
                            <th>Metrc Tag</th>
                            <th>Strain</th>
                            <th>Type</th>
                            <th>Pre Buil UPC</th>
                            <th>COA</th>
                            <th>Qty</th>
                            <th>Weight</th>
                            <th>Um</th>
                            <th>Harvested Date</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>

    @include('layouts.modal_alert')
@stop
<script>
    let SD = {!! json_encode($data) !!};
</script>
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/inventory/combine_panel.js') }}"></script>
@stop   