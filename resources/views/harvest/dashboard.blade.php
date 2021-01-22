@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = 'Harvest Dashboard'
@endphp
@section('title', $title)
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
<!--box-->
<div class="box box-info dashboard">
    <!--box header-->
    <div class="box-header with-border">
        <h3 class="box-title">Harvest Dashboard</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!--/box header-->
    <!--box body-->
    {{ Form::open(array('url' => 'harvest/dashboard')) }}
    <div class="box-body">
        <div class="row topbar">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control" name="batch_id" value="{{ $batch_id }}" placeholder="Please enter the Harvest Batch ID" >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-info btn-flat"> <i class="fas fa-arrow-right"></i> Go</button>
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <button type="button" id="btnPrint" class="btn btn-info btn-lg"><i class="fas fa-print"></i>Print</button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <hr class="first">
                </div>
            </div>
        </div>
        @if ($data == 'first')
            <h1><i class="fas fa-search" style="color:#00c0ef"></i> Harvest Batch ID Snapshot</h1>
        @elseif ($data != null)
        <div id="DivToPrint">
        <div class="row detailRow">
            <div class="col-md-12">
                <h4 class="batchId">Batch ID = {{ $data['harvest']['harvest_batch_id'] }}</h4>
            </div>
        </div>
        <!--Start 1.Created At-->
        <hr style="width:100%";>
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">Newly Harvested: {{ $data['harvest']->created_at->format('Y-m-d H:i:s') }}</h4>
            </div>
            <div class="col-md-12">
                <table class="table table-responsive table-stripe table-bordered">
                    <thead style="font-size: 10px;">
                        <th>No</th>
                        <th>Plant Tag</th>
                        <th>Total Weight</th>
                    </thead>
                    <tbody style="font-size: 10px;">
                        @php
                            $cnt = 1;
                            $total_wet_weight = 0;
                        @endphp
                        @foreach ($data['harvest_items'] as $item)
                            <tr>
                                <td>{{ $cnt }}</td>
                                <td> {{ $item->plant_tag }} </td>
                                <td> {{ $item['weight'] }} </td>
                            </tr>
                            @php
                                $cnt ++;
                                $total_wet_weight += $item->weight;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <td></td>
                        <td>Total Plants Count:{{ count( $data['harvest_items'] ) }}</td>
                        <td>Total Weight:{{ $total_wet_weight }}</td>
                    </tfoot>
                </table>
            </div>
        </div>
        <!--/End 1.Created At-->
        <!--Start 2.Harvest Dynamics-->
        @if ($data['dynamics'] != null)
            <div class="row detailRow">
                <div class="col-md-4" style="text-align:left">
                    <h4 class="batchId">Harvest Dynamics: {{ $data['dynamics']['created_at']->format('Y-m-d H:i:s') }}</h4>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered">
                       <thead style="font-size: 10px;">
                            <th>Total Weight</th>
                            <th>Unit of Weight</th>
                            <th>Strain</th>
                            <th>Flower Room Location</th>
                            <th>Cultivator license id</th>
                            <th>Trimroom Water</th>
                            <th>Dryroom Water</th>
                        </thead>
                        <tbody style="font-size: 10px;">
                            <tr>
                                <td>{{ $data['dynamics']['total_weight'] == ''?'Empty':$data['dynamics']['total_weight'] }}</td>
                                <td>{{ $data['dynamics']['UnitOfWeight']['name'].'-'.$data['dynamics']['UnitOfWeight']['abbriviation'] }}</td>
                                <td>{{ $data['dynamics']['Strain']['strain'] }}</td>
                                <td>{{ $data['dynamics']['Room'] == ''?'Empty':$data['dynamics']['Room']['location_id'].'-'.$data['dynamics']['Room']['name'] }}</td>
                                <td>{{ $data['dynamics']['License'] == ''?'Empty':$data['dynamics']['License']['companyname'].'-'.$data['dynamics']['License']['license'] }}</td>
                                <td>{{ $data['dynamics']['trimroom_h2o'] == ''?'Empty':$data['dynamics']['trimroom_h2o'] }}</td>
                                <td>{{ $data['dynamics']['dryroom_h2o'] == ''?'Empty':$data['dynamics']['dryroom_h2o'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead style="font-size: 10px;">
                            <th>Day1</th>
                            <th>Day2</th>
                            <th>Day3</th>
                            <th>Day4</th>
                            <th>Day5</th>
                            <th>Day6</th>
                            <th>Day7</th>
                            <th>Day8</th>
                            <th>Day9</th>
                            <th>Day10</th>
                            <th>Day 11 Buffer</th>
                            <th>Day 12 Buffer</th>
                        </thead>
                        <tbody style="font-size: 10px;">
                            <tr>
                                <td>{{ $data['dynamics']['day1'] == ''?'Empty':$data['dynamics']['day1'] }}</td>
                                <td>{{ $data['dynamics']['day2'] == ''?'Empty':$data['dynamics']['day2'] }}</td>
                                <td>{{ $data['dynamics']['day3'] == ''?'Empty':$data['dynamics']['day3'] }}</td>
                                <td>{{ $data['dynamics']['day4'] == ''?'Empty':$data['dynamics']['day4'] }}</td>
                                <td>{{ $data['dynamics']['day5'] == ''?'Empty':$data['dynamics']['day5'] }}</td>
                                <td>{{ $data['dynamics']['day6'] == ''?'Empty':$data['dynamics']['day6'] }}</td>
                                <td>{{ $data['dynamics']['day7'] == ''?'Empty':$data['dynamics']['day7'] }}</td>
                                <td>{{ $data['dynamics']['day8'] == ''?'Empty':$data['dynamics']['day8'] }}</td>
                                <td>{{ $data['dynamics']['day9'] == ''?'Empty':$data['dynamics']['day9'] }}</td>
                                <td>{{ $data['dynamics']['day10'] == ''?'Empty':$data['dynamics']['day10'] }}</td>
                                <td>{{ $data['dynamics']['day_11_buffer'] == ''?'Empty':$data['dynamics']['day_11_buffer'] }}</td>
                                <td>{{ $data['dynamics']['day_12_buffer'] == ''?'Empty':$data['dynamics']['day_12_buffer'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="row detailRow">
                <div class="col-md-4" style="text-align:left">
                    <h4 class="batchId">Harvest Dynamics</h4>
                </div>
                <div class="col-md-12">
                    <h3>Harvest Dynamics Not Recorded</h3>
                </div>
            </div>
        @endif
        
        <!--/End 2.Harvest Dynamics-->
        <!--Start Weight Deduction-->
        <div class="row detailRow">
            <div class="col-md-12"></div>
        </div>
        <!--End Weight Deduction-->
        <!--Start 3.Dry Weight List-->
        @if ($data['dry'] != null)
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">Dry Harvest: {{ date('Y-m-d H:i:s',$data['dry']->created_at->timestamp) }}</h4>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead style="font-size: 10px;">
                        <th>Total Weight</th>
                        <th>Remain Weight</th>
                        <th>Unit Of Weight</th>
                        <th>Strain</th>
                        <th>Flower Room Location</th>
                        <th>Cultivator license id</th>
                    </thead>
                    <tbody style="font-size: 10px;">
                        <tr>
                            <td>{{ $data['dry']['total_weight'] == ''?'Empty':$data['dry']['total_weight'] }}</td>
                            <td>{{ $data['dry']['remain_weight'] == ''?'Empty':$data['dry']['remain_weight'] }}</td>
                            <td>{{ $data['dry']['unit_weight'] == ''?'Empty':$data['dry']['UnitOfWeight']['name'].'-'.$data['dry']['UnitOfWeight']['abbriviation'] }}</td>
                            <td>{{ $data['dry']['strain_id'] == ''?'Empty':$data['dry']['Strain']['strain'] }}</td>
                            <td>{{ $data['dry']['Room'] == ''?'Empty':$data['dry']['Room']['location_id'].'-'.$data['dry']['Room']['name'] }}</td>
                            <td>{{ $data['dry']['License'] == ''?'Empty':$data['dry']['License']['companyname'].'-'.$data['dry']['License']['license'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">Dry Harvest</h4>
            </div>
            <div class="col-md-12">
                <h3>Dry weight data not recorded</h3>
            </div>
        </div>
        @endif
        <!--/End 3.Dry Weight List-->
        <!--Start 4.Curing-->
        @if ($data['curning'] != null)
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">Curing Stage</h4>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead style="font-size: 10px;">
                        <th>No</th>
                        <th>Total Weight</th>
                        <th>Remain Weight</th>
                        <th>Status</th>
                        <th>Creation Date</th>
                    </thead>
                    <tbody style="font-size: 10px;">
                        @php
                            $cnt = 1;
                        @endphp
                        @forelse ($data['curning'] as $item)
                        <tr>
                            <td>{{ $cnt }}</td>
                            <td>{{ $item->total_weight }}</td>
                            <td>{{ $item->remain_weight }}</td>
                            <td class="{{ $item->archived == 1?'archived':'pending' }}">{{ $item->archived == 1?'archived':'pending' }}</td>
                            <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @php
                            $cnt ++;
                        @endphp
                        @empty
                            <tr><td>There is no Curing Data</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @foreach ($data['curning'] as $item)
                <table class="table table-bordered">
                    <thead style="font-size: 10px;">
                        <th>No</th>
                        <th>Weight</th>
                        <th>Metrc Tag</th>
                    </thead>
                    <tbody style="font-size: 10px;">
                        @php
                            $cnt = 1;
                        @endphp
                        @foreach ($item->asset as $item1)
                        <tr>
                            <td>{{ $cnt }}</td>
                            <td>{{ $item1->weight }}</td>
                            <td>{{ $item1->metrc }}</td>
                        </tr>
                        @php
                            $cnt ++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
                @endforeach
            </div>
        </div>
        @else 
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">4.Curing Harvest</h>
            </div>
            <div class="col-md-12">
                <h3>There is no Curing Data recorded</h3>
            </div>
        </div>
        @endif
        <!--/End 4.Curing-->
        <!--Start 5.Holding Inventory-->
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">Holding Inventory</h4>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead style="font-size: 10px;">
                        <th>No</th>
                        <th>StockImage</th>
                        <th>Strain</th>
                        <th>Asset Type</th>
                        <th>UPC</th>
                        <th>COA</th>
                        <th>Qty on Hand</th>
                        <th>Weight</th>
                        <th>Unit Of Weight</th>
                        <th>Creation Date</th>
                        <th>Status</th>
                    </thead>
                    <tbody style="font-size: 10px;">
                        @php
                            $cnt = 1;
                        @endphp
                        @forelse($data['holding'] as $item)
                                <tr>
                                    <td>{{ $cnt }}</td>
                                    <td> 
                                        @if($item['stockimage'] != '')
                                            <img class="stockimg" src="{{ url('/assets/upload/files/inv/').'/'.$item['stockimage'] }}" alt=""></td>
                                        @else
                                            <img class="stockimg1" src='{{ url("/assets/noimage.png") }}' alt=""></td>
                                        @endif
                                    <td>{{ $item['Strain']['strain'] }}</td>
                                    <td>{{ $item['AssetType']['producttype'] }}</td>
                                    <td>
                                        @if ($item['UPC'] != null)
                                            {{ $item['UPC']['upc'].'-'.$item['UPC']['strain'].'_'.$item['UPC']['type'] }}
                                        @else
                                            Empty
                                        @endif
                                        
                                    </td>
                                    
                                    <td> 
                                        @if($item['stockimage'] != '')
                                            <a href="{{ asset('assets/upload/files/coa/').'/'.$item['coa'] }}">{{ $item['coa'] }}</a>
                                        @else
                                            No Coa
                                        @endif
                                        
                                    </td>
                                    <td>{{ $item['qtyonhand'] }}</td>
                                    <td>{{ $item['weight'] }}</td>
                                    <td>{{ $item['UnitOfWeight']['name'] }}</td>
                                    <td>{{ $item['created_at']->format('Y-m-d H:i:s') }}</td>
                                    <td class="{{ $item->archived == 1?'archived':'pending' }}">{{ $item->archived == 1?'archived':'pending' }}</td>
                                </tr>
                                @php
                                    $cnt ++;
                                @endphp
                        @empty
                        <tr>
                            <td><h3 style="text-align:center">There is no Holding Inventory</h3></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
        <!--/End 5.Holding Inventory-->
        <!--Start 6.FG Inventory-->
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">Finished Good</h4>
            </div>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead style="font-size: 10px;">
                        <th>No</th>
                        <th>StockImage</th>
                        <th>Strain</th>
                        <th>Asset Type</th>
                        <th>UPC</th>
                        <th>COA</th>
                        <th>Qty on Hand</th>
                        <th>Weight</th>
                        <th>Unit Of Weight</th>
                        <th>Creation Date</th>
                    </thead>
                    <tbody style="font-size: 10px;">
                        @php
                            $cnt = 1;
                        @endphp
                        @forelse ($data['fg'] as $item)
                                <tr>
                                    <td>{{ $cnt }}</td>
                                    <td> 
                                        @if($item['stockimage'] != '')
                                            <img class="stockimg" src="{{ url('/assets/upload/files/inv/').'/'.$item['stockimage'] }}" alt=""></td>
                                        @else
                                            <img class="stockimg1" src='{{ url("/assets/noimage.png") }}' alt=""></td>
                                        @endif
                                    <td>{{ $item['Strain']['strain'] }}</td>
                                    <td>{{ $item['AssetType']['producttype'] }}</td>
                                    <td>
                                        @if ($item['UPC'] != null)
                                            {{ $item['UPC']['upc'].'-'.$item['UPC']['strain'].'_'.$item['UPC']['type'] }}
                                        @else
                                            Empty
                                        @endif
                                        
                                    </td>
                                    
                                    <td> 
                                        @if($item['stockimage'] != '')
                                            <a href="{{ asset('assets/upload/files/coa/').'/'.$item['coa'] }}">{{ $item['coa'] }}</a>
                                        @else
                                            No Coa
                                        @endif
                                        
                                    </td>
                                    <td>{{ $item['qtyonhand'] }}</td>
                                    <td>{{ $item['weight'] }}</td>
                                    <td>{{ $item['UnitOfWeight']['name'] }}</td>
                                    <td>{{ $item['created_at']->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @php
                                    $cnt ++;
                                @endphp
                        @empty
                        <tr>
                            <td><h3 style="text-align:center">There are no Finished Goods recordered</h3></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!--/End 6.FG Inventory-->
        <!--7.Temp Invoice-->
        <div class="row detailRow">
            <div class="col-md-4" style="text-align:left">
                <h4 class="batchId">Pending On Invoice</h4>
            </div>
            <div class="col-md-12">
            @forelse ($data['invoice_items'] as $item)
                <div class="col-md-4">
                    Customer Name:{{ $item['info']['clientName'] }}
                </div>
                <div class="col-md-4">
                    Invoice Number:{{ $item['info']['number'] }}
                </div>
                <div class="col-md-4">
                    Invoice Date:{{ $item['info']['idate'] }}
                </div>
                <table class="table table-bordered">
                    <thead style="font-size: 10px;">
                        <th>No</th>
                        <th>Finished Good</th>
                        <th>Qty</th>
                    </thead>
                    <tbody style="font-size: 10px;">
                        @php
                            $cnt = 1;
                        @endphp
                        @foreach ($item['items'] as $subItem)
                            <tr>
                                <td>{{ $cnt }}</td>
                                <td>{{ $subItem->producttype }}</td>
                                <td>{{ $subItem->qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @empty
            <tr>
            <td><h3 style="text-align:center">There are no Items pending on invoice</h3></td>
            </tr>
            @endforelse
            </div>
        </div>
        <!--/7.Temp Invoice-->
        </div>
        @else
            <h1><i class="fas fa-exclamation-triangle" style="color:#00c0ef"></i> No Harvest exists with this ID</h1>
        @endif
    </div>
<!--/box body-->
    {{ Form::close() }}
</div>
<!--/box-->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">              
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <img src="" class="imagepreview" style="width: 100%;" >
        </div>
        </div>
    </div>
</div>
@stop
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/dashboard.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop