@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $title = 'Room Builder'
@endphp
@section('title', $title)
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/harvest/form_room_builder.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content')
<div class="flash-message">
    <!--start edit form-->
    <div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    </div>
</div>
<div class="row" style="margin-bottom:30px">
    <div class="col-md-12">
        <a href="{{ url('harvest/room_builder') }}" class='btn btn-info pull-right'><i class="fas fa-hand-point-left"></i>&nbsp;back to list</a>
    </div>
</div>
<!--box-->
<div class="box box-info dashboard">
    <!--box header-->
    <div class="box-header with-border">
    <h3 class="box-title">Room Builder:{{date('Y-m-d')}}</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!--/box header-->
    <!--box body-->
    <div class="box-body">
        <div class="row top-line batch_info" style="margin-bottom:30px">
            <div class="col-md-2">
                <label>Room Name:&nbsp;</label>
                <span class="sp_batchId">{{ $room_name }}</span>
            </div>
            <div class="col-md-2">
                <label>User:&nbsp;</label>
                <span class="sp_strain">{{ auth()->user()->name }}</span>
            </div>
            <div class="col-md-3">
                <label>Matrix Type:&nbsp;</label>
                <span>{{ '4*'.$matrix_col }}</span>
            </div>
        </div>
        <form action="{{ 'store_room_builder' }}" method="post" id="myForm">
        {{ csrf_field() }}
        <div class="row col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Table1</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Table2</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Table3</a></li>
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                </ul>
                <div class="tab-content">
                    @php  
                    $col = floor(12/$matrix_col);
                    $cnt = 0;
                    $cnt1 = 0;
                    @endphp
                    @foreach ($tables as $table)
                        <div class="tab-pane {{ $cnt+1 == 1?'active':'' }}" id="tab_{{$cnt+1}}">
                            <div class='row'>
                                <div class='col-md-12'>
                                <table class='table table-striped table-bordered' id="form_table_{{ $cnt+1 }}" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>5</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $line_cnt = 1    
                                        @endphp
                                        @for ($i = 0; $i < 125; $i++)
                                            <tr class='item_row'>    
                                            <td>{{ $line_cnt }}</td>
                                            @for ($j = 0; $j < 4 ; $j++,$cnt1 ++)
                                                <td class='tab-pane'>
                                                <input name="plants[{{$cnt}}][]" maxlength="24" value="{{ isset($table[$cnt1])?$table[$cnt1]:'' }}" class="form-control metrc" type="text" placeholder="Plant Tag">
                                                </td>
                                            @endfor
                                            <td>
                                            </td>
                                            </tr>
                                            @php
                                                $line_cnt ++;
                                            @endphp
                                            <tr class='item_row'>
                                            <td>{{ $line_cnt }}</td>
                                            
                                            @for ($j = 0; $j < $matrix_col; $j++,$cnt1 ++)
                                                <td class='tab-pane'>
                                                <input name="plants[{{$cnt}}][]" maxlength="24" value="{{ isset($table[$cnt1])?$table[$cnt1]:'' }}" class="form-control metrc" type="text" placeholder="Plant Tag">
                                                </td>
                                            @endfor
                                            @for ($j = 0; $j < 5-$matrix_col; $j++)
                                                <td>
                                                </td>
                                            @endfor
                                            @php
                                                $line_cnt ++;
                                            @endphp
                                            </div>
                                        @endfor
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                        @php
                            $cnt ++;
                            $cnt1 = 0;
                        @endphp
                    @endforeach
                </div>
            </div>
        </div>
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" name="room_id" value="{{$room_id}}">
        <input type="hidden" name="matrix_col" value="{{$matrix_col}}">
        </form>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-5"></div>
                <div class="col-md-2">
                    <button class="btn btn-success btn-lg makeBtn"><i class="fas fa-eraser"></i>&nbsp;Build</button>
                </div>
                <div class="col-md-5"></div>
            </div>
        </div>
        
    </div>
</div>
<div id="snackbar">Has Been Saved.</div> 
<audio id="audio" src="{{ asset('assets/record_duplicate.mp3') }}" autostart="false" type="audio/mpeg"   ></audio>
<audio id="audio_24" src="{{ asset('assets/24digitwarning.mp3') }}" autostart="false" type="audio/mpeg"   ></audio>
@stop
@include('footer')
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/form_room_builder.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/harvest/table-edits.min.js') }}"></script>
@stop