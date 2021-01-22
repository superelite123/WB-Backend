@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'New Order')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/form_new.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/bootstrap-tagsinput.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/datepicker/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')

@stop

@section('content')
    <!--start edit form-->
  <div class="box box-info main-panel">
    <div class="box-header with-border">
      <h3 class="box-title"><i class="fas fa-file-invoice"></i> {{ $mode=='create'?'New':'Edit'}} {{' Sales Order'}} {{ $number }}</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label>Customer</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-users"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="client" id="client">
                <option value="0"></option>
                @foreach($clients as $client)
                  <option value="{{ $client->client_id }}" {{ $client->client_id == $invoice->customer_id?'selected':''}}>{{ $client->clientname}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- /.form-group -->
        <!-- /.col -->
        </div>
        <div class="col-md-3">
          <button class="btn btn-info addCustomerBtn"><i class="fa fa-fw fa-plus"></i>Change terms/Update customer</button>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Date:</label>

            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-calendar"></i>
              </div>
              <input type="text" class="form-control" id="date" name="date" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask readonly value="{{ $date }}">
            </div>
            <!-- /.input group -->
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Sales Order:</label>

            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-file-invoice"></i>
              </div>
              <input type="text" class="form-control" id="number" name="number" readonly value="{{ $number }}">
            </div>
            <!-- /.input group -->
          </div>
        </div>
      </div>
      <!-- /.row -->
      <!--Shipping Method Row-->
      <div class="row">
        <!--Saleperson-->
        <div class="col-md-3">
          <div class="form-group">
            <label>Sales Person</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-user-tie"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="salesperson_id" id="salesperson">
                <option value="0"></option>
                @foreach($contact_persons as $person)
                  <option value="{{ $person->contact_id }}" {{ $person->contact_id == $invoice->salesperson_id?'selected':'' }}>{{ $person->firstname." ".$person->lastname}}</option>
                @endforeach
              </select>
              <!-- /.input group -->
            </div>
          </div>
        </div>
        <!--end SalesPerson-->
        <!--Distuributor-->

        <!--end Distuributor-->
        <!--Terms-->
        <div class="col-md-3">
            <label style='margin-top:30px'>Terms:</label>
            <span class='term_content'>{{ $term == null?'No Term':$term->term}}</span>
        </div>
        <!--end Terms-->
      </div>
      <!--End Shipping Method Row-->
      <!--Note Row-->
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label>Add Order Note:</label>
            <div class="input-group">
              <div class="input-group-addon">
                  <i class="fas fa-file-invoice-dollar"></i>
              </div>
              <textarea type="number" cols="45" rows="3" class="form-control" id="note">{{ $invoice->note }}</textarea>
            </div>
            <!-- /.input group -->
          </div>
          <!-- /.form-group -->
        <!-- /.col -->
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <div class="form-group">
              <label>Notes for FulFillment Team:</label>
              <div class="input-group">
                <div class="input-group-addon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <textarea type="number" cols="45" rows="3" class="form-control" id="fulfillmentnote">{{ $invoice->fulfillmentnote }}</textarea>
              </div>
              <!-- /.input group -->
            </div>
          </div>
          <!-- /.form-group -->
        <!-- /.col -->
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label>Entire Order Discount</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-users"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="order_discount" id="order_discount">
                <option value="0"></option>
                @foreach($promos as $promo)
                  <option value="{{ $promo->promoid }}">{{ $promo->name.':'.$promo->multiplier }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- /.form-group -->
        </div>
        <!-- /.col -->
        <div class="col-md-3">
          <label for="">Don't Allow Tax</label>
          <input type="checkbox" id='tax_allow'>
          <p style='display:none'>This Order won't calculate any tax</p>
        </div>
        <!-- /.col -->
      </div>
      <!--./Note Row-->
      <!--Request Row-->
      <div class="row">
        <div class="col-md-12">
          <h5>Build Request</h5>
         <!--Avaliable QTY-->
        <div class="col-md-6">
          <div class="col-md-12" style="position: relative; text-align: right;">
            <span>Total Quantity on Hand:</span>
            <span id='avaliable_qty'>0</span>
            <span>Total Weight on Hand:</span>
            <span id='avaliable_weight'>0</span>
          </div>
        </div>
        <!--./Avaliable QTY-->
        </div>
        <!--Strain-->
        <div class="col-md-3">
          <div class="form-group">
            <label>Strain</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-sliders-h"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="strain" id="strain">
                <option value="0"></option>
                @foreach($strains as $strain)
                  <option value="{{ $strain->itemname_id }}">{{ $strain->strain }}</option>
                @endforeach
              </select>
            </div>
            <!-- /.input group -->
            <span class="error"><p id="strain_error" style='color:red'></p></span>
          </div>
        </div>
        <!--./Strain-->
        <!--Type-->
        <div class="col-md-3">
          <div class="form-group">
            <label>Product Type</label>
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fas fa-sliders-h"></i>
              </div>
              <select class="form-control select2" style="width: 100%;" name="p_type" id="p_type">
                <option value="0"></option>
                @foreach($producttypes as $producttype)
                  <option value="{{ $producttype->producttype_id }}">{{ $producttype->producttype}}</option>
                @endforeach
              </select>
            </div>
            <!-- /.input group -->
            <span class="error"><p id="p_type_error" style='color:red'></p></span>


          </div>


        </div>
        <!--./Type-->
      </div>
      <!--./Request Row-->
      <div class="row">
        <div class="col-md-6">
          <div class='col-md-12'>
            <div class="col-md-4">
              <div class="form-group">
                <label>Quantity:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-sliders-h"></i>
                  </div>
                  <input type="number" class="form-control" id="qty" name="qty">
                </div>
                <span class="error"><p id="qty_error" style='color:red'></p></span>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Units:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-sliders-h"></i>
                  </div>
                  <input type="number" class="form-control" id="units" name="units" value='1' disabled>
                </div>
                <span class="error"><p id="qty_error" style='color:red'></p></span>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Base Price:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                  <input type="number" class="form-control" id="unit_price" name="unit_price" value="">
                </div>
                <!-- /.input group -->
                <span class="error"><p id="unit_price_error" style='color:red'></p></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Sub Total:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-comment-dollar"></i>
                  </div>
                  <input type="number" readonly class="form-control" id="sub_total" name="sub_total" value="">
                </div>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>CPU:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-comment-dollar"></i>
                  </div>
                  <input type="number" readonly class="form-control" id="cpu" name="cpu" value="0">
                </div>
                <!-- /.input group -->
              </div>
            </div>
            <!--Per Unit Price-->
            <!--Promo-->
            <div class="col-md-4">
              <div class="form-group">
                <label>Per Row Discount</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-sliders-h"></i>
                  </div>
                  <select class="form-control select2" style="width: 100%;" name="row_discount" id="row_discount">
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
          </div>
          <div class='col-md-12'style="padding-bottom: 107px;">
            <div class="col-md-4">
              <div class="form-group">
                <label>Discount:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                      <i class="fas fa-comment-dollar"></i>
                  </div>
                  <input type="number" class="form-control" id="discount" name="discount" disabled>
                </div>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Extra Discount:</label>
                    <div class="input-group">
                      <div class="input-group-addon">
                          <i class="fas fa-comment-dollar"></i>
                      </div>
                      <input type="number" class="form-control" id="e_discount" {{ $isNew == 1?:'disabled' }} value=0>
                    </div>
                    <!-- /.input group -->
                </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Extended:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                      <i class="fas fa-comment-dollar"></i>
                  </div>
                  <input type="number" class="form-control" id="less_discount" name="less_discount" value="" min='0' max='99999' disabled>
                </div>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Tax:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                  <input type="number" class="form-control" id="tax" name="tax" value="0" min='0' max='99999'>
                </div>
                <!-- /.input group -->
                <span class="error"><p id="tax_error" style='color:red'></p></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Note:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                  <input type="text" class="form-control" id="tax_note" name="tax_note">
                </div>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Adjust Price:</label>
                <div class="input-group">
                  <div class="input-group-addon">
                      <i class="fas fa-file-invoice-dollar"></i>
                  </div>
                  <input type="number" class="form-control" id="adjust_price" name="adjust_price" value="" disabled>
                </div>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-4">
              <button class="btn btn-info addBtn" id="add_row"><i class="fa fa-fw fa-plus"></i> Add Row</button>
            </div>
          </div>
        </div>
        <div class="col-md-6" style="margin-top: -105px;position: inherit;">
        <h4>Active Inventory as of {{ date('Y-m-d H:i:s') }}</h4>
          <table class="table table-bordered table-striped" id="aInbTable" style="background-color: #ececec;padding: 15px;">
            <thead>
              <th>Strain</th>
              <th>Producttype</th>
              <th>Qty</th>
              <th>Weight</th>
            </thead>
            <tbody>
              @foreach ($aInvList as $item)
                  <tr>
                    <td>{{ $item['strain'] }}</td>
                    <td>{{ $item['p_type'] }}</td>
                    <td>{{ $item['qty'] }}</td>
                    <td>{{ $item['weight'] }}</td>
                  </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <!-- /.row -->
      <!--hr-->
      <div class="row">
        <div class="col-md-12">
            <hr class="first">
            <h3><i class="fas fa-list-ol"></i> Sales Order Items</h3>
        </div>
      </div>
      <!--inserted panel-->
      <div class="row">
        <div class="col-md-12">
          <h4>Inventory Status</h4>
        </div>
        <div class="col-md-12">
          <table class="table table-bordered table-striped fixed_header" id="inserted_table">
            <thead>
              <th>No</th>
              <th>Item</th>
              <th>Quantity</th>
              <th>Units</th>
              <th>Cost</th>
              <th>Sub Total</th>
              <th>CPU</th>
              <th>Discount</th>
              <th>Discount Type</th>
              <th>Extra Discount</th>
              <th>Less Discount</th>
              <th>Line Note</th>
              <th>Adjust Price</th>
              <th></th>
              <th></th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
       <div class="col-md-6 total_panel">
           <hr>
          <div class="col-md-7"><h5>Total Base Price:</h5></div>
          <div class="col-md-5"><h5 id="total_base_price">0</h5></div>
          <div class="col-md-7"><h5>Discount Amount:</h5></div>
          <div class="col-md-5"><h5 id="total_discounted">0</h5></div>
          <div class="col-md-7"><h5>Extra Discount Amount:</h5></div>
          <div class="col-md-5"><h5 id="total_e_discounted">0</h5></div>
          <div class="col-md-7"><h5>Promotion Value:</h5></div>
          <div class="col-md-5"><h5 id="total_promotion">0</h5></div>
          <div class="col-md-7"><h5>Sub Total:</h5></div>
          <div class="col-md-5"><h5 id="total_extended">0</h5></div>
          <div class="col-md-7"><h5>CA Excise Tax Based On Total Base Price @ 27%:</h5></div>
          <div class="col-md-5"><h5 id="total_tax">0</h5></div>
          <div class="col-md-7"><h5>Total Due:</h5></div>
          <div class="col-md-5"><h5 id="total_adjust_price">0</h5></div>
        </div>
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-md-12">
          <button class="btn btn-success btn-lg pull-right makeBtn" id="make_invoice"><i class="fa fa-upload"></i>&nbsp;{{ $mode=='store'?'Build Sales Order':'Update Sales Order' }}</button>
        </div>
      </div>
    </div>
  </div>
    <!--end edit form-->
    <!-- /.box-body -->
    <!--shipping modal-->
    <div class="modal fade" id="modal_shipping_method">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Shipping Info</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                    <label>Shipping Reference:</label>

                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-file"></i>
                      </div>
                      <input type="text" class="form-control" id="reference" name="reference" readonly value="{{ $shipping_method->reference }}">
                    </div>
                    <!-- /.input group -->
                  </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label>Shipment Date:</label>

                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                  <input type="text" class="form-control pull-right datepicker" id="shipment_date" name="shipment_date" value="{{ $shipping_method->shipment_date}}">
                  </div>
                  <!-- /.input group -->
                </div>
              </div>
              <!--/.div-->
              <div class="col-md-12">
                <div class="form-group">
                  <label>Shipped Via:</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-ship"></i>
                    </div>
                    <select class="form-control select2" style="width: 100%;" name="shipping_carrier" id="shipping_carrier">
                      <option value="0"></option>
                      @foreach($carriers as $carrier)
                        <option value="{{ $carrier->carrierid }}" {{ $carrier->carrierid == $shipping_method->shipping_carrier?'selected':'' }}>{{ $carrier->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <!-- /.input group -->
                </div>
              </div>
              <!--/.div-->
              <div class="col-md-12">
                <div class="form-group">
                  <label>Expected Delivery Date:</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right datepicker" id="expected_date" name="expected_date" value="{{ $shipping_method->expected_date}}">
                  </div>
                  <!-- /.input group -->
                </div>
              </div>
              <!--/.div-->
              <div class="col-md-12">
                <div class="form-group">
                  <label>Acutal Delivery Date:</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right datepicker" id="actual_date" name="actual_date" value="{{ $shipping_method->actual_date}}">
                  </div>
                  <!-- /.input group -->
                </div>
              </div>
              <!--/.div-->
              <div class="col-md-12">
                <div class="form-group">
                  <label>Tracking Number:</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-file"></i>
                    </div>
                    <input type="text" data-role="tagsinput" id="trackingid" name="trackingid" value="{{ $shipping_method->trackingid}}">
                  </div>
                  <!-- /.input group -->
                </div>
              </div>
              <!--/.div-->
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button class="btn btn-info pull-right" id="save_shipping_method">Save Changes</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!--/.shipping modal-->

<div class="modal fade" id="modal_credit_note">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Credit Note</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Credit Note:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                <i class="fas fa-dollar-sign"></i>
                                </div>
                                <input type="number" class="form-control" id="txtCreditNote" value=0>
                            </div>
                            <!-- /.input group -->
                        </div>
                        <!-- /.form-group -->
                    </div>
                </div>
            </div>
            <!--./modal body-->
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button class="btn btn-info pull-right" id="btnSaveCreditNote">Save Changes</button>
            </div>
            <!--./modal footer-->
        </div>
        <!--./modal content-->
    </div>
    <!--./modal dialog-->
</div>
<!--./modal-->
@stop
@include('footer')
<script>
    const submitBtnLabel = '<?php echo $mode=='store'?'Build Sales Order':'Update Sales Order';?>'
</script>
@section('js')
  <script type="text/javascript" src="{{ asset('assets/component/js/daterangepicker/moment.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/bootstrap-tagsinput.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/datepicker/bootstrap-datepicker.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/ajax_loader.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/order/form.js?v=20201231') }}"></script>
@stop
