<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>NDA</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta http-equiv='cache-control' content='no-cache'>
  <meta http-equiv='expires' content='0'>
  <meta http-equiv='pragma' content='no-cache'>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/bootstrap.min.css') }}"  media="all" type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/AdminLTE.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/css/nda_m/view.css') }}">
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
                <hr class='titlebar'>
            </div>
            <div class="col-md-12 paragraph">
                <p>This Nondisclosure Agreement ("Agreement") is made and effective the {{ date('m/d/Y')}}.</p>
            </div>
            <div class="col-md-12 paragraph">
                <table class='class1-table'>
                    <tr>
                        <td class='class1-td'> <label for="">BETWEEN:</label> </td>
                        <td>
                            <span>Walnut Distro,3030 Walnut Ave (the "Company"), a corporation organized</span>
                            <p style='text-align:right;margin-right:57px'>and</p>
                            <p>existing under the laws of the State of California, with its head office located at:</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12 paragraph">
                <table class='class1-table'>
                    <tr>
                        <td class='class1-td'> <label for="">AND:</label> </td>
                        <td>
                            <span>{{ $nda->customer_name }}</span>
                            <span>(the "Visitor"),a (an) {{ $nda->rCustomerType->name }}</span>
                            <p>
                                corporation organized and existing under the laws of the State of California,with 
                                its head office located
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><span>{{ $nda->street }}</span></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><span>{{ $nda->city }}</span></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><span>{{ $nda->rState->name,$nda->zip }}</span></td>
                    </tr>
                </table>
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
                    <table class='subsection'>
                        <tr>
                            <td class='subsection-mark'>a.</td>
                            <td class='subsection-content'>Any and all information concerning the Company's current, 
                                future or proposed products, including, but not limited to, unpublished computer code 
                                (both source code and object code), drawings, specifications, notebook entries, technical notes 
                                and graphs, computer printouts, technical memoranda and correspondence, product development agreements 
                                and related agreements.</td>
                        </tr>
                    </table>
                </p>
                <p class='paragraph-section'>
                    <table class='subsection'>
                        <tr>
                            <td class='subsection-mark'>b.</td>
                            <td class='subsection-content'>
                                Information and materials relating to the Company's purchasing, accounting and marketing; including, 
                                but not limited to, marketing plans, sales data, unpublished promotional material, cost 
                                and pricing information and customer lists.
                            </td>
                        </tr>
                    </table>
                </p>
                <p class='paragraph-section'>
                    <table class='subsection'>
                        <tr>
                            <td class='subsection-mark'>c.</td>
                            <td class='subsection-content'>
                                Information of the type described above which the Company obtained from another party 
                                and which the Company treats as confidential, whether or not owned or developed by the Company.
                            </td>
                        </tr>
                    </table>
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
            <table class='sign-table'>
                <tr>
                    <td style='width:50%'>
                        <h4>Walnut Distro</h4>
                        <img src="{{ asset('sign.png') }}" class='walnut-sign' alt="">
                        <h5>David Schaeffer - Director of Operations</h5>
                        <h3>Visitor's ID</h3>
                        @if ($id_file != null)
                            <img src="{!! $id_file !!}" style='width:500px;height:400px' alt="">
                        @endif
                    </td>
                    <td style='width:50%'>
                        <h4>Visitor Name</h4>
                        <p class='visitor-info'>{{ $nda->customer_name }}</p>
                        <p class='visitor-info'>{{ $nda->title }}</p>
                        <p class='visitor-info'>{{ $nda->company_name }}</p>
                        <p class='visitor-info'>{{ $nda->email }}</p>
                        <p class='visitor-info'>{{ $nda->street }}</p>
                        <p class='visitor-info'>{{ $nda->city }}</p>
                        <p class='visitor-info'>{{ $nda->zip }}</p>
                        <p class='visitor-info'>{{ $nda->rState->name }}</p>
                        <p class='visitor-info'>{{ $nda->rCustomerType->name }}</p>
                        <img src="{{ asset('storage/ndaSigns/'.$nda->signature_file) }}" class='walnut-sign' alt="">
                    </td>
                </tr>
            </table>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
