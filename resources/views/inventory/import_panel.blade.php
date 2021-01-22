@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Walnut to Deliver')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/inventory/import.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/dropzone/normalize.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/dropzone/component.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
@stop
@section('content_header')
@stop

@section('content')
    <div class="box box-info">
        <div class="box-header with-border">
        <h1>Inventory Import</h1>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
        </div>
        <!-- /.box-header -->

        <div class="box-body">
            <form action="importInventory" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="row">
                <div class="col-md-12 file-upload-content">
                    <input type="file" accept=".csv" name="inventoryFile" id="file-1" class="inputfile inputfile-1" />
                    <label for="file-1"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span>Choose a file&hellip;</span></label>
                </div>
                <div class="col-md-12" style='text-align:center;'>
                    <button id="df" class="btn btn-info btn-lg"><i class="fas fa-save"></i>&nbsp;&nbsp;&nbsp;Import</button>
                </div>
            </div>
            </form>
        </div>
        <!-- /.box-body -->
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
          <h1>Inventory Bulk Import</h1>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <!-- /.box-header -->

        <div class="box-body">
            <form action="bulk_import_confirm" method="POST">
              @csrf
              <div class="row">
                <!-- metrc tag -->
                <div class="col-md-4">
                    <div class="form-group">
                      <label>First Metrc Tag:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <input type="text" class="form-control" id="metrc" name="metrc" value="{{ old('metrc') }}" placeholder="Enter Metrc Tag">
                      </div>
                      <!-- /.input group -->
                      <span class="error"><p style='color:red'><?php if ($errors->has('metrc')) echo $errors->first('metrc');?></p></span>
                    </div>
                    <!-- /.form-group -->
                </div>

                <!-- count -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Count:</label>
                        <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <input type="text" class="form-control" id="count" name="count"  value="{{ old('count',1) }}">
                        </div>
                        <!-- /.input group -->
                        <span class="error">
                          <?php 
                            if ($errors->has('count')) 
                              foreach ($errors->get('count') as $message) 
                              {
                          ?>
                          <p style='color:red'>
                              {{ $message }}
                          </p>
                          <?php
                              }
                          ?>
                      </span>
                    </div>
                    <!-- /.form-group -->
                </div>

                <!-- Inventory -->
                <div class="col-md-3">
                  <div class="form-group">
                      <label>Inventory</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-sliders-h"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" name="i_type" id="i_type">
                          <option value="0" selected>---Select Where to save---</option>
                          <option value="2" <?php if(old('i_type') == 2) echo 'selected';?>>Inv 1</option>
                          <option value="1" <?php if(old('i_type') == 1) echo 'selected';?>>Inv 2</option>
                        </select>
                      </div>
                      <!-- /.input group -->
                      <span class="error"><p style='color:red'><?php if ($errors->has('i_type')) echo $errors->first('i_type');?></p></span>
                  </div>
                </div>

                <!-- Blank col-md-12 -->
                <div class="col-md-12"></div>

                <!-- strains -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Strain</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fas fa-sliders-h"></i>
                          </div>
                          <select class="form-control select2" style="width: 100%;" name="strain" id="strain">
                            <option value="0" selected>---Select Strain---</option>
                            @foreach($strains as $strain)
                              <option value="{{ $strain->itemname_id }}" <?php if(old('strain') == $strain->itemname_id) echo 'selected';?>>{{ $strain->strain }}</option>
                            @endforeach
                          </select>
                        </div>
                        <!-- /.input group -->
                        <span class="error"><p style='color:red'><?php if ($errors->has('strain')) echo $errors->first('strain');?></p></span>
                    </div>
                </div>

                <!-- p_type -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Product Type</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fas fa-sliders-h"></i>
                          </div>
                          <select class="form-control select2" style="width: 100%;" name="p_type" id="p_type">
                            <option value="0" selected>---Select Product Type---</option>
                            @foreach($p_types as $p_type)
                              <option value="{{ $p_type->producttype_id }}" <?php if(old('p_type') == $p_type->producttype_id) echo 'selected';?>>{{ $p_type->producttype }}</option>
                            @endforeach
                          </select>
                        </div>
                        <!-- /.input group -->
                        <span class="error"><p style='color:red'><?php if ($errors->has('p_type')) echo $errors->first('p_type');?></p></span>
                    </div>
                </div>

                <!-- weight -->
                <div class="col-md-3">
                    <div class="form-group">
                      <label>Weight:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <input type="text" class="form-control" id="weight" name="weight" value="{{ old('weight') }}">
                      </div>
                      <span class="error">
                        <?php 
                          if ($errors->has('weight')) 
                          foreach ($errors->get('weight') as $message) 
                          {
                        ?>
                        <p style='color:red'>
                          {{ $message }}
                        </p>
                        <?php
                          }
                        ?>
                      </span>
                      <!-- /.input group -->
                    </div>
                    <!-- /.form-group -->
                </div>

                <!-- Parent Harvest ID -->
                <div class="col-md-3">
                    <div class="form-group">
                      <label>Parent Harvest ID:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" name="harvest" id="harvest">
                          <option value="0" selected>---Select Harvest---</option>
                          @foreach($harvests as $harvest)
                            <option value="{{ $harvest->id }}" <?php if(old('harvest') == $harvest->id) echo 'selected';?>>{{ $harvest->harvest_batch_id }}</option>
                          @endforeach
                        </select>
                      </div>
                      <!-- /.input group -->
                      <span class="error"><p style='color:red'><?php if ($errors->has('harvest')) echo $errors->first('harvest');?></p></span>
                    </div>
                    <!-- /.form-group -->
                </div>
                <div class="col-md-12">
                  <input type="submit" class="btn btn-info" value="Confirm">
                </div>
              </div>
            </form>
        </div>
        <!-- /.box-body -->
    </div>
@stop
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/dropzone/custom-file-input.js') }}"></script>

<script>
    @if (session('success'))
        $(function () {
                toastr.success('{{ session('success') }}');
        });
    @endif
    @if (session('warning'))
        $(function () {
                toastr.warning('{{ session('warning') }}');
        });
    @endif
    $('.select2').select2();
</script>
@stop

