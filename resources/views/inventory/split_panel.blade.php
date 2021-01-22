@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Split-Inventory Manage')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/inventory/split.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
    <div class="box box-info">
        <div class="box-header with-border">
        <h1>Split Page</h1>

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
                    <table class="table table-bordered nowrap" id="inventoryTable" style='width:100%'>
                        <thead>
                            <th></th>
                            <th>Parent Harvest Batch ID</th>
                            <th>Metrc Tag</th>
                            <th>Strain</th>
                            <th>Type</th>
                            <th>Pre Built UPC</th>
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
            <form action="_split" method="POST" id="form" onsubmit="return checkForm()">
                @csrf
            <div class="row">
                <div class="col-md-12 split-panel-title">
                    <h3>Split</h3>
                </div>
                <div class="col-md-12 split-item">
                    <div class="col-md-2">
                        <label for="">Batch ID:</label>
                        <span id='splitBatchId'></span>
                    </div>
                    <div class="col-md-2">
                        <label for="">Metrc Tag:</label>
                        <span id='splitMetrc'></span>
                    </div>
                    <div class="col-md-2">
                        <label for="">Strain:</label>
                        <span id='splitStrain'></span>
                    </div>
                    <div class="col-md-2">
                        <label for="">Type:</label>
                        <span id='splitType'></span>
                    </div>
                    <div class="col-md-1">
                        <label for="">Quantity:</label>
                        <span id='splitQty'></span>
                    </div>
                    <div class="col-md-2">
                        <label for="">Weight:</label>
                        <span id='splitWeight'></span>
                    </div>
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
                            <input type="text" class="form-control" name="metrc" id="inputMetrc" placeholder='Metrc Tag' maxlength="24">
                            </div>
                            <span class="error"><p style='color:red'><?php if ($errors->has('metrc')) echo $errors->first('metrc');?></p></span>
                            <!-- /.input group -->
                        </div>
                    </div>
                    <!--./Metrc Input-->
                    <!--Qty Input-->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Quantity:</label>
                            <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <input type="text" class="form-control" name="count" id="inputQty" placeholder='Quantity'>
                            </div>
                            <span class="error">
                                <?php 
                                  if ($errors->has('count')) 
                                    foreach ($errors->get('count') as $message) 
                                    {
                                ?>
                                <p style='color:red'>
                                    {{ $message }}
                                </p>
                                <?php
                                    }
                                ?>
                            </span>
                            <!-- /.input group -->
                        </div>
                    </div>
                    <!--./Qty Input-->
                    <!--Weight Input-->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Weight:</label>
                            <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <input type="text" class="form-control" name="weight" id="inputWeight" placeholder='Weight'>
                            </div>
                            <span class="error">
                            <?php 
                                if ($errors->has('weight')) 
                                foreach ($errors->get('weight') as $message) 
                                {
                            ?>
                                <p style='color:red'>
                                {{ $message }}
                                </p>
                            <?php
                                }
                            ?>
                        </span>
                            <!-- /.input group -->
                        </div>
                    </div>
                    <!--./Weight Input-->
                    <!--P_type-->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Product Type</label>
                            <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <select class="form-control select2" style="width: 100%;" name="p_type" name="p_type" id="inputPType">
                                @foreach($p_types as $p_type)
                                <option value="{{ $p_type->producttype_id }}">{{ $p_type->producttype }}</option>
                                @endforeach
                            </select>
                            </div>
                            <!-- /.input group -->
                            <span class="error"><p id="strain_error" style='color:red'></p></span>
                        </div>
                    </div>
                    <!--<div class="col-md-1">
                        <button class='btn btn-info' style='margin-top:25px' id='btn_add_row'><i class="fas fa-plus"></i>&nbsp;Add Row</button>
                    </div>-->
                    <input type="hidden" name="fgasset_id" id="fgasset_id" value="0"> 
                    <input type="hidden" name="i_type" id="i_type" value="0"> 
                    <div class="col-md-1">
                        <button class='btn btn-info btn-lg' style='margin-top:15px' id='btnSplit'><i class="fas fa-cut"></i>&nbsp;Split</button>
                    </div>
                    <!--./P_type-->
                </div>
                <!--
                <div class="col-md-12 combine_table_panel">
                    <table class="table table-bordered table-striped" id="splitTable" style="display: none">
                        <thead>
                            <th></th>
                            <th>Parent Harvest Batch ID</th>
                            <th>Metrc Tag</th>
                            <th>Strain</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Weight</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>-->
            </div>
            </form>
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
  <script type="text/javascript" src="{{ asset('assets/js/inventory/split_panel.js?v=20201231') }}"></script>
@stop   