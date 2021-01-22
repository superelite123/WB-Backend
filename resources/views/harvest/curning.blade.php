@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Curing Process')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/harvest/curning.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
    <div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    </div>

<div class="box box-success">
    <div class="box-header with-border">
      <h1>Curing Process</h1>
      <h4>Record Curing Data for Harvest, then once complete, process to Packages</h4>
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
                        <th>CSV</th>
                        <th>Barcode</th>
                        <th>Build</th>
                        <th>Waste</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th id="total_weight">Total Weight (gr)</th>
                        <th id="remain_weight">Remain Weight (gr)</th>
                        <th></th>
                        <th></th>
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
                <h4 id="error_message">weight cant be larger than weight remaining</h4>
                <button type="button" class="close" id="close_alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="col-md-12">
				<!--Quantity-->
				<div class="form-group">
                  <label for="quantity">Quantity of Assets</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <input type="number" class="form-control" id="qty" placeholder="Enter Quantity">
                    </div>
				</div>
				<!--Weight-->
                <div class="form-group">
                    <label for="weight">Weight Used</label>
                    <span id="weight_remain" style="font-color:#ff0000;font-size:16px;margin-left:20px"></span>
                    <label id="um_batch" style="color:#ff0000;margin-left:70px;"></label>
                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fas fa-balance-scale"></i>
                        </div>
                        <input type="number" class="form-control red_placeholder" id="w_remain" placeholder="Enter Weight">
                    </div>
				</div>
				<div class="form-group">
                    <label>Asset Type</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fas fa-balance-scale"></i></div>
                        <select class="form-control select2" style="width: 100%;" name="type_id" id="type_id">
                            <option value="0"></option>
                            @foreach($producttypes as $item)
                                <option value="{{ $item->producttype_id }}">{{ $item->producttype}}</option>
                            @endforeach
                        </select>
                    </div>
            	</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary saveBtn">Save changes</button>
		</div>
	</div>
	<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal_waste">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="waist_title">Process as Waste</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display:none" id="invalid_value_waist">
                    <h4 id="error_message_waist">Enter the Correct Weight</h4>
                    <button type="button" class="close" id="close_alert_waist" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <th>Type</th>
                                <th>Weight</th>
                                <th>Metrc Tag</th>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td>Process Loss</td>
                                    <td id='waste_weight'></td>
                                    <td><input type="text" class="form-control waist_metrc" id="waste_metrc"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sd pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info saveBtn" id="deduct_waist"> <i class="fas fa-arrow-right"></i> Process as Waste</button>
            </div>
        </div>
    </div>
</div>

@stop
@include('footer')
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/harvest/curning.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
@stop
