@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = 'Dry Weight Conversion'
@endphp
@section('title', $title)
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/form_dry.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
<!--box-->
<div class="box box-info main-panel">
  <!--box header-->
  <div class="box-header with-border">
    <h3 class="box-title">{{ $title.' : '.date('Y-m-d') }}</h3>
    
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
      <div class="col-md-1">
        <label>Strain:&nbsp;</label>
        <br>
        <span>{{ $harvest->Strain->strain }}</span>
      </div>
      <div class="col-md-2">
        <label>License:&nbsp;</label>
        <br>
        <span>{{ $harvest->License->license }}</span>
      </div>
      <div class="col-md-2">
        <label>Unit of Weight:&nbsp;</label>
        <br>
        <span>{{ $harvest->UnitOfWeight->name.'-'.$harvest->UnitOfWeight->abbriviation }}</span>
      </div>
      <div class="col-md-2">
        <label>Total Dry Weight:&nbsp;</label>
        <br>
        <span>{{ $originalTotalWeight }}</span>
      </div>
      <div class="col-md-3">
        <label>Original Number Of Plant Tags:&nbsp;</label>
        <br>
        <span>{{ $originalItemCount }}</span>
      </div>
      <div class="col-md-2">
        <label>Original Harvest Batch ID:&nbsp;</label>
        <br>
        <span>{{ $parentHarvestBatchId }}</span>
      </div>
    </div>
    <!--/row-top_line-->
    <!--row-add_plant-tag-->
    <div class="row add-line">

      <div class="col-md-3">
        <div class="form-group">
          <label>Dry Weight:</label>

          <div class="input-group">
            <div class="input-group-addon">
              <i class="fas fa-balance-scale"></i>
            </div>
            <input type="number" class="form-control" id="weight" name="weight" placeholder="Enter the Weight">
          </div>
          <!-- /.input group -->
        </div>
      </div>
      <div class="col-md-7 addtional-row-panel">
        <div class="col-md-3">
          <button class="btn btn-info" id="add_row"><i class="fa fa-fw fa-plus"></i> ADD Row</button>
        </div>
        <div class="col-md-4 item_count_panel">
          <label for="">User has entered: </label><label class="count_panel" id="session_count">0</label>
        </div>
        <div class="col-md-5 item_count_panel">
          <label for="">Remaning Items to Weight: </label><label class="count_panel" id="item_count">{{ $originalItemCount }}</label>
        </div>
      </div>
    </div>
    <!--/row-add_plant-tag-->
    <!--row-temp-table-panel-->
    <div class="row temp_table_panel">
      <div class="col-md-12">
        <label class="label-temp_table_panel">Inserted Weight</label>
      </div>
      <div class="col-md-12">
        <table class="table table-bordered data-table" id="temp_table">
          <thead>
            <tr>
              <th>No</th>
              <th>Weight</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
    <!--row-temp-table-panel-->
    <!--row-Build Button Area-->
    <div class="row">
      <div class="col-md-12">
          <button class="btn btn-success btn-lg makeBtn"><i class="fa fa-upload"></i>&nbsp; Process Dry Harvest</button>
      </div>
    </div>
    <!--/row-Build Button Area-->
  </div>
  <!--/box body-->
</div>
<!--/box-->
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/form_dry.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop