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
          <h1>Inventory Bulk Import Confirm</h1>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <!-- /.box-header -->

        <div class="box-body">
            <div class="row" style="margin-bottom:30px">
                <div class="col-md-12">
                    <a href="javascript:history.go(-1)"><i class="fas fa-hand-point-left"></i>Back</a>
                </div>
            </div>
            <form action="bulk_import" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-12">
                  <input type="submit" class="btn btn-info" value="ADD">
                </div>
                <div class="col-md-12">
                  <table class="table table-bordered">
                    <thead>
                      <th>Metrc</th>
                      <th>Harvest</th>
                      <th>Strain</th>
                      <th>Product Type</th>
                      <th>Weight</th>
                    </thead>
                    <tbody>
                      @foreach ($bulk_import_data as $key => $item)
                        <tr>
                          <td>
                            <input type="text" class="form-control" name="items[{{ $key }}][metrc_tag]" value="{{ $item['metrc'] }}" placeholder="Enter Metrc Tag">
                          </td>
                          <td>
                            <select class="form-control select2" style="width: 100%;" name="items[{{ $key }}][parent_id]">
                              @foreach($harvests as $harvest)
                                <option value="{{ $harvest->id }}" <?php if($item['harvest'] == $harvest->id) echo 'selected';?>>{{ $harvest->harvest_batch_id }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td>
                            <select class="form-control select2" style="width: 100%;" name="items[{{ $key }}][strainname]">
                              @foreach($strains as $strain)
                                <option value="{{ $strain->itemname_id }}" <?php if($item['strain'] == $strain->itemname_id) echo 'selected';?>>{{ $strain->strain }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td>
                            <select class="form-control select2" style="width: 100%;" name="items[{{ $key }}][asset_type_id]">
                              @foreach($p_types as $p_type)
                                <option value="{{ $p_type->producttype_id }}" <?php if($item['p_type'] == $p_type->producttype_id) echo 'selected';?>>{{ $p_type->producttype }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td>
                            <input type="text" class="form-control" name="items[{{ $key }}][weight]" value="{{ $item['weight'] }}" placeholder="Enter Metrc Tag">
                          </td>
                          <td>
                            <select class="form-control select2" style="width: 100%;" name="items[{{ $key }}][i_type]">
                              <option value="2" <?php if($item['i_type'] == 2) echo 'selected';?>>Inv 1</option>
                              <option value="1" <?php if($item['i_type'] == 1) echo 'selected';?>>Inv 2</option>
                            </select>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <div class="col-md-12">
                  <input type="submit" class="btn btn-info" value="ADD">
                </div>
              </div>
              <input type="hidden" name="default_strain" value="{{ $default_strain }}">
              <input type="hidden" name="default_p_type" value="{{ $default_p_type }}">
              <input type="hidden" name="default_harvest" value="{{ $default_harvest }}">
              <input type="hidden" name="fgasset_id" value="{{ isset($fgasset_id) ? $fgasset_id : 0 }}">
              <input type="hidden" name="i_type" value="{{ isset($i_type) ? $i_type : 0 }}">
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
    $('.select2').select2();
</script>
@stop

