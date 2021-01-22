<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>View Order</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta http-equiv='cache-control' content='no-cache'>
  <meta http-equiv='expires' content='0'>
  <meta http-equiv='pragma' content='no-cache'>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/bootstrap.min.css') }}"  media="all" type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/bootstrap-responsive.min.css') }}"  media="all" type="text/css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/ionicons.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/AdminLTE.min.css') }}"  media="all"  type="text/css">
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
        @include('shared.close_button')
        @include('shared.invoice_header')
    </div>

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-responsive table-striped">
          <thead>
          <tr>
            <th>Strain</th>
            <th>Product Type</th>
            <th>Qty</th>
            <th>Units</th>
            <th>Base Price</th>
            <th>CPU</th>
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
              @foreach ($invoice->itemAP as $item)
                  <tr>
                        <td>{{ $item->StrainLabel }}</td>
                        <td>{{ $item->PTypeLabel }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->units }}</td>
                        <td>${{ $item->unit_price }}</td>
                        <td>${{ $item->CPU }}</td>
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
                    <td colspan=16></td>
                </tr>
                <tr>
                    <td colspan=6>{{ $invoice->rPDiscount->note }}</td>
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
      <div class="col-xs-6">
        <div class="table-responsive">
          <table class="table table-bordered">
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
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
@if ($print == 1)
<script>
  window.print()
</script>
@endif
</html>
