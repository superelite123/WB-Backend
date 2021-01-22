@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'AR Calendar')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/fullcalendar/fullcalendar.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/fullcalendar/fullcalendar.print.min.css') }}" media="print">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/radio.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/datatable-fixedWidthColumn.css') }}">
@stop
@section('content')
<div class="box box-info">
  <div class="box-header with-border">
    <h3 class='box-title'>AR Calendar</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
    </div>
  </div>
  <!-- /.box-header -->

  <div class="box-body" style='margin-bottom:0px'>
    <div class="row">
      <div class="col-md-12">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>
<!--TablePart-->
<div class="box box-info">
  <div class="box-header with-border">
    <h3 class='box-title'>Invoices</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
    </div>
  </div>
  <!-- /.box-header -->

  <div class="box-body">
    <div class="row">
      <div class="col-md-3">
        <div class="form-check">
          <label class='radio-label'>
              <input type="radio" name="radioPType" value='0' checked> <span class="label-text">Awaiting Verification</span>
          </label>
        </div>
      </div>
      <div class="col-md-3">
          <div class="form-check">
            <label class='radio-label'>
                <input type="radio" name="radioPType" value='1'> <span class="label-text">Awaiting Payment</span>
            </label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="form-check">
          <label class='radio-label'>
              <input type="radio" name="radioPType" value='2'> <span class="label-text">Paid</span>
          </label>
        </div>
      </div>
    </div>
    <!--/.option row-->
    <div class="row">
      <div class="col-md-3 form-check">
        <label class='radio-label'>
            <input type="radio" name="radioDType" value='30' checked> <span class="label-text">30</span>
        </label>
      </div>
      <div class="col-md-3 form-check">
        <label class='radio-label'>
            <input type="radio" name="radioDType" value='60'> <span class="label-text">60</span>
        </label>
      </div>
      <div class="col-md-3 form-check">
        <label class='radio-label'>
            <input type="radio" name="radioDType" value='9000'> <span class="label-text">90+</span>
        </label>
      </div>
    </div>
    <!--/.option row-->
    <div class="row">
      <div class="col-md-12">
        <table class='table table-bordered nowrap' id='invoice_table'>
          <thead>
            <th></th>
            <th>No</th>
            <th>Sales Order</th>
            <th>Invoice</th>
            <th>Client</th>
            <th>Total Price</th>
            <th>R Sub Total</th>
            <th>R Tax</th>
            <th>Date</th>
            <th>Delivered Date</th>
            <th>Term</th>
            <th>Payment Due Date</th>
            <th>Paid Date</th>
          </thead>
          <tbody></tbody>
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
          </tfoot>
        </table>
      </div>
    </div>
    <!--/.table row-->
  </div>
</div>
<div id="calendarModal" class="modal fade">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
            <h4 id="modalTitle" class="modal-title"></h4>
        </div>
        <div id="modalBody" class="modal-body">
          <!--Redirect Option-->
          <div class="col-md-12">
            <div class="form-group">
              <label>Redirect Option</label>
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fas fa-sliders-h"></i>
                </div>
                <select class="form-control select2" style="width: 100%;" id="rOptions">
                    <option value="0">Collect Payment</option>
                    <option value="1">Open Invoice</option>
                </select>
              </div>
              <!-- /.input group -->
              <span class="error"><p id="strain_error" style='color:red'></p></span>
            </div>
          </div>
          <!--./Redirect Option-->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-info btnCollection">Ok</button>
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
</div>
@stop
@include('footer')
<script>
  let calendarData  = {!! json_encode($cData) !!}
  let collectionUrl = '{{ $collectionUrl }}'
  let viewUrl       = '{{ $viewUrl }}'
  let signFileUrl   = '{{ $signFileUrl }}'
</script>
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/fullcalendar/fullcalendar.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/sign/p_verification/home.js') }}"></script>
@stop
