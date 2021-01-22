@extends('adminlte::page')

@section('title', 'Walnut')

	<meta name="csrf-token" content="{{ csrf_token() }}">

@section('css')
	@foreach ($css_files as $css_file)
	<link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
		<link rel="stylesheet" href="{{ $css_file }}">
	@endforeach
@stop
@section('content_header')
    <h1>Harvest Dynamics</h1>
@stop

@section('content')

{!! $output !!}
@stop

@section('js')
<script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/CC/harvestDynamics.js') }}"></script>
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
