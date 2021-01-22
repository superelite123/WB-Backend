@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Delete Fulfilled Order')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
    
@stop

@section('content')
<div class="box box-danger main-panel">
    <div class="box-header with-border">
      <h3 class="box-title" style='color:red'><i class="fas fa-file-invoice"></i> Delete FulFilled Invoice:{{ $invoice->number }}</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <table class='table table-striped table-bordered' id='inventory_table'>
                    <thead>
                        <th>No</th>
                        <th>Strain</th>
                        <th>Product Type</th>
                        <th>Metrc Tag</th>
                        <th>Qty</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
            </div>
            <div class="col-md-2">
                <button class='btn btn-danger btn-lg' id='btnSubmit'><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Delete Invoice</button>
            </div>
        </div>
    </div>
</div>
@stop
<script>
    let invoice = {!! json_encode($invoice) !!}
    console.log(invoice)
</script>
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/delete.js') }}"></script>
@stop 