@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Signature')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
<div class="box box-blue">
    <div class="box-header with-border">
      <h1>Sginature Collection</h1>

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
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered" id="invoice_table">
                      <thead>
                          <th></th>
                          <th>No</th>
                          <th>Invoice id</th>
                          <th>Customer</th>
                          <th>Distributor</th>
                          <th>Total Cost</th>
                          <th>Date</th>
                          <th>SIGN</th>
                          <th>Note</th>
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
<div class="modal fade" id="modal_deliver_note">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Deliver Note</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label>Add Deliver Note:</label>
              <div class="input-group">
                <div class="input-group-addon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <textarea type="number" cols="45" rows="7" class="form-control" id="deliver_note"></textarea>
              </div>
              <!-- /.input group -->
            </div>
            <!-- /.form-group -->
          </div>
        </div>
      </div>
      <!--./modal body-->
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button class="btn btn-info pull-right" id="save_deliver_note">Save Changes</button>
      </div>
      <!--./modal footer-->
    </div>
    <!--./modal content-->
  </div>
  <!--./modal dialog-->
</div>
<!--./modal-->
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/order/signature_list.js') }}"></script>
@stop