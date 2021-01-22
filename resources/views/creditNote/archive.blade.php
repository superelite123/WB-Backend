@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Credit Note')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/creditNote/form.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
      <h1>Credit Note</h1>

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
                    <label>Order Period:</label>

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
                <table class="table table-bordered table-striped" id="invoice_table">
                      <thead>
                          <th></th>
                          <th>No</th>
                          <th>Customer</th>
                          <th>Current Credit Balance</th>
                          <th>Total Credit Value</th>
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
<script>
    const s_date = '<?php echo $start_date?>'
    const e_date = '<?php echo $end_date?>'
</script>
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/creditNote/archive.js?202101190634') }}"></script>
@stop
