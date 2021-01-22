@extends('adminlte::page')

@section('title', 'Walnut')

	<meta name="csrf-token" content="{{ csrf_token() }}">

@section('css')
	@foreach ($css_files as $css_file)
		<link rel="stylesheet" href="{{ $css_file }}">
	@endforeach
@stop
@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    {!! $output !!}
@stop
<button style="display:none" data-toggle="modal" data-target="#modal-view" id="modal_view_btn"></button>
<div class="modal fade modal-lg" id="modal-view">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">View Asset</h4>
		</div>
		<div class="modal-body">
            <table class="table table-striped table-bordered" id="view_assets_table">
                <thead>
                    <th>No</th>
                    <th>Batch ID</th>
                    <th>Asset ID</th>
                    <th>Quantity</th>
                    <th>Weight</th>
                    <th>Asset Type</th>
                    <th>Creation Date</th>
                </thead>
                <tbody></tbody>
            </table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
		</div>
	</div>
	<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
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
                $('#view_assets_table').DataTable()
			});
        }
        
        $("body").addClass('fixed');
    
	</script>
@stop
