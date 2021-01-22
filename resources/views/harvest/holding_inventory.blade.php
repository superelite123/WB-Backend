@extends('adminlte::page')

@section('title', 'Walnut')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('css')
	@foreach ($css_files as $css_file)
		<link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
		<link rel="stylesheet" href="{{ $css_file }}">
	@endforeach
	<style>
	.red_placeholder::-webkit-input-placeholder {
		color: #ff0000
	}
	</style>
@stop

@section('content_header')
    <h1>Inventory in Holding Pattern</h1>
    <h4>Not available to move to Finished goods or Inventory 1 until COA, stock image are allocated.</h4>
@stop

@section('content')
{!! $output !!}
	
@stop
<div class="modal fade" id="modal-default">
	<div class="modal-dialog">
		<div class="modal-content">
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">What do you want?</h4>
			</div>
		
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-3">
						<button type="button" class="btn btn-danger btn-lg" data-dismiss="modal" id="btnSendNo">
							<i class="fa fa-close"></i>  Close
						</button>
						</div>
						<div class="col-md-3">
						<button type="button" class="btn btn-success btn-lg"  id="btnSendOk">
							<i class="fa fa-upload"></i>  SEND to Finished Goods Inventory
						</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<button type="button" id="show_modal" style="display:none" class="btn btn-default" data-toggle="modal" data-target="#modal-default"></button>
@section('js')
	<script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/harvest/holdinginventory.js') }}"></script>
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
