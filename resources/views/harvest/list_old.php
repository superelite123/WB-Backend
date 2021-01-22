@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Harvest Allocations')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/harvest/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))

    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
@endforeach
<div class="box box-success">
    <div class="box-header with-border">
      <h1>Harvest Allocation and Distribution</h1>
      <h4>Non Archived</h4>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Harvest Period:</label>

                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation">
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success" style="margin-top:1.5em" id="filter">View Filtered Harvest History</button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-info btn-sm"  style="margin-top:1.5em" id="export_btncomp" class="export"><i class="fa fa-download"></i>&nbsp;CSV Compliance</button>
                <button class="btn btn-success btn-sm"  style="margin-top:1.5em" id="export_btn" class="export"><i class="fa fa-download"></i>&nbsp;CSV Manager</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered" id="harvest_table">
                    <thead>
                        <th></th>
                        <th>No</th>
                        <th>Harvest Batch ID..</th>
                        <th>Total Plant Count</th>
                        <th>Total Wet Weight (gr)</th>
                        <th>Total Wet Weight (lbs)</th>
                        <th>Flower Room Location</th>
                        <th>Strain</th>
                        <th>License</th>
                        <th>Unit Of Weight</th>
                        <th>Creation Date</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        @if ($perm == 'admin')
                            
                            <th>Edit</th>
                            <th>Delete</th>
                        @endif
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th id="total_count">Total Plant Count</th>
                        <th id="total_weight">Total Wet Weight (gr)</th>
                        <th id="total_pounds_wet">Total Wet Weight (lbs)</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        @if ($perm == 'admin')
                            
                            <th></th>
                            <th></th>
                        @endif
                  </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
<div class="modal fade" id="modal_fresh">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Fresh Info</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display:none" id="invalid_value">
                    <h4 id="error_message">Enter the Correct Weight</h4>
                    <button type="button" class="close" id="close_alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="form-group">
                    <label for="weight">Number of Package:</label>
                    <label for="weight">Total Weight:</label>
                    <span id="total_weight" style="font-color:#ff0000;font-size:16px;margin-right:20px"></span>
                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fas fa-balance-scale"></i>
                        </div>
                        <input type="number" class="form-control red_placeholder" id="fresh_weight" placeholder="Enter Number of Package">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sd pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info saveBtn"> <i class="fas fa-arrow-right"></i> Go to Fresh Page</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_waist">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="waist_title">Waste Matrix Builder</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible" style="display:none" id="invalid_value_waist">
                    <h4 id="error_message_waist">Enter the Correct Weight</h4>
                    <button type="button" class="close" id="close_alert_waist" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4 id="waist_total_weight">Total Havested Wet Weight:</h4>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <th>No</th>
                                <th>Waste Type</th>
                                <th>Weight</th>
                                <th>Metrc Tag</th>
                            </thead>
                            <tbody>
                                @php
                                    $cnt = 1;
                                @endphp
                                @foreach ($waist_type_list as $item)
                                     <tr>
                                        <td>{{ $cnt }}</td>
                                        <td>{{ $item->label }}</td>
                                        <td><input type="number" class="form-control waist_weight" id="w_{{ $item->id }}"></td>
                                        <td><input type="text" class="form-control waist_metrc" id="m_{{ $item->id }}"></td>
                                    </tr>
                                    @php
                                        $cnt ++
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sd pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info saveBtn" id="deduct_waist"> <i class="fas fa-arrow-right"></i> Process Weight</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id='modal_barcode_question'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="waist_title">Processed Quantity</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="weight">How many Items:</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                        <i class="fas fa-balance-scale"></i>
                        </div>
                        <input type="number" class="form-control red_placeholder" id="question" placeholder="Number of items processing?">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sd pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info questionBtn"> <i class="fas fa-arrow-right"></i> Confirm</button>
            </div>
        </div>
    </div>
</div>
<div style="display:none" id="print_barcode_panel"></div>
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/list.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table2csv.js') }}"></script>
@stop
