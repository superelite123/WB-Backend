@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Plant Room Builder')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/harvest/room_builder.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/icheck/all.css') }}">
@stop
@section('content')
<div class="flash-message">
    <!--start edit form-->
    <div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    </div>
</div>
<div class="box box-success">
    <!--Box Header-->
    <div class="box-header with-border">
      <h1>Plant Room Builder</h1>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <!--Box Body-->
    <div class="box-body">
        <!--Top Bar-->
        <div class="row top_bar">
            <!--Date Range-->
            <div class="col-md-6">
                <div class="form-group">
                    <label>Room Period:</label>

                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation">
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <!--/.Date Range-->
            <!--New Room Builder Button-->
            <div class="col-md-3 pull-right">
                <button class="btn btn-info pull-right" style="margin-top:1.6em;margin-right:1em" id="btn_new"><i class="fas fa-plus"></i>&nbsp;&nbsp;New Plant Room Builder</button>            </div>
            <!--/.New Room Builder Button-->
        </div>
        <!--/.Top Bar-->
        <!---->
        <div class="row col-md-12">
            <table class="table table-bordered" id="harvest_table">
                <thead>
                    <th>No</th>
                    <th>Room Name</th>
                    <th>User Name</th>
                    <th>Matrix Type</th>
                    <th>Created Date</th>
                    <th>Edit</th>
                </thead>
                <tbody>

                </tbody>
            </table>
            
        </div>
        <!--/.-->
    </div>
    <!--/.Box Body-->
</div>
<!--/.Box-->
<div class="modal fade" id="modal_picker">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select the Room and Matrix Type</h4>
            </div>
            <div class="modal-body">
                <p class="alert alert-danger" style="display:none" id="modal_alert_panel">
                    <span id="modal_error_message"></span>
                    <a href="#" class="close" id="modal_close_alert" aria-label="close">&times;</a>
                </p>
                <div class="col-md-12 form-group" style="margin-top:1em">
                    <label>Room</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" name="rooms" id="rooms">
                            <option value="0"></option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->location_id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- /.form-group -->
                </div>
                <!-- /.col-md-12 -->
                <!-- radio -->
                <div class="col-md-12">
                    <label for=""></label>
                </div>
                <div class="col-md-12 form-group">
                    <label>
                        <input type="radio" name="matrix_type" value='3' class="flat-red">
                        4*3
                    </label>
                    <label>
                        <input type="radio" name="matrix_type" value='4' class="flat-red" checked>
                        4*4
                    </label>
                    <label>
                        <input type="radio" name="matrix_type" value='5' class="flat-red">
                        4*5
                    </label>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sd pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info saveBtn"> <i class="fas fa-arrow-right"></i> Build Room Matrix</button>
            </div>
        </div>
    </div>
</div>
@stop

@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/icheck.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/room_builder.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
@stop