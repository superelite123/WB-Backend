@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Walnut FulFillment')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
        <h1>FulFillment</h1>

        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->

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
                    <table class="table table-bordered" id="invoice_table" style="width:100%">
                        <thead>
                            <th></th>
                            <th>No</th>
                            <th>Sales Order</th>
                            <th>Customer</th>
                            <th>Distributor</th>
                            <th>Total Cost</th>
                            <th>Date</th>
                            <th>ETC</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="2">
                                <h4>Total:&nbsp;&nbsp;<span style="color:green" class="footer-total">$0</span></h4>
                            </th>
                            <th></th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</div>
<div class="box box-info">
    <div class="box-header with-border">
        <h1>Problematic Orders</h1>

        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->

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
                            <input type="text" class="form-control pull-right" id="reservation_problematic">
                        </div>
                        <!-- /.input group -->
                    </div>
                </div>
                <div class="col-xs-3"></div>
                <div class="col-xs-3">
                    <button class="btn btn-info pull-right"  style="margin-top:1.5em" id="export_problematic_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-bordered" id="problematic_table" style="width:100%">
                        <thead>
                            <th></th>
                            <th>No</th>
                            <th>Sales Order</th>
                            <th>Customer</th>
                            <th>Distributor</th>
                            <th>Total Cost</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</div>
<div class="box box-info">
    <div class="box-header with-border">
        <h1>Inventory for Restock Verification</h1>

        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12" style="margin-bottom: 30px;display:flex;justify-content: flex-end;">
                    <button class="btn btn-info btn-verified" style="margin-right: 30px">Distro Verified(0)</button>
                    <button class="btn btn-info btn-approved">Fullfilnent Approved(0)</button>
                </div>
                <div class="col-xs-12">
                    <table class="table table-bordered" id="inv_restock_table" style="width:100%">
                        <thead>
                            <th>No</th>
                            <th>Metrc Tag</th>
                            <th>SO Number</th>
                            <th>Retailer</th>
                            <th>Strain</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Restock Reason</th>
                            <th>Distro Verified</th>
                            <th>Fullfilment Approved</th>
                        </thead>
                        <tbody>
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
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/ajax_loader.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/order/fulfillment_list.js?v=202101160554') }}"></script>
@stop
