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
  <style>
    .banner{
        padding: 7px;
        background-color: #e2e2e2;
    }
    .img-logo{
        width:90px;
        height:69px;
    }
    .logo-title{
        color: #5c5d5d;
        font-size: 20px;
        margin-left:10px;
    }
    .signature-panel{
        display:none
    }
    </style>
</head>
<body>
    <section class="invoice">
        <div class="wrapper">
            <div class="row">
                <div class="col-md-12 banner">
                    <table>
                        <tr>
                            <td class='logo-panel'>
                                <img src="{{ asset('assets/wbcolorlogo.jpg') }}" class='img-logo' style='margin-left:20px;'>
                            </td>
                            <td>
                                <span class='logo-title'>Walnut LLC</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row" style='margin-top:3em'>
                <div class="col-md-12">
                    <h2 style='text-align:center'>Welcome to Walnut LLC</h2>
                </div>
            </div>
            <div class="row" style='margin-top:3em'>
                <div class="col-md-12">
                    <h4 style="text-align:center">Have you Visited us before?</h4>
                </div>
                <div class="col-md-12" style='margin-top:2em;text-align:center'>
                    <div class="col-md-3"></div>
                    <div class="col-md-2" style='margin-bottom:1em'>
                        <a href="{{ url('nda_home') }}" class='btn btn-info btn-lg' style='width:8em'>
                            Yes
                        </a>
                    </div>
                    <div class="col-md-2" style='margin-bottom:1em'>
                        <a href="{{ url('nda_page') }}" class='btn btn-info btn-lg' style='width:8em'>No</a>
                    </div>
                    <div class="col-md-2" style='margin-bottom:1em'>
                        <a href='{{ url('nda_signout_p/') }}' class='btn btn-lg btn-info''>
                            <i class="fas fa-sign-out-alt"></i>&nbsp;Sign Out
                        </a>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>