@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'NDA Management')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/datatable-fixedWidthColumn.css') }}">
@stop
@section('content_header')

@stop

@section('content')
<div class="box box-info main-panel">
    <div class="box-header with-border">
      <h1>NDA Management</h1>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
              <table class='table table-bordered' id='tbl_nda'>
                <thead>
                  <th>No</th>
                  <th>Name</th>
                  <th>Company Name</th>
                  <th>Email</th>
                  <th>Customer Type</th>
                  <th>Date</th>
                  <th>View</th>
                  <th>Delete</th>
                </thead>
                <tbody>
                  @foreach ($ndas as $key => $nda)
                    <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $nda->customer_name }}</td>
                    <td>{{ $nda->company_name }}</td>
                    <td>{{ $nda->email }}</td>
                    <td>{{ $nda->rCustomerType->name }}</td>
                    <td>{{ $nda->created_at }}</td>
                    <td>
                      <a href="{{ url('nda_management/view/'.$nda->id ) }}" target='_blank' class='btn btn-info'><i class="fas fa-file-invoice-dollar">&nbsp;</i>View</a>
                    </td>
                    <td>
                      <button class='btn btn-danger' onclick="deleteID('{{ $nda->id }}')">
                        <i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete ID
                      </button>
                    </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
        </div>
    </div>
</div>
<div class="box box-info main-panel">
    <div class="box-header with-border">
      <h1>VISITOR NDA HISTORY LOG</h1>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <label>Order Period:</label>

                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation">
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
              <table class='table table-bordered' id='tbl_log'>
                <thead>
                  <th>No</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>NDA Email</th>
                  <th>Date</th>
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
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/nda_m/home.js') }}"></script>
@stop
