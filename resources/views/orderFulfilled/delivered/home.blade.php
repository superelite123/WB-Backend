@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Walnut Delivered')
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
      <h1>Walnut Delivered</h1>

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
              <div class="col-xs-3"></div>
              <div class="col-xs-3">
                  <button class="btn btn-info pull-right"  style="margin-top:1.5em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
              </div>
          </div>
          <div class="row">
              <div class="col-xs-12">
                  <table class="table table-bordered" id="invoice_table" style='width:100%'>
                      <thead>
                          <th></th>
                          <th>No</th>
                          <th>Sales Order</th>
                          <th>Invoice</th>
                          <th>Sales Rep</th>
                          <th>Customer</th>
                          <th>Total Price</th>
                          <th>R Sub Total</th>
                          <th>R Tax</th>
                          <th>Creation Date</th>
                          <th>Delivered Date</th>
                          <th>Actions</th>
                      </thead>
                      <tbody>
                      </tbody>
                      <tfoot>
                          <th colspan=3>
                              <h4>Sub Total:&nbsp;&nbsp;<span style="color:green" class="footer-sub_total">$0</span></h4>
                          </th>
                          <th colspan=2>
                              <h4>Discount Total:&nbsp;&nbsp;<span style="color:green" class="footer-discount_total">$0</span></h4>
                          </th>
                          <th colspan=2>
                              <h4>Extra Discount Total:&nbsp;&nbsp;<span style="color:green" class="footer-e_discount_total">$0</span></h4>
                          </th>
                          <th colspan=2>
                              <h4>Tax Total:&nbsp;&nbsp;<span style="color:green" class="footer-tax_total">$0</span></h4>
                          </th>
                      </tfoot>
                  </table>
              </div>
          </div>
      </div>
      <!-- /.box-body -->
</div>
</div>
<div class="modal fade" id='loadingModal' id="modal-email-confirm">
    <div class="modal-dialog">
        <div class="modal-content"  style="height:150px">
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <img src="{{ asset('assets/loading1.gif') }}" style="width:100px;height:100px">
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <p>Loading CSV Data...</p>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
            <h4 id="signModalTitle" class="modal-title"></h4>
          </div>
        <div class="modal-body">
            <img src="" class="imagepreview" style="width: 100%;" >
        </div>
    </div>
</div>
@stop
@include('footer')
<script>
</script>
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/ajax_loader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/delivered/home.js?v=202101160401') }}"></script>
@stop
