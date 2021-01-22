@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Harvest Traker')
@section('css')
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
      <h1>Harvest List</h1>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered" id="tracker_table">
                    <thead>
                        <th></th>
                        <th>No</th>
                        <th>Harvest Batch ID</th>
                        <th>Strain</th>
                        <th>Type</th>
                        <th>Current location</th>
                        <th>Allocatedweight</th>
                        <th>Creation Date</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/js/CC/harvest_tracker_list.js') }}"></script>
@stop
