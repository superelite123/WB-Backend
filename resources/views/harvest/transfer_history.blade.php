@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = 'Transfer History'
@endphp
@section('title', $title)
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/transfer_history.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
<!--box-->
<div class="box box-info dashboard">
    <!--box header-->
    <div class="box-header with-border">
        <h3 class="box-title">Transfer History</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!--/box header-->
    <!--box body-->
    <div class="box-body">
        
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab">Barcode</a></li>
                        <li><a href="#tab_2" data-toggle="tab">Room</a></li>
                        <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                                <div class="row topbar">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="input-group input-group-lg">
                                                <input type="text" class="form-control" id="batch_id" name="batch_id" placeholder="Please enter the Harvest Batch ID" >
                                                <span class="input-group-btn">
                                                    <button type="button" id="btnSearch" class="btn btn-info btn-flat"> <i class="fas fa-arrow-right"></i> Go</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr class="first">
                                    </div>
                                </div>
                        
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-hover dt-responsive display nowrap" id="harvest_table">
                                            <thead>
                                                <th>No</th>
                                                <th>Barcode</th>
                                                <th>Type</th>
                                                <th>User</th>
                                                <th>Room</th>
                                                <th>Moved Date</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                        <div class="tab-pane" id="tab_2">
                            <div class="row topbar">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Room</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fas fa-file-signature"></i>
                                                </div>
                                                <select class="form-control select2" style="width: 100%;" name="rooms" id="rooms">
                                                    <option value="0" selected disabled>Select the Room</option>
                                                    @foreach ($rooms as $room)
                                                        <option value="{{ $room->location_id }}">{{ $room->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <hr class="first">
                                </div>
                            </div>
                    
                            <div class="row">
                                <div class="col-md-12">
                                        <table class="table table-bordered" id="harvest_table1">
                                            <thead>
                                                <th></th>
                                                <th>No</th>
                                                <th>Barcode</th>
                                                <th>Type</th>
                                                <th>User</th>
                                                <th>Moved Date</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--/box body-->
</div>
<!--/box-->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">              
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <img src="" class="imagepreview" style="width: 100%;" >
        </div>
        </div>
    </div>
</div>
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/transfer_history.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop