@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = 'Dry Weight Page'
@endphp
@section('title', $title)
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/form_curning.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/loadingspin/ladda-themeless.min.css') }}">
@stop
@section('content')
<div class="flash-message">
@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))

    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
@endforeach
</div>
<p class="alert alert-danger" style="display:none" id="input_warning"><span id="msg_field"></span><a href="#" class="close" aria-label="close">&times;</a></p>
<!--box-->
<div class="box box-info main-panel">
    <!--box header-->
    <div class="box-header with-border">
    <h3 class="box-title">{{ 'Curing'.':'.date('Y-m-d') }}</h3>

    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
    </div>
    </div>
    
    <!--/box header-->
    <!--box body-->
    <div class="box-body">
        <!--row-top_line-->
        <div class="row top-line batch_info">
            <div class="col-md-12">
                <label for="" class="label_batch_info ">Batch Info</label>
            </div>
            <div class="col-md-2">
                <label>BatchID:&nbsp;</label>
                <span class="sp_batchId">{{ $batchId }}</span>
            </div>
            <div class="col-md-2">
                <label>Strain:&nbsp;</label>
                <span class="sp_strain">{{ $curning->Strain->strain }}</span>
            </div>
            <div class="col-md-3">
                <label>Process Date:&nbsp;</label>
                <span>{{ $curning->created_at }}</span>
            </div>
            <div class="col-md-2">
                <label>Total Dry Weight:&nbsp;</label>
                <span>{{ $curning->total_weight }}</span>
            </div>
            <div class="col-md-2">
                <label>Remain Dry Weight:&nbsp;</label>
                <span>{{ $curning->remain_weight }}</span>
            </div>
        </div>
        <div class="row input-field">
            <div class="col-md-12">
                <label for="" class="label_batch_info ">Input Field</label>
            </div>
            <!--Input Weight-->
            <div class="col-md-2 form-group">
                <label>Weight</label>
                <div class="input-group">
                    <div class="input-group-addon">
                    <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="input-group">
                        <input type="number" class="form-control" placeholder="Enter the Weight" size="24" maxlength="24" id="weight" name="weight">
                    </div>
                </div>
            </div>
            <!-- /.form-group -->
            <!--Input Weight-->
            <div class="form-group col-md-2">
                <label>Product Type</label>
                <div class="input-group">
                    <div class="input-group-addon">
                    <i class="fas fa-file-signature"></i>
                    </div>
                    <select class="form-control select2" style="width: 100%;" name="type" id="type">
                        <option value="0" disabled selected>Select Product Type</option>
                        @foreach($productype as $item)                        
                        <option value="{{ $item->producttype_id }}">{{ $item->producttype }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- /.form-group -->
            <!--Input Weight-->
            <div class="form-group col-md-2">
                <label>Tare Weight</label>
                <div class="input-group">
                    <div class="input-group-addon">
                    <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="input-group">
                        <input type="number" class="form-control" placeholder="Tare Weight" disabled size="24" maxlength="24" id="tare" name="tare">
                    </div>
                </div>
            </div>
            <!-- /.form-group -->
            <!--Input Weight-->
            <div class="col-md-4 form-group">
                <label>Metrc Tag</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="input-group" style="width:100%">
                        <input type="text" class="form-control"  placeholder="Enter the Metrc Tag" size="24" maxlength="24" id="metrc" name="metrc">
                    </div>
                </div>
            </div>
            <!-- /.form-group -->
            <!--Input Weight-->
            <div class="col-md-2 form-group">
                <button class="btn btn-info btnAdd" id="add_row"><i class="fa fa-fw fa-plus"></i> ADD Row</button>
            </div>
            <!-- /.form-group -->
        </div>
        <div class="row col-md-12 table_panel">
            <table class="table table-bordered data-table" id="containerTable">
                <thead>
                    <th>No</th>
                    <th>Weight</th>
                    <th>Product Type</th>
                    <th>Tare Weight</th>
                    <th>Metrc Tag</th>
                    <th>Print</th>
                    <th>Remove</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <input type="hidden" name="c_id" value="{{ $curning->id}}">
        <div class="row">
            <div class="col-md-5"></div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success btn-lg makeBtn"><i class="fas fa-eraser"></i>&nbsp;Build This Package</button>
            </div>
            <div class="col-md-4"></div>
        </div>
      <!--/row-top_line-->
    </div>
</div>
<div class="barcode_panel" style="display: none">

</div>
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/loadingspin/spin.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/loadingspin/ladda.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/form_curning.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop
