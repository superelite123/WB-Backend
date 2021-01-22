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
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
  <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/ionicons.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/AdminLTE.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/guest/nda.css') }}">

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
            <div class="col-md-12">
                <h3 class='doc-title'>VISITOR’S NONDISCLOSURE AGREEMENT</h3>
            </div>
            <div class="col-md-12 paragraph">
                <p>This Nondisclosure Agreement ("Agreement") is made and effective the {{ date('m/d/Y')}}.</p>
            </div>
            <div class="col-md-12 paragraph">
                <h5 class='paragraph-title'>1.ACCESS TO CONFIDENTIAL INFORMATION</h5>
                <p>
                    The Visitor understands that he/she may be given access to confidential information belonging 
                    to the Company through his/her relationship with the Company or as a result of his/her access 
                    to the Company's premises.
                </p>
            </div>
            <div class="col-md-12 paragraph">
                <h5 class='paragraph-title'>2.NATURE OF CONFIDENTIAL INFORMATION</h5>
                <p>
                    The Visitor understands and acknowledges that the Company's trade secrets consist of information 
                    and materials that are valuable and not generally known by the Company's competitors.<br>
                    The Company's trade secrets include:
                </p>
                <p class='paragraph-section'>
                    <span class='ordermark'>a.</span>
                    Any and all information concerning the Company's current, 
                    future or proposed products, including, but not limited to, unpublished computer code 
                    (both source code and object code), drawings, specifications, notebook entries, technical notes 
                    and graphs, computer printouts, technical memoranda and correspondence, product development agreements 
                    and related agreements.
                </p>
                <p class='paragraph-section'>
                    <span class='order-mark'>b.</span>
                    Information and materials relating to the Company's purchasing, accounting and marketing; including, 
                    but not limited to, marketing plans, sales data, unpublished promotional material, cost 
                    and pricing information and customer lists.
                </p>
                <p class='paragraph-section'>
                    <span class='order-mark'>c.</span>
                    Information of the type described above which the Company obtained from another party 
                    and which the Company treats as confidential, whether or not owned or developed by the Company.
                </p>
            </div>
            <div class="col-md-12 paragraph">
                <h5 class='paragraph-title'>3.VISITOR’S OBLIGATIONS</h5>
                <p>
                    In consideration of being admitted to the Company's facilities, The Visitor agrees to 
                    hold in the strictest confidence any trade secrets or confidential information which is disclosed to him/her. 
                    The Visitor agrees not to remove any document, equipment or other materials from the premises without the Company's written permission. The Visitor will not photograph 
                    or otherwise record any information to which he/she may have access during the visit. 
                </p>
            </div>
            <div class="col-md-12 paragraph">
                <h5 class='paragraph-title'>4. BINDING AGREEMENT</h5>
                <p>
                    This Agreement is binding on the Visitor, his/her heirs, executors, administrators and assigns; 
                    and inures to the benefit of the Company, its successors and assigns.  
                </p>
            </div>
            <div class="col-md-12 paragraph">
                <h5 class='paragraph-title'>5. ENTIRE AGREEMENT</h5>
                <p>
                    This Agreement constitutes the entire understanding between the Company and the Visitor with respect to its subject matter. 
                    It supersedes all earlier representations and understandings, whether oral or written.
                    IN WITNESS WHEREOF, 
                    Company and Customer have executed this agreement in Long Beach on {{ date('m/d/Y') }}
                </p>
            </div>
        </div>
        <!-- /.row -->
        <div class="row form-panel">
            <div class="col-md-5">
              <div class="col-md-12">
                <h4>Walnut Distro</h4>
                <img src="{{ asset('sign.png') }}" class='walnut-sign' alt="">
                <h5>David Schaeffer - Director of Operations</h5>
              </div>
              <div class="col-md-12" style='background-color:#e3e3e3;padding-bottom:2em'>
                <div class="col-md-12">
                  <h3 style='margin-top:1em'>Please Scan ID</h3>
                </div>
                <div class="col-md-12" id='video-panel'>
                  <video id='video' autoplay playsinline></video>
                </div>
                <div class="col-md-12" style='text-align:center'>
                    <button id="btnCapture" class='btn btn-info'>
                      <i class="fas fa-images"></i>&nbsp;Take Photo
                    </button>
                </div>
                <div class="col-md-12">
                  <canvas id="canvas">
                  </canvas>
                  <img id="photo" src='{{ asset("assets/noimage.png") }}' alt="The screen capture will appear in this box.">
                </div>
              </div>
            </div>
            <div class="col-md-7">
                <div class="col-md-12">
                  <h4>Visitor Name</h4>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Customer's Name:</label>
        
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fas fa-users"></i>
                      </div>
                      <input type="text" placeholder="Enter your Name" class="form-control" id="customerName" name='customerName' >
                    </div>
                    <!-- /.input group -->
                  </div>
                </div>
                <!--/.Customer Name-->
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Title:</label>
        
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fas fa-file-invoice"></i>
                      </div>
                      <input type="text" placeholder="Enter Title" class="form-control" id="title" name='title' >
                    </div>
                    <!-- /.input group -->
                  </div>
                </div>
                <!--/.Title-->
                <div class="col-md-12">
                    <div class="form-group">
                      <label>Company Name:</label>
          
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-building"></i>
                        </div>
                        <input type="text" placeholder="Enter your Name" class="form-control" id="companyName" name='companyName' >
                      </div>
                      <!-- /.input group -->
                    </div>
                </div>
                <!--/.Company Name-->
                <div class="col-md-12">
                    <div class="form-group">
                      <label>Email:</label>
          
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-at"></i>
                        </div>
                        <input type="email" placeholder="Enter Email Address" class="form-control" id="email" name='email' >
                      </div>
                      <!-- /.input group -->
                    </div>
                </div>
                <!--/.Email-->
                <div class="col-md-12">
                    <div class="form-group">
                      <label>Street:</label>
          
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-street-view"></i>
                        </div>
                        <input type="text" placeholder="Enter Street" class="form-control" id="street" name='street' >
                      </div>
                      <!-- /.input group -->
                    </div>
                </div>
                <!--/.Street-->
                <div class="col-md-12">
                  <div class="form-group">
                    <label>City:</label>
        
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fas fa-city"></i>
                      </div>
                      <input type="text" placeholder="Enter your City Name" class="form-control" id="city" name='city' >
                    </div>
                    <!-- /.input group -->
                  </div>
                </div>
                <!--/.City-->
                <div class="col-md-12">
                  <div class="form-group">
                    <label>State:</label>
        
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fas fa-flag-usa"></i>
                      </div>
                      <select class="form-control select2" style="width: 100%;" name="state" id="state">
                          <option value="0" disabled selected>Select State</option>
                          @foreach ($states as $state)
                              <option value="{{ $state->state_id }}">{{ $state->name }}</option>
                          @endforeach
                      </select>
                      <!-- /.input group -->
                      </div>
                  </div>
                </div>
                <!--/.State-->
                <div class="col-md-12">
                    <div class="form-group">
                      <label>Zip:</label>
          
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-truck"></i>
                        </div>
                        <input type="text" placeholder="Enter Zip Cide" class="form-control" id="zip" name='zip' >
                      </div>
                      <!-- /.input group -->
                    </div>
                </div>
                <!--/.Zip-->
                <div class="col-md-12">
                    <div class="form-group">
                      <label>Customer Type</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fas fa-user-tie"></i>
                        </div>
                        <select class="form-control select2" style="width: 100%;" name="customerType" id="customerType">
                            <option value="0" disabled selected>Select Type</option>
                            @foreach ($customerTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <!-- /.input group -->
                      </div>
                    </div>
                </div>
                <!--/.Customer Type-->
                <div class="col-md-12">
                    <h4><i class="fas fa-signature">&nbsp;&nbsp;</i>Please Sign</h4>
                    <canvas id="sig-canvas" width="620" height="160">
                      Get a better browser
                    </canvas>
                </div>
                <div class="col-md-12">
                    <button class="btn btn-danger" id="sig-clearBtn"><i class="fa fa-trash" aria-hidden="true">&nbsp;</i>Clear Signature</button>
                </div>
            </div>
            <div class="col-md-12">
              <div class="col-md-5"></div>
              <div class="col-md-5" style='margin-top:2em'>
                <div class="col-md-12">
                  <button class="btn btn-info btn-lg" onclick='submitData()'><i class="fas fa-signature"></i>&nbsp;Submit</button>
                </div>
              </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
<script type="text/javascript" src="{{ asset('assets/invoice_print/jquery.min.js') }}"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/guest/capture.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/guest/nda.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/component/js/signature.js') }}"></script>
</html>