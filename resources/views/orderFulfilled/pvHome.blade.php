@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Payments Awaiting Verification')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/datatable-fixedWidthColumn.css') }}">
@stop
@section('content_header')
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
      <h1>Payments Awaiting Verification</h1>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered" id="verification_table" style="width:100%">
                    <thead>
                        <th></th>
                        <th>No</th>
                        <th>Sales Order</th>
                        <th>Invoice</th>
                        <th>Client</th>
                        <th>Distributor</th>
                        <th>Total Price</th>
                        <th>Creation Date</th>
                        <th>Delivery Date</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
            <h4 id="signModalTitle" class="modal-title"></h4>
          </div>
        <div class="modal-body">
            <img src="" class="imagepreview" style="width: 100%;" >
        </div>
        </div>
    </div>
</div>
@stop
@include('footer')
<script>
    let verifies      = {!! json_encode($verifies) !!}
    for ( var i=0, ien=verifies.length ; i<ien ; i++ ) {
        verifies[i].no = i + 1
        verifies[i].total = "$" + verifies[i].total
        verifies[i].number  += '&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge badge-info">'
                            + verifies[i].items.length + '</span>'
    }
    let signFileUrl   = '{{ $signFileUrl }}'
</script>
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/pvHome.js') }}"></script>
@stop
