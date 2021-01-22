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
    <h1>Harvest Tracker Builder</h1>
@stop

@section('content')
{!! $output !!}
	
@stop
<div style="display:none" id="print_barcode_panel"></div>
@section('js')
<script type="text/javascript" src="{{ asset('assets/js/CC/harvest_tracker_builder.js') }}"></script>
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
