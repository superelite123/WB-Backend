@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Metrc Search')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
<style>
    hr.first{
        position: relative;
        top: 0px;
        border: none;
        height: 3px;
        background: #e2e2e2;
    }
    .alert {
        padding: 20px;
        background-color: #f44336;
        color: white;
    }

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }
</style>
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
      <h1>Metrc Search</h1>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="box-body">
            <!--Metrc Input Wrapper-->
            <div class="row">
                <div class="col-md-12">
                    @error('metrc')
                    <div class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="row">
                {{ Form::open(array('url' => 'metrc_search')) }}
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <input
                                    type="text"
                                    class="form-control"
                                    name="metrc"
                                    placeholder="Please enter the Metrc Tag"
                                    value="{{ isset($metrc)?$metrc:'' }}" >
                                <span class="input-group-btn">
                                    <button type="submit" id="btnSearch" class="btn btn-info btn-flat"> <i class="fas fa-arrow-right"></i> Go</button>
                                </span>
                            </div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
            <!--./Metrc Input Wrapper-->
            <div class="row">
                <div class="col-md-12">
                    <hr class="first">
                </div>
            </div>
            @if(isset($result))
            <div class='row result'>
                @if($result['label'] != '')
                <div class="col-md-12">
                    <label for="">{{ $result['label'] }}</label>
                    <a class='btn btn-info' target="_blank" href="{{ $result['link'] }}">Go</a>
                </div>
                @else
                <div class="col-md-12">
                    <label for="">No Result</label>
                </div>
                @endif
            </div>
            @endif
        </div>
        <!-- /.box-body -->
    </div>
</div>
@stop
@include('footer')
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/metrcSearch/index.js') }}"></script>
@stop
