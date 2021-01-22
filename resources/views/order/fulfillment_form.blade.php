@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'New Order')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/fulfillment_form.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')

@stop

@section('content')
  <div class='row'>
      <a href="pending_list" class='backtolist pull-right'><i class="fas fa-hand-point-left"></i>Back To List</a>
  </div>
    <!--start edit form-->
  <div class="box box-info main-panel">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fas fa-file-invoice"></i> FulFill {{' Invoice'}} {{ $order->number }}</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-3">
            <label>Customer</label>
            <p>{{ $order->clientname }}</p>
          <!-- /.col -->
          </div>
          <div class="col-md-2">
            <label>Date:</label>
            <p>{{ $order->date }}</p>
          </div>
          <div class="col-md-2">
            <label>Invoice Number:</label>
            <p>{{ $order->number }}</p>
          </div>
          <div class="col-md-2">
            <label>Metrc Manifest:</label>
            <p>{{ $order->m_m_str }}</p>
          </div>
        </div>
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-md-12">
          <div class="col-md-2">
            <label>Sales Persion</label>
            <p>{{ $order->salesperson->firstname }}</p>
          </div>
          <div class="col-md-2">
            <label>Distributor:</label>
            <p>{{ $order->companyname }}</p>
          </div>
          <div class="col-md-2">
            <label>Term:</label>
            <p>{{ 'Term' }}</p>
          </div>
          <div class="col-md-3">
            <label>Order Note:</label>
            <p>{{ $order->note != ''?$order->note:'No Note' }}</p>
          </div>
          <div class="col-md-3">
            <label>Note for Fulfillment Team:</label>
            <p>{{ $order->fulfillmentnote != ''?$order->fulfillmentnote:'No Fulfillment note' }}</p>
          </div>
        </div>
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-md-12">
          <h3 class='req_item_list_title'>Inventory Requested</h3>
        </div>
        <div class="col-md-12 req_list_panel">
          <table class='table table-striped table-bordered table-fixed'>
            <thead>
              <tr>
              <th>No</th>
              <th>Strain</th>
              <th>Type</th>
              <th>Qty</th>
              <th>Units</th>
              <th>Base Price</th>
              <th>CPU</th>
              <th>Discount</th>
              <th>Discount Type</th>
              <th>Extra Discount</th>
              <th>Extended</th>
              <th>Tax</th>
              <th>Adjuested Price</th>
              <th>Line Note</th>
              <th>FulFilled Count</th>
              </tr>
            </thead>
            <tbody id='req_tbody'></tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <hr>
        </div>
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-md-12">
          <h3>Inventory for Invoice</h3>
        </div>
        <div class="col-md-12 inventory_panel">
          <table class='table table-striped table-responsive table-bordered table-fixed'>
            <thead>
              <th>No</th>
              <th>Combine</th>
              <th>Item</th>
              <th>Fulfillment scanned Metrc Tag</th>
              <th>Qty</th>
              <th>Weight</th>
              <th>Harvested Date</th>
              <th>COA</th>
              <th>Barcode</th>
            </thead>
            <tbody id='inventory_tbody'></tbody>
          </table>
        </div>
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-2">
          <button class="btn btn-danger btn-lg pull-right unableBtn"><i class="fas fa-stop"></i>&nbsp;Unable to FulFill</button>
        </div>
        <div class="col-md-2">
          <button class="btn btn-success btn-lg pull-right fulfillBtn"><i class="fa fa-upload"></i>&nbsp;FulFill Order</button>
        </div>

      </div>
    </div>
  </div>
    <!--end edit form-->
    <!-- /.box-body -->
    <div class="barcode_panel" style="display: none">
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/order/fulfillment_form.js') }}"></script>
@stop
