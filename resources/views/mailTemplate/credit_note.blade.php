<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <title>

    </title>
    <!--[if !mso]><!-- -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        #outlook a {
            padding: 0;
        }
        .header-font{
          font-family:'Helvetica Neue',Arial,sans-serif;
          font-size:14px;
          text-align:left;
          color:#525252;"
        }
        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block;
            margin: 13px 0;
        }
    </style>
    <!--[if !mso]><!-->
    <style type="text/css">
        @media only screen and (max-width:480px) {
            @-ms-viewport {
                width: 320px;
            }
            @viewport {
                width: 320px;
            }
        }
    </style>
    <!--<![endif]-->
    <style type="text/css">
        @media only screen and (min-width:480px) {
            .mj-column-per-100 {
                width: 100% !important;
            }
        }
    </style>


    <style type="text/css">
    </style>

</head>

<body style="background-color:#f9f9f9;">


    <div style="background-color:#f9f9f9;">


        <div style="background:#f9f9f9;background-color:#f9f9f9;Margin:0px auto;max-width:850px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f9f9f9;background-color:#f9f9f9;width:100%;">
                <tbody>
                    <tr>
                        <td style="border-bottom:#333957 solid 5px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>


        <div style="background:#fff;background-color:#fff;Margin:0px auto;max-width:850px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#fff;background-color:#fff;width:100%;">
                <tbody>
                    <tr>
                        <td style="border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">

                            <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;">

                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:bottom;" width="100%">

                                    <tr>
                                        <td align="center" colspan="3" style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <div style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:18px;font-weight:bold;line-height:22px;text-align:left;color:#525252;">
                                              Credit Note # ({{ $creditNote->no }}) has been created and approved.<br>
                                              Please see attached CN for details.
                                            </div>

                                        </td>
                                    </tr>
                                    <tr>
                                      <td align="center" style="font-size:0px;padding:10px 25px;width:37%;word-break:break-word;">
                                        <p class="header-font"><strong>Credit Note #: {{ $creditNote->no }}</strong></p>
                                        <p class="header-font"><strong>Credit Note Date: {{ $creditNote->created_at->format('m/d/Y H:i') }}</strong></p>
                                        <p class="header-font"><strong>Related : Invoice # : {{ $invoice->number.','.$invoice->number2 }}</strong></p>
                                        <p class="header-font">TERMS: 
                                          @if ($invoice->customer != null)
                                          {{ $invoice->customer->term != null?$invoice->customer->term->term:'No Term' }}
                                          @endif</p>
                                        <p class="header-font">REP:{{ $invoice->SalesRepName }}</p>
                                        <p class="header-font">REP PHONE:{{ $invoice->salesperson != null?$invoice->salesperson->telephone:'' }}</p>
                                      </td>
                                      <td align="center" style="font-size:0px;width:30%;word-break:break-word;">
                                        @if($invoice->customer != null)
                                          <p class="header-font"><strong>{{ $invoice->CName }}</strong></p>
                                          <p class="header-font">{{ $invoice->customer->address1 }}</p>
                                          <p class="header-font">{{ $invoice->customer->city }},
                                            {{ $invoice->customer->state_name->name }}
                                            {{ $invoice->customer->zip }}</p>
                                          <p class="header-font">Phone: {{ $invoice->customer->companyphone }}</p>
                                          <p class="header-font">Email: {{ $invoice->customer->companyemail }}</p>
                                          <p class="header-font">License: <strong>{{ $invoice->customer->licensenumber }}</strong></p>
                                        @endif
                                      </td>
                                      <td align="center" style="font-size:0px;width:30%;word-break:break-word;">
                                        <p class="header-font"><strong>Distributor/Transporter:</strong></p>
                                        <p class="header-font"><strong>{{ $invoice->distuributor->companyname }}</strong></p>
                                        <p class="header-font">{{ $invoice->distuributor->address1 }},
                                          {{ $invoice->distuributor->address2 }}</p>
                                        <p class="header-font">{{ $invoice->distuributor->city }},
                                          {{ $invoice->distuributor->state_name!=null?$invoice->distuributor->state_name->name:'No State' }}
                                          {{ $invoice->distuributor->zipcode }}</p>
                                        <p class="header-font">Phone: {{ $invoice->distuributor->phone }}</p>
                                        <p class="header-font">Email: {{ $invoice->distuributor->email }}</p>
                                        <p class="header-font">License: <strong>{{ $invoice->distuributor->license }}</strong></p>
                                      </td>
                                  </tr>
                                    <tr>
                                        <td align="left" colspan=3 style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <table 0="[object Object]" 1="[object Object]" 2="[object Object]" border="0" style="cellspacing:0;color:#000;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;line-height:22px;table-layout:auto;width:100%;">
                                                <tr style="border-bottom:1px solid #ecedee;text-align:left;">
                                                  <th width="20px" style="padding: 0 15px 10px 0;">No</th>
                                                  <th style="padding: 0 15px 10px 0;text-align:center">Item</th>
                                                  <th width="50px" style="padding: 0 15px;text-align:center">Qty</th>
                                                  <th width="70px" style="padding: 0 0 0 15px;text-align:center" align="right">Price</th>
                                                </tr>
                                                @foreach ($creditNote->rItems as $key => $item)
                                                  <tr>
                                                    <td style="padding: 5px 15px 5px 0;text-align:center">{{ $key + 1 }}</td>
                                                    <td style="padding: 5px 15px 5px 0;text-align:center">{{ $item->Label }}</td>
                                                    <td style="padding: 0 15px;text-align:center">{{ $item->qty }}</td>
                                                    <td style="padding: 0 0 0 15px;">{{ number_format($item->price,2) }}</td>
                                                  </tr>
                                                @endforeach
                                                <tr style="border-bottom:2px solid #ecedee;text-align:left;padding:15px 0;">
                                                    <td colspan="2">
                                                      <p>Reason:{{ $creditNote->LReason }}</p>
                                                      <p>Note:{{ $creditNote->detail }}</p>
                                                    </td>
                                                    <td style="padding: 0 0 0 15px; font-weight:bold" align="right">TOTAL&nbsp;:&nbsp;${{ number_format($creditNote->total_price,2) }}</td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>

                                </table>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>


    </div>

</body>

</html>