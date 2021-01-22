<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>View Invoice</title>
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
        <table class="table table-striped" style="table-layout: fixed;word-wrap:break-word;;width:90vw">
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
                    <td>${{ $item->DividedDiscount }}</td>
                    <td>{{ $item->ap_item->DisType }}</td>
                    <td>${{ $item->DividedEDiscount }}</td>
                    <td>${{ $item->DividedBasePrice }}</td>
                    <td>${{ $item->DividedExtended }}</td>
                    <td>{{ $item->ap_item->tax_note != null?$item->ap_item->tax_note:' ' }}</td>
                    <td>${{ $item->DividedAdjustPrice }}</td>
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
                    <td colspan=17></td>
                </tr>
                <tr>
                    <td colspan=9>{{ $invoice->rPDiscount->note }}</td>
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
      <div class="col-xs-8">
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
      @if ($print == '0')
        <div class="col-md-8"></div>
        <div class="col-md-4">
          @if ($invoice->paid != null)
          <h3>Paid on Date:{{ $invoice->paid }}</h3>
          @else
            <h3>No Paid Yet</h3>
          @endif
          @if ($invoice->delivered != null)
          <h3>Delivered on Date:{{ $invoice->delivered }}</h3>
          @else
            <h3>No Delivered Yet</h3>
          @endif
        </div>
      @endif
      <div class="col-md-8"></div>
      <div class="col-md-4">
        @if ($invoice->deliver_note != null)
          <h4>Deliver Note:{{ $invoice->deliver_note }}</h4>
          @else
            <h4>No Deliver Note</h4>
          @endif
      </div>
    </div>
    <!-- /.row -->
    @if ($print == '0')
    <div class="row pdf_area">
      <h4>Certificates of Authentication.</h4>
      @foreach ($invoice->fulfilledItem as $key => $item)
        @foreach ($item->CoaList as $coa)
          <div class="col-md-12">
            <h4>{{ $item->asset->description."'s coa file" }}</h4>
            <embed src="{{ asset('assets/upload/files/coa/'.$coa['coa']) }}" type="application/pdf"   height="700px" width="900">
          </div>
        @endforeach
      @endforeach
    </div>
    @endif
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
@if($print == '1')
<script>
  window.print()
</script>
@endif
</body>
</html>
