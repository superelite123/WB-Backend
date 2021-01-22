@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Walnut FulFillment')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
        <h1>FA Export Log</h1>

        <div class="box-tools pull-right">
         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
         <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-bordered" id="invoice_table" style="width:100%">
                        <thead>
                            <th>No</th>
                            <th>User</th>
                            <th>Invoice</th>
                            <th>Date</th>
                        </thead>
                        <tbody>
                           @foreach ($data as $key => $item)
                              <tr>
                                 <td>{{ $key + 1 }}</td>
                                 <td>{{ $item->rUser->name }}</td>
                                 <td>{{ $item->OrderLabel }}</td>
                                 <td>{{ $item->created_at }}</td>
                              </tr>
                           @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</div>
@stop
@include('footer')
@section('js')
    <script type="text/javascript" src="{{ asset('assets/js/order/fa_export_log.js') }}"></script>
@stop

