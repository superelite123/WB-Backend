@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Sales Report')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
    <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script type="text/javascript" src="//cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
    <style>
        .nav-tabs > li > a{
            cursor: pointer;
        }
    </style>
@stop
<?php
    $Chart1 = new \App\Library\FusionCharts("pie3d", "chart-1" , "100%", "500", "chart-container1", "json", $chartData);
    $Chart2 = new \App\Library\FusionCharts("column2d", "ex1", "100%", "500", "chart-container2", "json", $chartData);
?>
@section('content')

    <!-- Main content -->
    <section class="content">
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
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li <?php echo $mode == 1?'class="active"':"";?> >
                            <a href='#' onclick="javascript:switchTab(1)" >Monthly Sales</a>
                        </li>
                        <li <?php echo $mode == 2?'class="active"':"";?> >
                            <a href='#' onclick="javascript:switchTab(2)" >Top Sales Rep</a>
                        </li>
                        <li <?php echo $mode == 3?'class="active"':"";?> >
                            <a href='#' onclick="javascript:switchTab(3)" >Top Customer</a>
                        </li>
                        <li <?php echo $mode == 4?'class="active"':"";?> >
                            <a href='#' onclick="javascript:switchTab(4)" >Top Returned Inventory</a>
                        </li>
                        <li <?php echo $mode == 5?'class="active"':"";?> >
                            <a href='#' onclick="javascript:switchTab(5)" >Most Returned Item by Sales Rep</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                {{ $Chart1->render() }}
                <div id="chart-container1">Chart will render here!</div>
            </div>
            <div class="col-md-12">
                {{ $Chart2->render() }}
                <div id="chart-container2">Chart will render here!</div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop
@include('footer')
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        let sDate = "{{ $date[0] }}"
        let eDate = "{{ $date[1] }}"
        let mode = "{{ $mode }}"
        $(() => {
            $("#reservation").daterangepicker({
                startDate: "{{ date('m/d/Y', strtotime($date[0])) }}",
                endDate: "{{ date('m/d/Y', strtotime($date[1])) }}",
                showDropdowns: true,
                locale: {
                    format: 'M/D/Y'
                }
            }).on('apply.daterangepicker', function(ev, picker) {
                sDate = picker.startDate.format('YYYY-MM-DD');
                eDate = picker.endDate.format('YYYY-MM-DD');
                switchTab()
            });
            $('body').addClass('fixed')
        })
        
        const switchTab = (selectedMode = 0)=>{
            if(selectedMode == 0)
            {
                selectedMode = mode
            }
            location.href = "{{ route('sreport.month') }}?sdate=" + sDate + '&edate=' + eDate + '&mode=' + selectedMode
        }
    </script>
@stop

