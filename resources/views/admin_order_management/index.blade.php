@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Dashboard')

@section('css')
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
        <form action="{{ route('admin.order.find') }}">
        <div class="row">
            <div class="col-md-3">
                
                <div class="form-group">
                    <label>Invoice ID:</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fas fa-sliders-h"></i>
                        </div>
                        <input type="text" class="form-control" id="id" name="id">
                    </div>
                    <span class="error"><p id="qty_error" style='color:red'></p></span>
                    <!-- /.i-->
                </div>

            </div>
            <div class="col-md-3">
                <button class="btn btn-info">Find</button>
            </div>
        </div>
        </form>
    </section>
    <!-- /.content -->
@stop
@section('js')
@stop

