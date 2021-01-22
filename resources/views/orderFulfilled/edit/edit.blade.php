@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Edit Fulfilled Order')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/orderFulfilled/edit.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/datatable-fixedWidthColumn.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/checkbox.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/custom.css') }}"  media="all"  type="text/css">
@stop
@section('content_header')

@stop

@section('content')
<div class="box box-info main-panel">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fas fa-file-invoice"></i> Edit Sales Order:<span class='number'>{{ $invoice->number }}</span></h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="row top-panel">
            @include('shared.invoice_header')
        </div>
        <div class="row">
            <!--Customer-->
            <div class="col-md-3">
                <div class="form-group">
                    <label>Client</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-users"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" name="client" id="clients">
                            <option value="0"></option>
                            @foreach($clients as $client)
                            <option value="{{ $client->client_id }}" {{ $client->client_id == $invoice->customer_id?'selected':''}}>{{ $client->clientname}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!--./Customer-->
            <!--Distributor-->
            <div class="col-md-3">
                <div class="form-group">
                <label>Distributor</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-users"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" id="distributors">
                            <option value="0"></option>
                            @foreach($distributors as $distributor)
                            <option value="{{ $distributor->distributor_id }}" {{ $distributor->distributor_id == $invoice->distuributor_id?'selected':''}}>{{ $distributor->companyname}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!--./Distributor-->
            <!--Order Note-->
            <div class="col-md-3">
                <div class="form-group">
                    <label>Note</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-users"></i>
                        </div>
                        <textarea id="orderNote" cols="30" rows="2">{{ $invoice->note }}</textarea>
                    </div>
                </div>
            </div>
            <!--./Note-->
            <div class="col-md-1">
              <div class="checkbox">
                  <label>
                  <input type="checkbox" id='chkTax' value="" {{ $invoice->tax_allow == 1?'checked':'' }}>
                  <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                  Don't Allow Tax
                  </label>
              </div>
              <p style='display:{{ $invoice->tax_allow == 1?'block':'none' }}'>This Order won't calculate any tax</p>
          </div>
            <div class="col-md-2">
                <button class='btn btn-info btn-md row-btn' id='updateTopInfo'><i class="fas fa-sync-alt">&nbsp;</i>Update Credentials</button>
            </div>
        </div>
        <hr class='hr1'>
        <div class="row">
            <div class="col-md-12" style='background-color:#F0F8FF;color:#2F4F4F;padding-top: 10px;padding-bottom: 10px;'>
                <div class="col-md-2">
                    <h4>Metrc Merging Panel</h4>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label>Parent Metrc Tag for Merge:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <input type="text" class="form-control" id="mergeMetrcTag" placeholder="Please Scan new Parent Metrc Tag">
                      </div>
                      <!-- /.input group -->
                      <span class="error"><p id="errorMergeMetrc" style='color:red'></p></span>
                    </div>
                </div>
                <!--Metrc Tag-->
                <div class="col-md-2">
                    <div class="form-group">
                      <label>Base Price:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-comment-dollar"></i>
                        </div>
                        <input type="number" class="form-control" id="mergeUnitPrice" value="0">
                      </div>
                      <!-- /.input group -->
                    </div>
                </div>
                <!--Per Unit Price-->
                <div class="col-md-2">
                    <div class="form-group">
                      <label>Discount</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-sliders-h"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" id="mergeDiscount">
                          <option value="0"></option>
                          @foreach($promos as $promo)
                            <option value="{{ $promo->promoid }}">{{ $promo->name.':'.$promo->multiplier }}</option>
                          @endforeach
                        </select>
                      </div>
                      <!-- /.input group -->
                      <span class="error"><p id="strain_error" style='color:red'></p></span>
                    </div>
                </div>
                <!--./Promo-->
                <div class="col-md-2">
                <button class='btn bg-navy btn-sm row-btn' id='btnMerge'><i class="fas fa-layer-group">&nbsp;</i>Merge Selected Inventory</button>
            </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-2">
                    <h4>Add a New Metrc from Inventory to this invoice</h4>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label>New Inventory Metrc Tag:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <input type="text" class="form-control" id="newMetrcTag" placeholder="Scan new Metrc Tag to add to Invoice">
                      </div>
                      <!-- /.input group -->
                      <span class="error"><p id="errorNewMetrc" style='color:red'></p></span>
                    </div>
                </div>
                <!--Metrc Tag-->
                <div class="col-md-3 row-btn">
                    <div class="col-md-3">
                        <span>Strain:</span>
                    </div>
                    <div class="col-md-9">
                        <label for="" id='newStainLabel'></label>
                    </div>
                </div>
                <!--Strain-->
                <div class="col-md-4 row-btn">
                    <div class="col-md-3">
                        <span>Product Type:</span>
                    </div>
                    <div class="col-md-9">
                        <label for="" id="newPTypeLabel"></label>
                    </div>
                </div>
                <!--P_type-->
            </div>
            <div class="col-md-12">
                <div class="col-md-2"></div>
                <div class="col-md-2">
                    <div class="form-group">
                      <label>Base Price:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-comment-dollar"></i>
                        </div>
                        <input type="number" class="form-control" id="newUnitPrice" value="0">
                      </div>
                      <!-- /.input group -->
                    </div>
                </div>
                <!--Per Unit Price-->
                <div class="col-md-2">
                    <div class="form-group">
                      <label>Discount</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-sliders-h"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" id="newDiscount">
                          <option value="0"></option>
                          @foreach($promos as $promo)
                            <option value="{{ $promo->promoid }}">{{ $promo->name.':'.$promo->multiplier }}</option>
                          @endforeach
                        </select>
                      </div>
                      <!-- /.input group -->
                      <span class="error"><p id="strain_error" style='color:red'></p></span>
                    </div>
                </div>
                <!--./Promo-->
                <div class="col-md-2">
                    <button class='btn btn-info btn-md row-btn' id="addNewInventory"><i class="glyphicon glyphicon-plus">&nbsp;</i>Add New</button>
                </div>
            </div>
            <div class="col-md-12 table-panel">
                <table class='table table-striped table-bordered nowrap' id='inventory_table'>
                    <thead>
                        <th></th>
                        <th></th>
                        <th>No</th>
                        <th>Strain</th>
                        <th>Product&nbsp;Type</th>
                        <th>Metrc Tag</th>
                        <th>Qty</th>
                        <th>Units</th>
                        <th>Weight</th>
                        <th>Base Price</th>
                        <th>CPU</th>
                        <th>Discount</th>
                        <th>Discount Type</th>
                        <th>Sub Total</th>
                        <th>Extended</th>
                        <th>Line Note</th>
                        <th>Adjust Total</th>
                        <th>New Metrc Tag</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row total-panel">
            @include('orderFulfilled.edit.total')
        </div>
        <div class="row">
            <div class="col-md-8">
            </div>
            <div class="col-md-4 row-btn">
                <button class='btn btn-info btn-lg' id='btnSubmit'><i class="fas fa-sync-alt">&nbsp;</i>Update order and Recalculate totals</button>
            </div>
        </div>
    </div>
</div>
@stop
<div class="modal fade" id="modal-edit-row">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Edit Inventory Info</h4>
          </div>
          <div class="modal-body">
            <div class="col-md-12">
              <div class="form-group">
                <label>Edit Metrc Tag:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                      <i class="fas fa-tag"></i>
                  </div>
                  <input type="text" class="form-control" id="editMetrcTag" placeholder="Scann the Metrc Tag">
                </div>
                <!-- /.input group -->
                <span class="error">
                  <p id="errorEditMetrc" style='color:green'>
                  </p>
                </span>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Edit Base Price:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                      <i class="fas fa-tag"></i>
                  </div>
                  <input type="number" class="form-control" id="editUnitPrice" placeholder="Scann the Metrc Tag">
                </div>
                <!-- /.input group -->
                <span class="error"><p id="errorEditUnitPrice" style='color:red'></p></span>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Edit Discount</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-sliders-h"></i>
                  </div>
                  <select class="form-control select2" style="width: 100%;" id="editDiscount">
                    <option value="0"></option>
                    @foreach($promos as $promo)
                      <option value="{{ $promo->promoid }}">{{ $promo->name.':'.$promo->multiplier }}</option>
                    @endforeach
                  </select>
                </div>
                <!-- /.input group -->
              </div>
            </div>
          </div>
          <div class="modal-footer">
                  <div class="col-md-4 m-foot-div">
                      <button class="btn bg-navy m-foot-btn=" data-dismiss="modal">
                          <i class="fas fa-trash"></i>Cancel
                      </button>
                  </div>
                  <div class="col-md-4 m-foot-div">
                      <button class="btn bg-olive m-foot-btn" id='btnSaveRowInfo'>
                        <i class="fas fa-save"></i>&nbsp;Save
                      </button>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
@include('footer')
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/edit/variable.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/edit/addForm.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/edit/inventory.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/edit/top.js') }}"></script>
@stop
