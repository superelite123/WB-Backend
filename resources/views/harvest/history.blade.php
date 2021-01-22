@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Harvest Status')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/harvest/history.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
@if ($message = Session::get('success'))
  <div class="alert alert-success">
      <p>{{ $message }}</p>
  </div>
@endif
<div class="box box-success">
    <div class="box-header with-border">
      <h1>Harvest Statistical Overview</h1>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>Harvest Period:</label>

                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation">
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Status</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fas fa-dna"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" name="mode" id="mode">
                            <option value="0">All</option>
                            <option value="1">Dynamics</option>
                            <option value="2">Dry</option>
                            <option value="3">Curing</option>
                            <option value="4">Holding Inventory</option>
                            <option value="5">Finished Good</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-info"  style="margin-top:1.5em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered" id="history_table">
                    <thead>
                        <th>No</th>
                        <th>Harvest Batch ID</th>
                        <th>Dynamics</th>
                        <th>Dry</th>
                        <th>Curing</th>
                        <th>Holding</th>
                        <th>Finished Good</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
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
    </div>
</div>
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/js/harvest/history.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
@stop
