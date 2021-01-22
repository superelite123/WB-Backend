@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = 'Dry Weight Page'
@endphp
@section('title', $title)
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/form_transfer.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
<div class="flash-message">
@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))

    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
@endforeach
</div>
<!--box-->
<div class="box box-info main-panel">
    <!--box header-->
    <div class="box-header with-border">
        <h3 class="box-title">You are going to Transfer{{' Harvest Date '.date('Y-m-d') }}</h3>
        
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    
    <!--/box header-->
    <!--box body-->
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
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
                </div>
                <!-- /.form-group -->
            </div>
            <!-- /.colmd-4 -->
            <div class="col-md-8"></div>
            <div class="col-md-3">
                <div class="form-group">
                <label>Harvest Batch Id / Metrc Tag / UPC:</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fas fa-cannabis"></i>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" size="24" maxlength="24" id="barcode" name="barcode">
                    </div>
                </div>
                <!-- /.input group -->
                </div>
            </div>
            <!-- /. col-md-3 -->
            <div class="col-md-2">
                <button class="btn btn-info" id="add_row"><i class="fa fa-fw fa-plus"></i>&nbsp;ADD</button>
            </div>
            <!-- /. col-md-2 -->
        </div>
        <!-- /.top ro -->
        <div class="row">
            <div class="col-md-12">
                <hr class="first">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 temp_table_panel">
                <table class="table table-bordered data-table" id="temp_table">
                    <thead>
                        <tr>
                        <th>No</th>
                        <th>Tag</th>
                        <th>Type</th>
                        <th>Current Room</th>
                        <th>Target Room</th>
                        <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5"></div>
            <div class="col-md-2">
                <button class="btn btn-success btn-lg makeBtn"><i class="fas fa-eraser"></i>&nbsp;Move</button>
            </div>
            <div class="col-md-5"></div>
        </div>
        <!-- /.hr -->
    </div>
    <!--./box body-->
</div>
@stop
@include('footer')
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/harvest/form_transfer.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop