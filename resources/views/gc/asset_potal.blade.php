@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Asset Potal')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/asset_potal.css') }}">
@stop
@section('content_header')
    <h1>Asset Potal</h1>
@stop

@section('content')
<div class="box box-blue">
    <div class="box-header">
        <h3 class="box-title">Asset Potal</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <table id="asset_group_list" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Creation Date</th>
                    <th>Group ID</th>
                    <th>AssetsCreated</th>
                    <th>Batch ID</th>
                    <th>Asset Type</th>
                    <th>Coa File</th>
                    <th>View</th>
                    <th>Print Manifest</th>
                    <th>Print Label</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                @endphp
                @foreach($asset_group_list as $asset_group)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ substr($asset_group->created_at,0,10) }}</td>
                    <td>
                        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($asset_group->group_id, 'C39')}}" alt="barcode" />
                        <p style="text-align:center;padding-right:30px;">{{ $asset_group->group_id }}</p>
                    </td>
                    <td>{{ $asset_group->assetscreated }}</td>
                    <td>{{ $asset_group->batch_id }}</td>
                    <td>{{ $asset_group->producttype }}</td>
                    <td>{{ $asset_group->coa_file }}</td>
                    <td><button class="btn btn-info" onclick="view({{ $asset_group->group_id }})">View</button></td>
                    <td><button class="btn btn-primary" onclick="print_manifest({{ $asset_group->group_id }})">Print Manifest</button></td>
                    <td><button class="btn btn-primary" onclick="print_labels({{ $asset_group->group_id }})">Print Label</button></td>
                </tr>
                @php
                    $i ++;
                @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Creation Date</th>
                    <th>Group ID</th>
                    <th>AssetsCreated</th>
                    <th>Batch ID</th>
                    <th>Asset Type</th>
                    <th>Coa File</th>
                    <th>View</th>
                    <th>Print Manifest</th>
                    <th>Print Label</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->
<button style="display:none" data-toggle="modal" data-target="#modal-default" id="modalbtn"></button>
@stop
@section('js')
    <script type="text/javascript" src="{{ asset('assets/js/CC/asset_potal.js') }}"></script>
@stop
@include('footer')
<div class="modal fade" id="modal-view">
    <div class="modal-dialog modal-lg" style="width:1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">View Asset</h4>
            </div>
            <div class="modal-body" id="modal-body">
                <div class="col-md-12 table">
                    <table class="table table-bordered table-striped fixed_header" id="view_assets_table">
                        <thead>
                            <th>No</th>
                            <th>Batch ID</th>
                            <th>Group ID</th>
                            <th>Asset ID</th>
                            <th>Weight</th>
                            <th>Asset Type</th>
                            <th>Creation Date</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!--------------------------print manifest--------------------------------->
<div class="row modal fade" id="print_labels_panel"></div>
<!--------------------------print labels----------------------------------->
<div class="row modal fade" id="print_manifest_panel">
    <div class="col-md-12" id="print_group_id"></div>
    <table class="table table-bordered table-striped fixed_header" id="print_manifest_table">
        <thead>
            <th>No</th>
            <th>Batch ID</th>
            <th>Asset ID</th>
            <th>Weight</th>
            <th>Asset Type</th>
            <th>Creation Date</th>
        </thead>
        <tbody></tbody>
    </table>
</div>