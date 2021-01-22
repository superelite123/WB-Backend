@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = $mode=='create'?'New':'Edit' 
@endphp
@section('title', $title." Harvest")
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/form.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
@include('alerts')
<div class="box box-info main-panel">
  <div class="box-header with-border">
    <h3 class="box-title">You are creating a {{ $mode=='create'?'New':'Edit'}}{{' Harvest Date '.date('Y-m-d') }}</h3>
    
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
    </div>
  </div>
  
  <div class="box-body">
    <div class="row">
         <div class="col-md-3">
          <div class="form-group">
            <label>License</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-file-signature"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="license" id="license">
              <option value="0"></option>
                @foreach($cultivator_list as $license)
                  <option value="{{ $license->cultivator_id }}" {{ $license->cultivator_id == $harvest->cultivator_license_id?'selected':''}}>{{ $license->companyname.' - '.$license->license }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- /.form-group -->
        </div>
        <!-- /.col -->
        <div class="col-md-3">
          <div class="form-group">
            <label>Flower Room Location</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-door-open"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="company" id="company">
              <option value="0"></option>
                @foreach($company_list as $room)
                  <option value="{{ $room->location_id }}" {{ $room->location_id == $harvest->cultivator_company_id?'selected':''}}>{{ $room->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- /.form-group -->
        </div>
        <!-- /.col -->
        <div class="col-md-3">
          <div class="form-group">
            <label>Strain - Alias</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-dna"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="strain" id="strain">
                  <option value="0"></option>
                @foreach($strain_list as $strain)
                  <option value="{{ $strain->itemname_id }}" {{ $strain->itemname_id == $harvest->strain_id?'selected':''}}>{{ $strain->strain.' - '.$strain->strainalias }}</option>
                @endforeach
              </select>
          </div>
          </div>
          <!-- /.form-group -->
        </div>
        <!-- /.col -->
       
        <div class="col-md-3">
          <div class="form-group">
            <label>Unit of Weight</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-weight"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="unit" id="unit">
                @foreach($unit_list as $unit)
                @if ($unit->unit_id == 4)
                  <option value="{{ $unit->unit_id }}" {{ $unit->unit_id == $harvest->unit_weight?'selected':''}}>{{ $unit->name.'-'.$unit->abbriviation }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>
          <!-- /.form-group -->
        </div>
        <!-- /.col -->
    </div>
    <div class="row">
      <div class="col-md-12">
          <hr class="first">
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Plant tag:</label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fas fa-cannabis"></i>
            </div>
            <div class="input-group">
              <input type="text" class="form-control" size="24" maxlength="24" id="plant" name="plant">
            </div>
          </div>
          <!-- /.input group -->
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>Weight:</label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fas fa-weight"></i>
            </div>
            <div class="input-group">
              <input type="number" class="form-control" id="weight" name="weight">
            </div>
          </div>
          <!-- /.input group -->
        </div>
      </div>
      <div class="col-md-2">
        <button class="btn btn-info" id="add_row"><i class="fa fa-fw fa-plus"></i> ADD Plant</button>
      </div>
       <div class="col-md-2 item_count_panel" style="text-align: center;">
        <label for="">Harvested by user: </label><br/><label class="count_panel" id="session_count">0</label>
      </div>
      <div class="col-md-2 item_count_panel"  style="text-align: center;">
        <label for="">Harvest Total: </label><br/><label class="count_panel" id="item_count">0</label>
      </div>
    </div>
    <!--./row-->
    <!--/insert temp table row-->
    <div class="row">
      <div class="col-md-12 temp_table_panel">
          <table class="table table-bordered data-table" id="temp_table">
            <thead>
              <tr>
                <th>No</th>
                <th>Plant Tag</th>
                <th>Weight</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
      </div>
    </div>
    <!--./insert temp table row-->
  </div>
</div>
<div id="snackbar">This Plant Belongs to an active Harvest. Merging with existing Harvest.</div>
<div class="row">
  <div class="col-md-12">
      <button class="btn btn-success btn-lg makeBtn"><i class="fas fa-eraser"></i>&nbsp;{{ $mode=='create'?'Process':'Update'}} Harvest</button>
  </div>
</div>

<div class="row">
    <div class="col-md-12">
    <i class="fas fa-code-branch"></i> Version 9 8th November 2019 - Node A Release)
</div>
</div>
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/form.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop