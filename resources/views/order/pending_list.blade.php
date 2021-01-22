@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'WP Pending PO')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')

<style>
    .dataTables_scrollBody {
        overflow: scroll !important;
    }
</style>
    <!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
      <h1>Pending Approval to Fulfill</h1>

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
                  <table class="table table-bordered" id="invoice_table" style="width:100%;">
                      <thead>
                          <th></th>
                          <th>No</th>
                          <th>Sales Order</th>
                          <th>Client</th>
                          <th>Distributor</th>
                          <th>TBP</th>
                          <th>Discount</th>
                          <th>Extra Discount</th>
                          <th>Sub</th>
                          <th>PR-Value</th>
                          <th>ETax</th>
                          <th>Total Due</th>
                          <th>Total Debt</th>
                          <th>ETC</th>
                          <th>Date</th>
                          <th>Priority</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                      </thead>
                      <tbody>
                      </tbody>
                      <tfoot style='background-color:#e8e8e8'>
                        <th colspan=2>
                            <h4>TBP:&nbsp;&nbsp;<span style="color:green" class="footer-tbp">$0</span></h4>
                        </th>
                        <th colspan=3>
                            <h4>Discount:&nbsp;&nbsp;<span style="color:green" class="footer-discount">$0</span></h4>
                        </th>
                        <th colspan=3>
                            <h4>Extra Discount:&nbsp;&nbsp;<span style="color:green" class="footer-e_discount">$0</span></h4>
                        </th>
                        <th colspan=3>
                            <h4>Sub:&nbsp;&nbsp;<span style="color:green" class="footer-sub">$0</span></h4>
                        </th>
                        <th colspan=3>
                            <h4>PR-Value:&nbsp;&nbsp;<span style="color:green" class="footer-pr_value">$0</span></h4>
                        </th>
                        <th colspan=3>
                            <h4>ETax:&nbsp;&nbsp;<span style="color:green" class="footer-e_tax">$0</span></h4>
                        </th>
                        
                        <th colspan=3>
                            <h4>Total Due:&nbsp;&nbsp;<span style="color:green" class="footer-total_due">$0</span></h4>
                        </th>
                        <th colspan=4>
                            <h4>Total Debt:&nbsp;&nbsp;<span style="color:green" class="footer-total_debt">$0</span></h4>
                        </th>
                      </tfoot>
                  </table>
              </div>
          </div>
      </div>
      <!-- /.box-body -->
</div>
</div>
<div class="modal fade" id="modal-discount">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Add Discount</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Enter Discount Note:</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fas fa-tags"></i>
                          </div>
                          <input type="text" class="form-control" id='discount_note' placeholder="Enter Reason">
                        </div>
                        <!-- /.input group -->
                    </div>
                    <!-- /.form-group -->
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Enter Discount Amount:</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-comments-dollar"></i>
                            </div>
                            <input type="number" placeholder="Enter Discount" class="form-control" id='discount_amount' value='0'>
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
            <button class="btn btn-info pull-right" id="btnAddDiscount"><i class="fas fa-save"></i>ADD</button>
          </div>
          <!--./modal footer-->
      </div>
    </div>
</div>
@stop
@include('footer')
<script>
    let priorities = {!! json_encode($priorities) !!}
</script>
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/ajax_loader.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/order/pending_list.js?v=202101140122') }}"></script>
@stop
