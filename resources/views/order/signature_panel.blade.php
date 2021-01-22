<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Signature | Invoice</title>
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
        <h2 class="page-header">
          {{ $invoice->company_detail->companyname }}
          <small class="pull-right">Invoice Date: {{ $invoice->date }}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
        <address>
          {{ $invoice->company_detail->companyname }}<br>
          {{ $invoice->company_detail-> address1}},<br>
          {{ $invoice->company_detail->city }}, {{ $invoice->company_detail->state }} {{ $invoice->company_detail->zip }}<br>
          Phone: {{ $invoice->company_detail->phone }}<br>
          <strong>{{ $invoice->salesperson->firstname.' '.$invoice->salesperson->lastname }}.</strong><br>
          Rep Phone: {{ $invoice->salesperson->telephone }}<br>
          Cultivation License: <strong>{{ $invoice->company_detail->license }}</strong><br>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        Client:
        <address>
          @if ($invoice->customer != null)
            <strong>{{ $invoice->CName }}</strong><br>
            {{ $invoice->customer->address1 }}<br>
            {{ $invoice->customer->city }}, {{ $invoice->customer->state_name->name }} {{ $invoice->customer->zip }}<br>
            Phone: {{ $invoice->customer->companyphone }}<br>
            Email: {{ $invoice->customer->companyemail }}<br>
            License: <strong>{{ $invoice->customer->licensenumber }}</strong>
          @endif
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        Distributor/Transporter:
        <address>
          {{ $invoice->distuributor->companyname }} <br>
          {{ $invoice->distuributor->address1 }}, {{ $invoice->distuributor->address2 }}<br>
          {{ $invoice->distuributor->city }}, {{ $invoice->distuributor->state_name!=null?$invoice->distuributor->state_name->name:'No State' }} {{ $invoice->distuributor->zipcode }}  <br>
          Phone: {{ $invoice->distuributor->phone }} <br>
          Email: {{ $invoice->distuributor->email }} <br>
          License: <strong>{{ $invoice->distuributor->license }}</strong>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        Invoice: <b>{{ $invoice->number }}</b><br>
        Terms: 
        <b>{{ $invoice->Term != null?$invoice->Term->term:'No Term' }}</b><br>    
        @if ($invoice->customer != null)
        <b>Account:</b> {{ $invoice->customer->client_id }}
        @endif
        @endif
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <strong>Note:</strong> <br>{{ $invoice->note }}
      </div>
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Strain</th>
            <th>Product Type</th>
            <th>Qty</th>
            <th>Units</th>
            <th>Base Price</th>
            <th>Discount</th>
            <th>Discount Type</th>
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
                        <td>${{ $item->BasePrice }}</td>
                        <td>${{ $item->Extended }}</td>
                        <td>{{ $item->TNote }}</td>
                        <td>${{ $item->AdjustPrice }}</td>
                  </tr>
              @endforeach
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
          @if( Storage::disk('public')->has($invoice->number.'/sign.png') )
            <img src="{{ asset('storage/'.$invoice->number.'/sign.png') }}" alt="">
          @else
             No Signature recorded for Invoice..
          @endif
        </div>
      </div>
      <!-- /.col -->
      <div class="col-xs-4">
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
      <div class="col-xs-6">
          <h4>
              Please Sign - Signature releases custody of Inventory
          </h4>
          <div class="sign-wrapper col-cs-12">
              <canvas id="signature-pad" class="signature-pad" style="" width=400 height=200></canvas>
          </div>
          <div class="col-xs-12">
              <button class="btn btn-success" id="save">Save</button>
              <button class="btn btn-warning" id="clear">Clear</button>
          </div>
      </div>
      <div class="col-xs-3">
        <!-- Date -->
        <div class="form-group" style="margin-top:100px">
          <label>Select Sign Date:</label>

          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            @php
              $sign_date = $invoice['sign_date'];    
            @endphp
            @if ($invoice['sign_date'] == null || $invoice['sign_date'] == '0000-00-00')
              <?php $sign_date = date('Y-m-d');?>
            @endif
            <input type="text" class="form-control pull-right" id="sign_date" value="{{ $sign_date }}">
          </div>
          <!-- /.input group -->
        </div>
        <!-- /.form group -->
        <!-- Enter Name -->
        <div class="form-group">
          <label>Enter Name:</label>

          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-pencil"></i>
            </div>
            <input type="text" class="form-control pull-right" id="sign_name" value="{{ $invoice['sign_name'] }}">
          </div>
          <!-- /.input group -->
        </div>
        <!-- /.form group -->
      </div>
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
<script type="text/javascript" src="{{ asset('assets/invoice_print/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/signature_pad.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/invoice_print/custom.js') }}"></script>
<script>
  var invoice_id = <?php echo $invoice->id;?>;
  $('#sign_date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    })
</script>  
</html>
