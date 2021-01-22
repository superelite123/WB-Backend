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
        .tbl-items td,.tbl-items th{
            border: 1px solid #ecedee;
        }
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
                                              <h3>Thank you for your order.</h3>
                                              Dear {{ $invoice->customer->clientname }}.</br></br>
                                              Your invoice <strong>No: {{ $invoice->number }}</strong> is being processed.<br/></br>
                                            </div>

                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td align="left" colspan=3 style="font-size:0px;padding:10px 25px;">

                                            <table class="tbl-items" style="cellspacing:0;color:#000;font-family:'Helvetica Neue',Arial,sans-serif;font-size:13px;line-height:22px;table-layout:auto;width:100%;text-align:center">
                                                <tr style="border-bottom:1px solid #ecedee;">
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
                                                    <th>Sub</th>
                                                    <th>Extended</th>
                                                    <th>Line Note</th>
                                                    <th>Total</th>
                                                </tr>
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
                                                            <td>${{ $item->DividedBasePrice }}</td>
                                                            <td>${{ $item->DividedExtended }}</td>
                                                            <td>{{ $item->ap_item->tax_note != null?$item->ap_item->tax_note:' ' }}</td>
                                                            <td>${{ $item->DividedAdjustPrice }}</td>
                                                    </tr>
                                                @endforeach
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