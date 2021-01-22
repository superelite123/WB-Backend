<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NDA</title>
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
  <link rel="stylesheet" href="{{ asset('assets/component/css/growl/jquery.growl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/guest/nda_home.css') }}">

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
        <div class="row email_addres">
            <div class="col-md-12" style='text-align:center'>
                <h3>Please enter the email address that you have used before.</h3>
            </div>
            <div class="col-md-3"></div>
            <!--Email Address-->
            <div class="col-md-6">
                <div class="input-group input-group-lg">
                    <input type="email" class="form-control" id='inEmail' 
                    placeholder="Please Enter Your Email Address" >
                    <span class="input-group-btn">
                        <button class="btn btn-info btn-flat" onclick='checkEmail()'><i class="fas fa-at"></i>&nbsp;Confirm</button>
                    </span>
                </div>
            </div>
            <!--/Email Address-->
            <div class="col-md-3"></div>
        </div>
        <!-- /.row -->
        <div class="row signature-panel">
            <div class="col-md-8">
                <div class="col-md-12">
                    <h4><i class="fas fa-signature">&nbsp;&nbsp;</i>Please Sign</h4>
                    <canvas id="sig-canvas" width="620" height="260">
                        Get a better browser, bro.
                    </canvas>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-danger" id="sig-clearBtn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Clear Signature</button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-info" onclick='onSubmit()'><i class="fas fa-signature"></i>&nbsp;Sign In</button>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
<script type="text/javascript" src="{{ asset('assets/invoice_print/jquery.min.js') }}"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/component/js/growl/jquery.growl.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/guest/nda_home.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/component/js/signature.js') }}"></script>
</html>