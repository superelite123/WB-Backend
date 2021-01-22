@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Walnut Financial')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/datatable-fixedWidthColumn.css') }}">
@stop
<style>
    @import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
    modal-content {
        border-radius: 0px;
        box-shadow: 0 0 20px 8px rgba(0, 0, 0, 0.7);
    }

    .modal-backdrop.show {
        opacity: 0.0;
    }
    table.dataTable {
        clear: both;
        margin-top: 6px !important;
        margin-bottom: 6px !important;
        max-width: none !important;
        border-collapse: separate !important;
        font-size: small !important;
    }
    table {
        border-spacing: 0;
        border-collapse: collapse;
        font-size: small !important;
    }
    .invoice_table_panel > div.dataTables_wrapper > div.row > div.col-sm-12 > div.dataTables_scroll > div.dataTables_scrollBody {
        overflow: visible !important;
    }
    .radio-label{
        position: relative;
        cursor: pointer;
        color: #666;
        font-size: 23px;
    }

    input[type="radio"]{
        position: absolute;
        right: 9000px;
    }

    /*Radio box*/

    input[type="radio"] + .label-text:before{
        content: "\f10c";
        font-family: "FontAwesome";
        speak: none;
        font-style: normal;
        font-weight: normal;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing:antialiased;
        width: 1em;
        display: inline-block;
        margin-right: 5px;
    }

    input[type="radio"]:checked + .label-text:before{
        content: "\f192";
        color: #8e44ad;
        animation: effect 250ms ease-in;
    }
    @keyframes effect{
        0%{transform: scale(0);}
        25%{transform: scale(1.3);}
        75%{transform: scale(1.4);}
        100%{transform: scale(1);}
    }
</style>
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
      <h1>Invoice Export</h1>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
      <div class="box-body">
          <div class="row">
            <div class="col-md-2">
                <div class="form-check">
                    <label class='radio-label'>
                        <input type="radio" name="invoiceType" value='0'> <span class="label-text">Show All</span>
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <label class='radio-label'>
                        <input type="radio" name="invoiceType" value='1' checked> <span class="label-text">Only Delivered</span>
                    </label>
                </div>
            </div>
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
              <div class="col-md-2"></div>
              <div class="col-xs-2">
                <button class="btn btn-info pull-right"  style="margin-top:1.5em" id="btn_export_invoice_hopper" class="export"><i class="fa fa-download"></i>&nbsp;CSV Download</button>
              </div>
              <div class="col-xs-2">
                  <button class="btn btn-info pull-right"  style="margin-top:1.5em" id="btn_export_invoice_maritsa" class="export"><i class="fa fa-download"></i>&nbsp;Maritsa</button>
              </div>
          </div>
          <div class="row">
              <div class="col-xs-12 invoice_table_panel" style="overflow:auto">
                  <table class="table table-bordered" id="invoice_table" style='width:100%'>
                        <thead>
                            <th>Client Legal Name</th>
                            <th>Customer Name</th>
                            <th>Account Rep</th>
                            <th>Metrc Manifest</th>
                            <th>Invoice</th>
                            <th>Creation Date</th>
                            <th>Delivery Date</th>
                            <th>Terms</th>
                            <th>Total Qty Grams</th>
                            <th>Total flower grams</th>
                            <th>Total pre roll grams</th>
                            <th>Total conce ntrate</th>
                            <th>Total Invoice</th>
                            <th>Discount</th>
                            <th>Net Invoice</th>
                            <th>Excise Tax</th>
                            <th>Total Price</th>
                            <th>Exported</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <th colspan=2>
                                <h4>QtyGrams:&nbsp;&nbsp;<span style="color:green" id="footer-qty_grams">0</span></h4>
                            </th>
                            <th colspan=3>
                                <h4>Total Invoice:&nbsp;&nbsp;<span style="color:green" id="footer-total_invoice">$0</span></h4>
                            </th>
                            <th colspan=3>
                                <h4>Discount:&nbsp;&nbsp;<span style="color:green" id="footer-discount">$0</span></h4>
                            </th>
                            <th colspan=4>
                                <h4>Net Invoice:&nbsp;&nbsp;<span style="color:green" id="footer-net_invoice">$0</span></h4>
                            </th>
                            <th colspan=3>
                                <h4>Excise Tax:&nbsp;&nbsp;<span style="color:green" id="footer-e_tax">$0</span></h4>
                            </th>
                            <th colspan=4>
                                <h4>Total Due:&nbsp;&nbsp;<span style="color:green" id="footer-total_due">$0</span></h4>
                            </th>
                        </tfoot>
                  </table>
              </div>
          </div>
      </div>
      <!-- /.box-body -->
</div>
<div class="box box-info">
    <div class="box-header with-border">
      <h1>Customer Export</h1>

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
                    <label>Customer Period:</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation_customer">
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="col-xs-3"></div>
            <div class="col-xs-3">
                <button class="btn btn-info pull-right"  style="margin-bottom:1.5em;" id="export_customer_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
            </div>
        </div>
          <div class="row">
              <div class="col-xs-12">
                <table class="table table-bordered nowrap" id="customer_table" style='width:100%'>
                    <thead>
                        <th></th>
                        <th>DBA/Short Name</th>
                        <th>Legal Name</th>
                        <th>Address 1</th>
                        <th>Address 2</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Zip</th>
                        <th>Phone</th>
                        <th>Fax</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>Payment Terms</th>
                        <th>Resale #</th>
                        <th>Customer Type</th>
                        <th>Cannabis Lic</th>
                        <th>City/Business Lic</th>
                        <th>EIN</th>
                        <th>Created</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
              </div>
          </div>
      </div>
      <!-- /.box-body -->
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
@stop
@include('footer')
<script>
    let collectionUrl = '{{ $collectionUrl }}'
    let viewUrl       = '{{ $viewUrl }}'
    let signFileUrl   = '{{ $signFileUrl }}'
</script>
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/ajax_loader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/admin/financial_export.js?v=202101140340') }}"></script>
@stop
