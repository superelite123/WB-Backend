@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Walnut Distro - Home')

@section('content_header')

@stop
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
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
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $harvest_cnt }}</h3>

                        <p>Batches Created M.T.D</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cannabis" style="line-height:1.4";></i>
                    </div>
                    <a href="harvest/list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $plant_cnt }}</h3>

                        <p>Plants Harvested M.T.D</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-barcode"  style="line-height:1.4";></i>
                    </div>
                    <a href="harvest/list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $wetweight }}</h3>

                        <p>Wet Weight Grams M.T.D</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cannabis" style="line-height:1.4";></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $wetweightlbs }}</h3>

                        <p>Wet Weight Pounds - M.T.D</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cannabis" style="line-height:1.4";></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- /.row -->
         <!-- /.row -->
            <!-- Main row -->
            <div class="row">
              <!-- Left col -->
              <section class="col-lg-12 connectedSortable">
                <!-- Custom tabs (Charts with tabs)-->
                <div class="col-md-12">
                  <!-- LINE CHART -->
                  <div class="box box-info">
                    <div class="box-header with-border">
                      <h3 class="box-title">Harvest Chart</h3>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Harvest Period:</label>
      
                          <div class="input-group">
                              <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control pull-right" id="reservation_harvest">
                          </div>
                          <!-- /.input group -->
                        </div>
                      </div>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <div class="box-body">
                        <div id="chart_container_harvest">Harvest Chart will render here</div>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
                <div class="col-md-12">
                  <!-- LINE CHART -->
                  <div class="box box-info">
                    <div class="box-header with-border">
                      <h3 class="box-title">Strain Chart</h3>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Strain Period:</label>
      
                          <div class="input-group">
                              <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" class="form-control pull-right" id="reservation_strain">
                          </div>
                          <!-- /.input group -->
                        </div>
                      </div>
                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                      </div>
                    </div>
                    <div class="box-body">
                        <div id="chart_container_strain">Strain Chart will render here</div>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
                <!-- /.nav-tabs-custom -->
              </section>
              <!-- /.Left col -->
            </div>
            <!-- /.row (main row) -->
      
          </section>
          <!-- /.content -->
      
    </section>
          <!-- /.content -->
@stop
@include('footer')
@section('js')
        <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
        <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
        <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/component/js/Chart.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/home.js') }}"></script>
@stop

