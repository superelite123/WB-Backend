@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Dry Harvest Builder')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/harvest/list_dry.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
@if ($message = Session::get('success'))
  <div class="alert alert-success">
      <p>{{ $message }}</p>
  </div>
@endif

<div class="box box-success">
    <div class="box-header with-border">
      <h1>Dry Harvest Builder</h1>
      <h4>Convert Wet harvest weight to dry</h4>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Harvest Period:</label>

                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation">
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-info"  style="margin-top:1.6em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered" id="harvest_table">
                    <thead>
                        <th>No</th>
                        <th>Parent Harvest Batch ID</th>
                        <th>Total Weight (gr)</th>
                        <th>Remain Weight (gr)</th>
                        <th>Flower Room Location</th>
                        <th>Strain</th>
                        <th>License</th>
                        <th>Unit Of Weight</th>
                        <th>Creation Date</th>
                        <th> </th>
                        <th> </th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th id="total_weight">Total Weight (gr)</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                  </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div style="display:none" id="print_barcode_panel"></div>
<div class="modal fade" id="modal-build">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Enter Asset Info</h4>
		</div>
		<div class="modal-body">
			<div class="alert alert-danger alert-dismissible;display:none" id="invalid_value">
                <h4 id="error_message">weight can not be big number than weightremain</h4>
                <button type="button" class="close" id="close_alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="col-md-12">
                <div class="form-group">
                    <label for="weight">Remain Weight:</label>
                    <span id="weight_remain" style="font-color:#ff0000;font-size:16px;margin-right:20px"></span>
                    <label id="um_batch" ></label>
                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fas fa-balance-scale"></i>
                        </div>
                        <input type="number" class="form-control red_placeholder" id="w_remain" placeholder="Enter Weight">
                    </div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary saveBtn">Dry</button>
		</div>
	</div>
	<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
@stop
@include('footer')
@section('js')

  <script type="text/javascript" src="{{ asset('assets/js/harvest/list_dry.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
@stop
