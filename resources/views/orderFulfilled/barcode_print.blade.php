<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Print Invoice</title>
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
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/all.min.css') }}">
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
    <div class="row pdf_area">
      <div class="col-md-2">
        <button class='btn btn-info control_panel'>Show Control Panel</button>
      </div>
    </div>
    <!-- title row -->
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
            <th>No</th>
            <th>Strain</th>
            <th>Product Type</th>
            <th>Description</th>
            <th>Metrc</th>
          </tr>
          </thead>
          <tbody>
              @foreach ($invoice->fulfilledItem as $key => $item)
                  <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->ap_item->StrainLabel }}</td>
                        <td>{{ $item->ap_item->PTypeLabel }}</td>
                        <td>{{ $item->asset->Description }}</td>
                        <td style='text-align:center'>
                            <img src="data:image/png;base64,{{ base64_encode($generator->getBarcode($item->asset->metrc_tag, $generator::TYPE_CODE_128))}}">
                            <br>
                            <span style="font-size:8px;">{{ $item->asset->metrc_tag }}</span>
                        </td>
                  </tr>
              @endforeach
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 coa_area">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>COA Name</th>
            <th>Print</th>
          </tr>
          </thead>
          <tbody>
              @foreach ($coas['exist'] as $item)
              <tr>
                <td>{{ $item }}</td>
                <td><a href="javascript: w=window.open('{{ asset('assets/upload/files/coa/'.$item) }}'); w.print();" class='btn btn-info btnPrint'><i class="fa fa-print">&nbsp;</i>Print</a></td>
              </tr>
              @endforeach
          </tbody>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
<div class="modal fade " id="bd-example-modal-sm">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Control Panel</h4>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12">
                    @forelse ($coas['n_exist'] as $item)
                      <h4 style="color:red">{{ $item }}</h4>
                    @empty
                    <h4 style="color:green">All COA's appear correct</h4>
                    @endforelse
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <div class="col-md-12" style='align:center'>
              <div class="btn-group">
                <a href='../../coalibrary' target='_blank' class="btn btn-info">Upload</a>
                <a href='javascript:;location.reload()' class="btn btn-info">Refresh</a>
                <button type="button" id='print_barcode' class="btn btn-info">Print Barcode</button>
              </div>
            </div>
          </div>
      </div>
  </div>
</div>
</body>
<script src="{{ asset('assets/invoice_print/jquery.min.js') }}"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="{{ asset('assets/js/orderFulfilled/print.js') }}"></script>
</html>
