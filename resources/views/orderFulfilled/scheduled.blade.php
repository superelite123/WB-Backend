@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Scheduled Deliveries')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/bootstrap-datetimepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/fullcalendar/fullcalendar.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/fullcalendar/fullcalendar.print.min.css') }}" media="print">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class='box-title'>Scheduled Deliveries</h3>

        <div class="box-tools pull-right">
            <button type="button" class="toggle-expand-btn btn bg-yellow btn-sm"><i class="fa fa-expand"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body" style='margin-bottom:0px'>
        <div class="row">
            <div class="col-md-12">
            <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!--TablePart-->
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class='box-title'>Scheduled Deliveries</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <label>Period:</label>

                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation">
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="col-xs-3">
                <button class="btn btn-info pull-right"  style="margin-top:1.5em" class="export" onclick="loadRangedData(1)">This Week</button>
            </div>
            <div class="col-xs-3">
                <button class="btn btn-info pull-right"  style="margin-top:1.5em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;Export CSV</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class='table table-bordered nowrap' id='tbl-schedule'>
                    <thead>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Sales Order</th>
                        <th>Delivery Date</th>
                        <th>Transported Via</th>
                        <th>Time</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                        @forelse ($cData as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item['number'] }}</td>
                                <td>{{ $item['numberSO'] }}</td>
                                <td>{{ $item['dDate'] }}</td>
                                <td>{{ $item['deliveryer'] }}</td>
                                <td>{{ $item['time'] }}</td>
                                <td>{{ $item['cName'] }}</td>
                                <td>{{ $item['amount'] }}</td>
                                <td>
                                    <button class="btn btn-xs btn-info" onclick="onChnageDatte({{ $item['id'] }},'{{ $item['dDate'].' '.$item['time'] }}',{{ $item['deliveryerID'] }})">Delivery Date/Driver</button>
                                </td>
                                @if($item['isDelivered'] == 0)
                                <td>
                                    <button class="btn btn-xs btn-danger" onclick="onRemoveDate({{ $item['id'] }})"><i class="fas fa-trash"></i>&nbsp;Reschedule</button>
                                </td>
                                @else
                                    <td>
                                        <span style="color:MediumSeaGreen"><i class="fas fa-check"></i>&nbsp;Delivered</span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan=9 style='text-align:center'><h3>No Data</h3></td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <th colspan=3>
                            <h4>Sub Total:&nbsp;:&nbsp;<span style="color:green">${{ $total_stat['sub_total'] }}</span></h4>
                        </th>
                        <th colspan=2>
                            <h4>Discount Total:&nbsp;:&nbsp;<span style="color:green">${{ $total_stat['discount_total'] }}</span></h4>
                        </th>
                        <th colspan=2>
                            <h4>Extra Discount Total:&nbsp;:&nbsp;<span style="color:green">${{ $total_stat['e_discount_total'] }}</span></h4>
                        </th>
                        <th colspan=2>
                            <h4>Tax Total:&nbsp;:&nbsp;<span style="color:green">${{ $total_stat['tax_total'] }}</span></h4>
                        </th>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <!--/.table row-->
</div>

<!--Clicking Report Modal-->
<div class="modal fade" id='modal_time_range'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="waist_title">Select the Delivery Time</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class='col-sm-12'>
                        <div class="form-group">
                            <label for="weight">Delivery Date:</label>
                            <div class='input-group date'>
                                <input type='text' class="form-control" id='delivery_schedule' />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Delivery Assigned To:</label>
                            <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-users"></i>
                            </div>
                            <select class="form-control select2" style="width: 100%;" name="client" id="deliveries">
                                @foreach($deliveries as $delivery)
                                    <option value="{{ $delivery->id }}"> {{ $delivery->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-bottom:0px;">
                <button type="button" class="btn btn-sd pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info deliveryConfirmBtn"><i class="fas fa-arrow-right"></i>&nbsp;Confirm</button>
            </div>
        </div>
    </div>
</div>
<!--Clicking Report Modal-->
@stop
@include('footer')
<script>
  let calendarData  = {!! json_encode($cData) !!}
</script>
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/bootstrap-datetimepicker.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/fullcalendar/fullcalendar.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/scheduled.js') }}"></script>
@stop
