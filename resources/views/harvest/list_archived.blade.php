@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Archived Harvest')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/harvest/list_dry.css') }}">
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
      <h1>Archived Harvest List</h1>
      <h4>Will show only Archived Harvests</h4>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
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
                <button class="btn btn-info"  style="margin-top:1.6em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered" id="harvest_table">
                    <thead>
                        <th></th>
                        <th>No</th>
                        <th>Harvest Batch ID</th>
                        <th>Total Plant Count</th>
                        <th>Total Wet Weight (gr)</th>
                        <th>Total Wet Weight (lbs)</th>
                        <th>Ant. Dry Weight (lbs)</th>
                        <th>Ant. Dry Weight (ounce)</th>
                        <th>Ant. Dry Weight (grams)</th>
                        <th>Flower Room Location</th>
                        <th>Strain</th>
                        <th>License</th>
                        <th>Unit Of Weight</th>
                        <th>Creation Date</th>
                        <th>CSV</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th id="total_count">Total Plant Count</th>
                        <th id="total_weight">Total Wet Weight (gr)</th>
                        <th id="total_pounds_wet">Total Wet Weight (lbs)</th>
                        <th id="ant_dry_weightlbs">Ant Dry Weight (lbs)</th>
                        <th id="ant_dry_weighton">Ant Dry Weight (on)</th>
                        <th id="ant_dry_weightgr">Ant Dry Weight (gr)</th>
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
  <script type="text/javascript" src="{{ asset('assets/js/harvest/list_archived.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
@stop
