@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Dashboard')

@section('content_header')
    <h4><span>Current Time: <span id='now_time'></span></span><span style="margin-left:20px;">On site Members: 
    @if( $mode == 3 || $mode == 4)
        {{ $clocking_drivers->count() }}
    @endif
    </span></h4>
@stop
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/clocking.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <style>
    .content-header>.breadcrumb {
    float: right;
    background: 0 0;
    margin-top: 0;
    margin-bottom: 0;
    font-size: 12px;
    padding: 7px 5px;
    position: absolute;
    top: 0px;
    right: 10px;
    border-radius: 2px;
}
  </style>
@stop
@section('content')
   <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <small>{{config('company.COMPANY_NAME')}}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li <?php echo $mode == 1?'class="active"':"";?> ><a href='javascript:switchTab(1)'>Lunch Break</a></li>
                        <li <?php echo $mode == 2?'class="active"':"";?> ><a href='javascript:switchTab(2)'>Return from Lunch</a></li>
                        <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                    </ul>
                    <div class="tab-content">
                        <!--./Lunch Break-->
                        <div class="tab-pane <?php echo $mode == 1?'active':'';?>" id="tab_3">
                            <div class='row'>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name ::</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                            <i class="fas fa-dna"></i>
                                            </div>
                                            <select class="form-control select2" style="width: 100%;" name="drivers" id="drivers">
                                                <option value="0"></option>
                                                @foreach ($drivers as $driver)
                                                    <option value="{{ $driver->contact_id }}">{{ $driver->lastname }}, {{ $driver->firstname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class='btn btn-lg btn-info' id='btn_lunch_in' style='margin-top:20px'>Lunch Break</button>
                                </div>
                            </div>
                        </div>
                        <!--Lunch Break-->
                        <!--Return From Lunch-->
                        <div class="tab-pane <?php echo $mode == 2?'active':'';?>" id="tab_4">
                            <div class="row">
                                <div class='col-md-12'>
                                    <h2 id='now_time'></h2>
                                </div>
                                <div class="col-md-12">
                                    <table class='table table-striped table-bordered' id='tbl_out_lunch'>
                                        <thead>
                                            <th>Name</th>
                                            <th>Start Time</th>
                                            <th>Return From Lunch</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--./Return from Lunch-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop
@include('footer')
@section('js')
        <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/clocking_distro.js') }}"></script>
@stop

