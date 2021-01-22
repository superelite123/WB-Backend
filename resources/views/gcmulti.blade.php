@extends('adminlte::page')

@section('title', 'Walnut-MULTI')

	<meta name="csrf-token" content="{{ csrf_token() }}">

@section('css')
	@foreach ($css_files as $css_file)
		<link rel="stylesheet" href="{{ $css_file }}">
	@endforeach
@stop
@section('content_header')
@stop

@section('content')
    {!! $output !!}
@stop

@section('js')
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
