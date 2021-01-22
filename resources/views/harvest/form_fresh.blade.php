@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = 'Create Fresh Item'
@endphp
@section('title', $title)
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/form_fresh.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))

    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
@endforeach
<p class="alert alert-danger" style="display:none" id="input_warning"><span id="msg_field"></span><a href="#" class="close" aria-label="close">&times;</a></p>
<!--box-->
<div class="box box-info main-panel">
  <!--box header-->
  <div class="box-header with-border">
    <h3 class="box-title">{{ $title.':'.date('Y-m-d') }}</h3>
    
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
    </div>
  </div>
  <!--/box header-->
  <!--box body-->
  <div class="box-body">
    <!--row-top_line-->
    <div class="row top-line">
      <div class="col-md-2">
        <label>Original Harvest Batch ID:&nbsp;</label>
        <br>
        <span class="sp_batchId">{{ $harvest->harvest_batch_id }}</span>
      </div>
      <div class="col-md-1">
        <label>Strain:&nbsp;</label>
        <br>
        <span class="sp_strain">{{ $harvest->Strain->strain }}</span>
      </div>
      <div class="col-md-1">
        <label>License:&nbsp;</label>
        <br>
        <span>{{ $harvest->License->license }}</span>
      </div>
      <div class="col-md-1">
        <label>Unit of Weight:&nbsp;</label>
        <br>
        <span>{{ $harvest->UnitOfWeight->name.'-'.$harvest->UnitOfWeight->abbriviation }}</span>
      </div>
      <div class="col-md-2">
        <label>Total Dry Weight:&nbsp;</label>
        <br>
        <span>{{ $harvest->total_weight }}</span>
      </div>
    </div>
    <!--/row-top_line-->
    <div class="row input-field">
      <div class="col-md-12">
          <label for="" class="label_batch_info ">Input Field</label>
      </div>
      <!--Input Weight-->
      <div class="col-md-5 form-group">
          <label>Weight</label>
          <div class="input-group">
              <div class="input-group-addon">
              <i class="fas fa-file-signature"></i>
              </div>
              <div class="input-group" style="width:100%">
                  <input type="number" class="form-control" placeholder="Enter the Weight" size="24" maxlength="24" id="weight" name="weight">
              </div>
          </div>
      </div>
      <!-- /.form-group -->
      <!--Input Weight-->
      <div class="col-md-5 form-group">
          <label>Metrc Tag</label>
          <div class="input-group">
              <div class="input-group-addon">
                  <i class="fas fa-file-signature"></i>
              </div>
              <div class="input-group" style="width:100%">
                  <input type="text" class="form-control"  placeholder="Enter the Metrc Tag" size="24" maxlength="24" id="metrc" name="metrc">
              </div>
          </div>
      </div>
      <!-- /.form-group -->
      <!--Input Weight-->
      <div class="col-md-2 form-group">
          <button class="btn btn-info btnAdd" id="add_row"><i class="fa fa-fw fa-plus"></i> ADD Row</button>
      </div>
      <!-- /.form-group -->
    </div>
    <!--row table-->
    <div class="row col-md-12 table_panel">
      <table class="table table-bordered data-table" id="containerTable">
        <thead>
          <th>No</th>
          <th>Weight</th>
          <th>Tare</th>
          <th>Metrc Tag</th>
          <th>Print</th>
          <th>Remove</th>
        </thead>
        <tbody>

        </tbody>
    </table>
    </div>
    <!--./row tale-->
    <!--row-Build Button Area-->
    <div class="row">
        <div class="col-md-5"></div>
        <div class="col-md-4">
            <button type="button" class="btn btn-success btn-lg makeBtn"><i class="fas fa-eraser"></i>&nbsp;Create Fresh Item</button>
        </div>
        <div class="col-md-4"></div>
    </div>
    <!--/row-Build Button Area-->
  </div>
  <!--/box body-->
</div>
<!--/box-->
<div class="barcode_panel" style="display: none">
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/form_fresh.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop