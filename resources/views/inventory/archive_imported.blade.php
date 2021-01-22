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
        <h1>Bulk Awaiting Approval</h1>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
        </div>
        <!-- /.box-header -->
        
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-bordered nowrap" id="tbl_inventory">
                        <thead>
                            <th></th>
                            <th>Harvest Batch ID</th>
                            <th>Strain</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Imported Date</th>
                            <th>Action</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
            <h1>Bulk Upload History</h1>

            <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
            </div>
        </div>
        <!--start edit form-->
        <div class="box-body">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Invoice Period:</label>

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
                        <table class="table table-bordered" id="tbl_history" style="width:100%">
                            <thead>
                                <th></th>
                                <th>Harvest Batch ID</th>
                                <th>Batch Upload ID</th>
                                <th>Strain</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Imported Date</th>
                                <th>Approved Date</th>
                                <th>Imported By</th>
                                <th>Approved By</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
    @include('layouts.modal_alert')
@stop
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/inventory/archive_imported.js') }}"></script>
@stop   

<script>
    let harvests_inventory = JSON.parse('{!! json_encode($data) !!}');
    harvests_inventory.forEach(element => {
            element.btn_approve = '<button class="btn btn-info btn-xs btn_approve"><i class="fas fa-envelope-square">&nbsp;</i>Approve</button>';
            element.qty = element.inventory.length;
    });
    let s_date = '{{ $start_date }}'
    let e_date = '{{ $end_date }}'
</script>
