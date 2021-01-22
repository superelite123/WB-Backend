@extends('adminlte::page')

@section('title', 'Walnut')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('css')
	@foreach ($css_files as $css_file)
		<link rel="stylesheet" href="{{ $css_file }}">
	@endforeach
	<style>
	.red_placeholder::-webkit-input-placeholder {
		color: #ff0000
	}
	</style>
@stop

@section('content_header')
@stop

@section('content')
{!! $output !!}
	
@stop
<div class="modal fade" id="modal-default">
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
			</div>
			<div class="col-md-12">
				<!--Quantity-->
				<div class="form-group">
                  <label for="quantity">Quantity of Assets</label>
                  <input type="number" class="form-control" id="qty" placeholder="Enter Quantity">
				</div>
				<!--Weight-->
                <div class="form-group">
				  <label for="weight">Weight Used</label>
				  <span id="weight_remain" style="font-color:#ff0000;font-size:16px;margin-left:20px"></span>
				  <label id="um_batch" style="color:#ff0000;margin-left:70px;"></label>
                  <input type="number" class="form-control red_placeholder" id="w_remain" placeholder="Enter Weight">
				</div>
				<div class="form-group">
					<input type = "checkbox" id = "send_fg" />
					<label for = "send_fg">Send To Finished Goods</label>
				</div>
				<div class="form-group">
					<label>Asset Type</label>
					<select class="form-control select2" style="width: 100%;" name="type_id" id="type_id">
						<option value="0"></option>
						@foreach($producttypes as $item)
							<option value="{{ $item->producttype_id }}">{{ $item->producttype}}</option>
						@endforeach
					</select>
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
<button type="button" id="show_modal" style="display:none" class="btn btn-default" data-toggle="modal" data-target="#modal-default"></button>
@section('js')
	<script type="text/javascript" src="{{ asset('assets/js/CC/batch.js') }}"></script>
	@foreach ($js_files as $js_file)
    	<script src="{{ $js_file }}"></script>
	@endforeach
	<script>
		if (typeof $ !== 'undefined') {
			$(document).ready(function () {
				$.ajaxSetup({
				    headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    }
				});
			});
		}
	$("body").addClass('fixed');
	</script>
@stop
