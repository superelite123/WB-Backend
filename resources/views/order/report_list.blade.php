@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'FulFillment')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/report_list.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/checkbox.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
    <div class="box box-info">
        <div class="box-header with-border">
            <h1>Report Page</h1>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row chk_row">
                <!--Paid-->
                <div class="col-md-1">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" id='chk_paid' value="" op_type='0'>
                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                        PAID
                        </label>
                    </div>
                </div>
                <!--/.Paid-->
                <!--overdue-->
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" id='chk_overdue' value="" op_type='1'>
                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                        Overdue
                        </label>
                    </div>
                </div>
                <!--/.overdue-->
                <!--15day-->
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" id='chk_15day' value="" op_type='2'>
                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                        15 Day
                        </label>
                    </div>
                </div>
                <!--/.15day-->
                <!--30day-->
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" id='chk_30day' value="" op_type='3'>
                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                        30 Day
                        </label>
                    </div>
                </div>
                <!--/.30day-->
                <!--30day-->
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" id='chk_60day' value="" op_type='4'>
                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                        60+ Day
                        </label>
                    </div>
                </div>
                <!--/.30day-->
                <!--30day-->
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" id='chk_90day' value="" op_type='5'>
                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                        90+ Day
                        </label>
                    </div>
                </div>
                <!--/.30day-->
            </div>
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
                <div class="col-xs-2"></div>
                <div class="col-xs-2">
                    <button class="btn btn-info"  style="margin-top:1.5em" id="export_snap_btn" class="export"><i class="fa fa-download"></i>&nbsp;Snapshot CSV</button>
                </div>
                <div class="col-xs-2">
                    <button class="btn btn-info"  style="margin-top:1.5em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-bordered" id="invoice_table">
                        <thead>
                            <th></th>
                            <th>No</th>
                            <th>Customer Name</th>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Base Price</th>
                            <th>Discount</th>
                            <th>Promotional Value</th>
                            <th>Sub Total</th>
                            <th>Subtotal Collected</th>
                            <th>Rem Sub Total</th>
                            <th>CA Tax</th>
                            <th>CA Tax Collected</th>
                            <th>Rem Tax</th>
                            <th>Total Due</th>
                            <th>Paid Date</th>
                            <th>Payments</th>
                            <th>Term</th>
                            <th>Minus Date</th>
                            <th>Payment Due Date</th>
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
                            <th></th>
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
    
    <!-- LINE CHART -->
    <div class="box box-info">
        <div class="box-header with-border">
        <h3 class="box-title">Total Base Chart</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
        </div>
        <div class="box-body">
            <div id="chart_container_base">Total Base Chart will render here</div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <!-- LINE CHART -->
    <div class="box box-info">
        <div class="box-header with-border">
        <h3 class="box-title">Total Weight Chart</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
        </div>
        <div class="box-body">
            <div id="chart_container_weight">Total Weight Chart will render here</div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <!-- LINE CHART -->
    <div class="box box-info">
        <div class="box-header with-border">
        <h3 class="box-title">Total Discounts Chart</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
        </div>
        <div class="box-body">
            <div id="chart_container_discount">Total Discount Chart will render here</div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <!-- LINE CHART -->
    <div class="box box-info">
        <div class="box-header with-border">
        <h3 class="box-title">Total Tax Chart</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
        </div>
        <div class="box-body">
            <div id="chart_container_tax">Total Tax Chart will render here</div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
  <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/Chart.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/order/report_list.js') }}"></script>
@stop   