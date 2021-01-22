<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Collect Payment</title>
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
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
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
    <div class="row">
        @include('shared.close_button')
        @include('shared.invoice_header')
    </div>

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
          <thead>
          <tr>
            <th><i class="fas fa-signature"></i>Strain</th>
            <th>Product Type</th>
            <th>Qty</th>
            <th>Units</th>
            <th>Base Price</th>
            <th>Discount</th>
            <th>Discount Type</th>
            <th>Extra Discount</th>
            <th>Sub Total</th>
            <th>Extended</th>
            <th>Line Note</th>
            <th>Adjust Total</th>
          </tr>
          </thead>
          <tbody>
              @php
               $total_sub_total = 0;
               $total_promotion = 0;
               $total_discounted = 0;
               $total_taxed = 0;
              @endphp
              @foreach ($invoice->itemAP as $item)
                @php
                    if ($item->Taxexempt == 1) {
                      $total_promotion += $item->base_price;
                    }

                    $total_sub_total  += $item->base_price;
                    $total_discounted += $item->discount;
                @endphp
                  <tr>
                        <td>{{ $item->StrainLabel }}</td>
                        <td>{{ $item->PTypeLabel }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->units }}</td>
                        <td>${{ $item->unit_price }}</td>
                        <td>${{ $item->discount }}</td>
                        <td>{{ $item->DisType }}</td>
                        <td>${{ $item->e_discount }}</td>
                        <td>${{ $item->BasePrice }}</td>
                        <td>${{ $item->Extended }}</td>
                        <td>{{ $item->TNote }}</td>
                        <td>${{ $item->AdjustPrice }}</td>
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


    <div class="row">
      <!-- accepted payments column -->
      <div class="col-xs-6">
        <h4>
          Signature
        </h4>
        <div class="col-xs-12 sign_panel" id="sign_panel">
          <img id='sign_img' src="{{ asset('storage/'.$invoice->number.'/sign.png') }}" alt="">
          @if( !Storage::disk('public')->has($invoice->number.'/sign.png') )
              <h5 id='no_img_label'>No Signature recorded for Invoice..</h5>
          @endif
        </div>
        <div class="col-md-12">
            @if ($invoice->sign_date == null || $invoice->sign_date == '0000-00-00')
            @else
              <span>Name:{{ $invoice['sign_name'] }}</span>
              <span style='margin-left:30px'>Date:{{ $invoice->SignDateH }}</span>
            @endif
          </span>
        </div>
      </div>
      <!-- /.col -->
      <div class="col-xs-6">
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
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-md-4">
        <table class='table table-striped'>
          <tbody>
            <tr><h4>Journal Entries</h4></tr>
            @php
            $label = ['Sub Total Collected','Tax Collected'];
            $label1 = ['Sub Total','Tax'];
            $cnt = 0;
            @endphp
            @foreach ($fInfo['logs'] as $key => $val)
              @forelse ($val as $item)
                  @if ($item['allowed'] == 1)
                  <tr style="color:#00a65a">
                  @else
                  <tr style='color:#00c0ef'>
                  @endif
                  <td>
                  {{ $label[$cnt] }}
                  </td>
                <td>${{$item['amount']}}</td>
                <td>{{ $item['date'] }}</td>
                @if ($item['allowed'] == 1)
                <td><i class="fas fa-check"></i></td>
                @else
                <td><i class="fas fa-hourglass"></i></td>
                @endif
                <td>{{ $item['deliveryerName'] }}</td>
                <td><a href='##' onclick="onDeleteP({{ $item['id'] }})" style='color:#d73925'><i class="fas fa-trash"></i></a></td>
              </tr>
              @empty
                <tr><td colspan=6>There is no {{ $label1[$cnt] }} Payment log yet</td></tr>
              @endforelse
              @php
                  $cnt ++;
              @endphp
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="col-md-6">
        <div class="col-md-6">
          <h4>Remaining Sub Total:  <span id='lblRSubTotal'>${{ $fInfo['rSubTotal'] }}</span></h4>
        </div>
        <div class="col-md-6">
          <h4>Remaining Excise Tax:  <span id='lblRTax'>${{ $fInfo['rTax'] }}</span></h4>
        </div>
        <div class="col-md-12">
          @php
              $cnt = 0;
          @endphp
          <table class='table table-striped'>
            <tbody>
              <tr><h4>Today Collected</h4></tr>
              @foreach ($fInfo['tLog'] as $key => $val)
                @forelse ($val as $item)
                    @if ($item['allowed'] == 1)
                    <tr style="color:#00a65a">
                    @else
                    <tr style='color:#00c0ef'>
                    @endif
                    <td>
                    {{ $label[$cnt] }}
                  </td>
                  <td>${{$item['amount']}}</td>
                  <td>{{ $item['date'] }}</td>
                  @if ($item['allowed'] == 1)
                  <td><i class="fas fa-check"></i></td>
                  @else
                  <td><i class="fas fa-hourglass"></i></td>
                  @endif
                </tr>
                @empty
                  <tr><td>There is no today {{ $label1[$cnt] }} Payment log</td></tr>
                @endforelse
                @php
                    $cnt ++;
                @endphp
              @endforeach
              </tbody>
            </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-4">
          <h4>
              Please Sign - Signature for collection of Payment
          </h4>
          <div class="sign-wrapper col-cs-12">
              <canvas id="dSignature-pad" class="signature-pad" style="" width=400 height=200></canvas>
          </div>
          <div class="col-xs-12">
              <button class="btn btn-warning" id="dClear">Clear</button>
          </div>
      </div>
      <div class="col-md-8">
        <div class='col-xs-3'>
            <!-- Enter Name -->
            <div class="form-group">
            <label>Enter Name:</label>

            <div class="input-group">
                <div class="input-group-addon">
                <i class="fas fa-pencil-alt"></i>
                </div>
                <input type="text" class="form-control pull-right" id="d_sign_name" value="" placeholder="Enter your name">
            </div>
            <!-- /.input group -->
            </div>
            <!-- /.form group -->
        </div>
        <!--Sub Total-->
        <div class="col-md-3">
            <div class="form-group">
                <label>Collect Sub Total:</label>

                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control" id="inputTotalCollect" value="0">
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <!--/.Sub Total-->
        <!--Tax-->
        <div class="col-md-3">
            <div class="form-group">
                <label>Collect Tax:</label>

                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="number" class="form-control" id="inputTaxCollect" value="0">
                </div>
                <!-- /.input group -->
            </div>
        </div>
        <!--/.Tax-->
        <div class="col-xs-4">
            <div class="form-group">
                <label>Select Collection Date:</label>

                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right calendar" id="inputCollectionDate" value="{{ date('Y-m-d') }}" readonly>
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
        </div>
        <!--./Div for tax collect-->

        <!-- Serial -->
        <div class='col-md-5'>
            <div class="form-group">
                <label>Cash Bag Serial:</label>

                <div class="input-group">
                <div class="input-group-addon">
                    <i class="fas fa-pencil-alt"></i>
                </div>
                <input type="text" class="form-control pull-right" id="cash_serial" placeholder="Enter Cash Bag Serial">
                </div>
                <!-- /.input group -->
            </div>
            <!-- /.form group -->
        </div>
        <!--./Serial -->
        <div class="col-md-2">
            <button class='btn btn-info btn-md' onclick="collectMoney()" style='margin-top:20px'>Collect</button>
        </div>
        <!--./Sub Total Collect-->
      </div>
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
<div class="modal fade" id='modalDeliveryOption'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Select the Delivery Status</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label>Delivery Option</label>
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fas fa-sliders-h"></i>
                </div>
                <select class="form-control select2" style="width: 100%;" id="dOptions">
                  @foreach($dOptions as $dOption)
                    <option value="{{ $dOption->id }}">{{ $dOption->name }}</option>
                  @endforeach
                </select>
              </div>
              <!-- /.input group -->
              <span class="error"><p id="strain_error" style='color:red'></p></span>
            </div>
            <!--./Form Group-->
          </div>
          <!--./col-md-12-->
        </div>
        <!--./row-->
      </div>
      <div class="modal-footer">
        <div class="col-md-12">
          <button class="btn btn-info pull-right" id="btnSaveDOption">Ok</button>
        </div>
      </div>
      <!--./modal footer-->
    </div>
    <!--./modal content-->
  </div>
  <!--./modal dialog-->
</div>
<!--./modal modal-->
</body>
<script type="text/javascript" src="{{ asset('assets/invoice_print/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/signature_pad.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/orderFulfilled/delivered/collect_payment.js') }}"></script>
<script>
  var invoice_id = <?php echo $invoice->id;?>;
</script>
</html>
