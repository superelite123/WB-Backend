@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Dashboard')

@section('css')
  <style>
    .content-header>.breadcrumb {
    float: right;
    background: 0 0;
    margin-top: 0;
    margin-bottom: 0;
    font-size: 12px;
    padding: 7px 5px;
    position: absolute;
    top: 0px;
    right: 10px;
    border-radius: 2px;
}
  </style>
@stop
@section('content')
   <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <small>{{config('company.COMPANY_NAME')}}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <form action="{{ route('admin.order.find') }}">
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <th>Metrc Tag</th>
                        <th>qty</th>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $item)
                        <tr>
                            <td>{{ $item->metrc_tag }}</td>
                            <td>{{ $item->qtyonhand }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            @if (count($item->cItems) > 0)
                                    
                                <table class="table">
                                    <thead>
                                        <th>Metrc Tag</th>
                                        <th>Qty</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->cItems as $cItem)
                                        <tr>
                                            <td>{{ $cItem->metrc_tag }}</td>    
                                            <td>{{ $cItem->qtyonhand }}</td>    
                                        </tr>    
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </form>
    </section>
    <!-- /.content -->
@stop
@section('js')
@stop

