<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PaymentView</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta http-equiv='cache-control' content='no-cache'>
  <meta http-equiv='expires' content='0'>
  <meta http-equiv='pragma' content='no-cache'>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/bootstrap.min.css') }}"  media="all" type="text/css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/ionicons.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/AdminLTE.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/bootstrap-datepicker.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/custom.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/all.min.css') }}">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        @include('shared.close_button')
        @include('shared.invoice_header')
      </div>
      <!-- /.col-md-12 -->
    </div>

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
          <thead>
            <th>No</th>
            <th>Strain</th>
            <th>Product Type</th>
            <th>Description</th>
            <th>Qty</th>
            <th>Units</th>
            <th>Weight</th>
            <th>Base Price</th>
            <th>CPU</th>
            <th>Discount</th>
            <th>Discount Type</th>
            <th>Extra Discount</th>
            <th>Sub Total</th>
            <th>Extended</th>
            <th>Line Note</th>
            <th>Adjust Total</th>
            <th>COA</th>
          </thead>
          <tbody>
              @foreach ($invoice->fulfilledItem as $key => $item)
                  <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->ap_item->StrainLabel }}</td>
                        <td>{{ $item->ap_item->PTypeLabel }}</td>
                        <td>{{ $item->asset->Description }}</td>
                        <td>{{ $item->asset->qtyonhand }}</td>
                        <td>{{ $item->DividedUnit }}</td>
                        <td>{{ $item->asset->weight }}</td>
                        <td>${{ $item->ap_item->unit_price }}</td>
                        <td>${{ $item->ap_item->CPU }}</td>
                        <td>${{ $item->ap_item->discount }}</td>
                        <td>{{ $item->ap_item->DisType }}</td>
                        <td>${{ $item->ap_item->e_discount }}</td>
                        <td>${{ $item->ap_item->BasePrice }}</td>
                        <td>${{ $item->ap_item->Extended }}</td>
                        <td>{{ $item->ap_item->tax_note != null?$item->ap_item->tax_note:' ' }}</td>
                        <td>${{ $item->ap_item->AdjustPrice }}</td>
                        <td>
                          @foreach ($item->CoaList as $coa)
                            @if ($coa['is_exist'])
                                <a href="{{ asset('assets/upload/files/coa/'.$coa['coa']) }}" target='_blank'>{{ $coa['coa'] }}</a>
                            @else
                                {{ $coa['coa'] }} doesn't exist
                            @endif
                            <br>
                          @endforeach
                        </td>
                  </tr>
              @endforeach
              @if ($invoice->rPDiscount != null)
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td colspan=5>{{ $invoice->rPDiscount->note }}</td>
                    <td>${{ $invoice->rPDiscount->value }}</td>
                </tr>
              @endif
          </tbody>
        </table>
      </div>

      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row" style='margin-top:30px;'>
      <div class="col-md-8">
        <h4>
          Signature
        </h4>
        <div class="col-xs-12 sign_panel" id="sign_panel">
          @if( Storage::disk('public')->has($invoice->number.'/sign.png') )
            <img src="{{ asset('storage/'.$invoice->number.'/sign.png') }}" alt="">
          @else
             No Signature recorded for Invoice.
          @endif
        </div>
      </div>
      <div class="col-md-4">
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th style="width:50%">Total Base Price:</th>
              <td>${{ $invoice->TotalInfo['base_price'] }}</td>
            </tr>
            <tr>
              <th style="width:50%">Discount Amount:</th>
              <td>${{ $invoice->TotalInfo['discount'] }}</td>
            </tr>
            <tr>
                <th style="width:50%">Total Extra Discount:</th>
                <td>${{ $invoice->TotalInfo['e_discount'] }}</td>
              </tr>
            <tr>
              <th style="width:50%">Promotion Value:</th>
              <td>${{ $invoice->TotalInfo['promotion'] }}</td>
            </tr>
            <tr>
              <th style="width:50%">Sub Total:</th>
              <td>${{ $invoice->TotalInfo['extended'] }}</td>
            </tr>
            <tr>
              <th>CA Excise Tax Based On Total Base Price @27%:</th>
              <td>${{ $invoice->TotalInfo['tax'] }}</td>
            </tr>
            <tr>
              <th>Total Due:</th>
              <td>${{ $invoice->TotalInfo['adjust_price'] }}</td>
            </tr>
          </table>
        </div>
      </div>
      <!-- /.col -->
      <!--Payment View Part-->

      <div class="col-md-6">
        <label for="">Verified Payments collected:</label>
        <table class='table table-bordered'>
          @forelse ($invoice['verified'] as $key => $item)
            <tr>
              <td>{{ $item->typeL }}</td>
              <td>${{ $item->amount }}</td>
            </tr>
          @empty
              <tr><td colspan=2>No Data</td></tr>
          @endforelse
        </table>
      </div>
      <div class="col-md-6">
        <label for="">Payments Awaiting Verification:</label>
        <table class='table table-bordered'>
          @forelse ($invoice['unVerified'] as $key => $item)
            <tr>
              <td>${{ $item->typeL }}</td>
              <td>${{ $item->amount }}</td>
            </tr>
          @empty
              <tr><td colspan=2>No Data</td></tr>
          @endforelse
        </table>
      </div>
      <div class="col-md-6">
        <label for="">Payments Not Collected:</label>
        <table class='table table-bordered'>
          <tr>
            <td>Remaining Sub Total</td>
            <td>${{ $invoice->rSubTotal }}</td>
          </tr>
          <tr>
            <td>Remaining Tax</td>
            <td>${{ $invoice->rTax }}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <label for="">Delivery Status :</label>
        <span>{{ $invoice->DeliveryStatusLabel }}</span>
      </div>
      <!--Payment View Part-->

    </div>
    <!-- /.row -->
    <div class="row col-md-12" style='margin-bottom:50px;'>
        <label for="">Communication:</label>
        @if ($invoice['contact']->contact_person != '')
            <table class='table table-bordered'>
            <tr>
                <td><strong>Contact Person</strong></td>
                <td>{{ $invoice['contact']->contact_person }}</td>
                <td><strong>Payment date agreed upon</strong></td>
                <td>{{ $invoice['contact']->p_date }}</td>
            </tr>
            <tr>
                <td><strong>Amount to collect Sub Total</strong></td>
                <td>{{ $invoice['contact']->c_sub_total }}</td>
                <td><strong>Amount to Collect Tax</strong></td>
                <td>{{ $invoice['contact']->c_tax }}</td>
            </tr>
            <tr>
                <td colspan=2><strong>Note</strong></td>
                <td colspan=2>{{ $invoice['contact']->note }}</td>
            </tr>
            </table>
        @else
            <h5>There is no already entered data</h5>
        @endif
    </div>
    <!--Contact Form-->
    {!! Form::open(['method' => 'post', 'url' => ['order_fulfilled/store_invoice_contact'], 'onsubmit' => 'ConfirmSubmit()']) !!}
    <div class="row col-md-12" style='margin-bottom:50px;'>
      @if(Session::has('message'))
      <p class="alert {{ Session::get('alert-class', 'alert-success') }}">
        {{ Session::get('message') }}
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      </p>
      @endif
      <h3>Communication Notes</h3>
      <div class="col-md-8">
        <div class="col-md-12">
          <div class="col-md-6">
            <div class="form-group">
              <label>Contact Person:</label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fas fa-address-book"></i>
                </div>
                <input type="text" class="form-control" placeholder="Enter Contact Person"
                        name='contact_person' id="inputContactPerson"
                        value='{{ old('contact_person') != ''?old('contact_person'):$invoice['contact']->contact_person }}'>
              </div>
              <span class="error">
                <p id="metrc_error" style='color:red'>
                  @error('contact_person') *Contact Person {{ $message }} @enderror
                </p>
              </span>
              <!-- /.input group -->
            </div>
          </div>
          <!--Contact Person-->
          <div class="col-md-6">
            <!-- Date -->
            <div class="form-group">
              <label>Payment date agreed upon:</label>

              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control calendar" name='p_date'
                value='{{ old('p_date') != ''?old('p_date'):$invoice['contact']->p_date }}'>
              </div>
              <span class="error">
                <p id="metrc_error" style='color:red'>
                  @error('p_date') *Payment date agreed upon {{ $message }} @enderror
                </p>
              </span>
              <!-- /.input group -->
            </div>
            <!-- /.form group -->
          </div>
          <!--./Date-->
        </div>
        <!--/.First Col-md-12-->
        <div class="col-md-12">
          <div class="col-md-6">
            <div class="form-group">
              <label>Amount to collect Sub Total:</label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-usd"></i>
                </div>
                <input type="number" placeholder="Enter Amount to collect Sub Total" class="form-control"
                 name="c_sub_total"
                 value='{{ old('c_sub_total') != ''?old('c_sub_total'):$invoice['contact']->c_sub_total }}'
                >
              </div>
              <span class="error">
                <p id="metrc_error" style='color:red'>
                  @error('c_sub_total') *Amount to collect Sub Total {{ $message }} @enderror
                </p>
              </span>
              <!-- /.input group -->
            </div>
          </div>
          <!--Amount to collect Sub Total-->
          <div class="col-md-6">
            <div class="form-group">
              <label>Amount to Collect Tax:</label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-usd"></i>
                </div>
                <input type="number" placeholder="Enter Amount to Collect Tax" class="form-control"
                value='{{ old('c_tax') != ''?old('c_tax'):$invoice['contact']->c_tax }}' name="c_tax">
              </div>
              <span class="error">
                <p id="metrc_error" style='color:red'>
                  @error('c_tax') *Amount to Collect Tax {{ $message }} @enderror
                </p>
              </span>
              <!-- /.input group -->
            </div>
          </div>
          <!--Amount to Collect Tax-->
        </div>
        <!--/.Second Col-md-12-->
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>Add Note:</label>
          <div class="input-group">
            <div class="input-group-addon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <textarea type="number" placeholder="Enter Note" cols="45" rows="5" class="form-control" name="note"
            >{{ old('note') != ''?old('note'):$invoice['contact']->note }}</textarea>
          </div>
          <!-- /.input group -->
        </div>
        <!-- /.form-group -->
        <button class='btn btn-lg btn-info'><i class="fas fa-save"></i>&nbsp;Save</button>
      </div>
      <input type="hidden" name='invoice_id' value='{{ $invoice->id }}'>
      <!--/.Note-->
    </div>
    </form>
    <!--/.Contact Form-->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
<script type="text/javascript" src="{{ asset('assets/invoice_print/jquery.min.js') }}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/bootstrap-datepicker.min.js') }}"></script>
<script>
  let ConfirmSubmit = () => {
    confirm('You are about save Contact Info')
  }
</script>
</html>
