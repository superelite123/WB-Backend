<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* {
  box-sizing: border-box;
}

.row::after {
  content: "";
  clear: both;
  display: table;
}

[class*="col-"] {
  float: left;
  padding: 20px;
}

.col-1 {width: 8.33%;}
.col-2 {width: 16.66%;}
.col-3 {width: 25%;}
.col-4 {width: 30%;}
.col-5 {width: 41.66%;}
.col-6 {width: 50%;}
.col-7 {width: 58.33%;}
.col-8 {width: 66.66%;}
.col-9 {width: 75%;}
.col-10 {width: 83.33%;}
.col-11 {width: 91.66%;}
.col-12 {width: 100%;}

html {
  font-family: "Lucida Sans", sans-serif;
  font-size: 10px;
}

.header {
  background-color: #e2e2e2;
  color: #030040;
  padding: 15px;
}

.menu ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}

.menu li {
  padding: 8px;
  margin-bottom: 7px;
  background-color: #33b5e5;
  color: #ffffff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}

.menu li:hover {
  background-color: #0099cc;
}
#customers {
  font-family: "Lucida Sans", sans-serif;
  font-size: 7px;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 5px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 5px;
  padding-bottom: 5px;
  text-align: left;
  background-color: #E4E4E5;
  color: #000000;
  font-size:7px;
}
</style>
</head>
<body>
<div class="row" style="padding: 7px;background-color: #e2e2e2;">
    <div class="col-4">
        <img
        src="{{ asset('assets/wbcolorlogo.jpg') }}"
        alt="Logo"
        style="width:90px;height:69px;" />
    </div>
    <div class="col-4"></div>
    <div class="col-4">
        <p>
            <strong>{{ $invoice->company_detail->companyname }}</strong><br>
            {{ $invoice->company_detail->address1}},
            <br>
            {{ $invoice->company_detail->city }},
            {{ $invoice->company_detail->state }}
            {{ $invoice->company_detail->zip }}
            <br>
            Cultivation LIC: CCL19-00006000<br>
            {{ $invoice->company_detail->phone }}<br>
        </p>
    </div>
</div>


<div class="row">
  <div class="col-4">
        <p>
            <strong style="font-size:12px">INVOICE DATE:{{ $invoice->date }}</strong><br>
            <strong style="font-size:12px">INVOICE #:{{ $invoice->number }},{{ $invoice->number2 }}</strong><br>
            <strong style="font-size:12px">METRC MANIFEST:{{ $invoice->m_m_str }}</strong><br>
            TERMS:
            @if ($invoice->customer != null)
            <span>{{ $invoice->customer->term != null?$invoice->customer->term->term:'No Term' }}</span>
            @endif<br>
            REP:{{ $invoice->salesperson != null?$invoice->salesperson->firstname.' '.$invoice->salesperson->lastname:'' }}<br>
            REP PHONE:{{ $invoice->salesperson != null?$invoice->salesperson->telephone:'' }}
        </p>
  </div>
  <div class="col-4">
        <p>
          @if ($invoice->customer != null)
            <strong>{{ $invoice->CName }}</strong><br>
            {{ $invoice->customer->address1 }}<br>
            {{ $invoice->customer->city }}, {{ $invoice->customer->state_name != null?$invoice->customer->state_name->name:'' }} {{ $invoice->customer->zip }}<br>
            Phone: {{ $invoice->customer->companyphone }}<br>
            Email: {{ $invoice->customer->companyemail }}<br>
            License: <strong>{{ $invoice->customer->licensenumber }}</strong>
          @endif
        </p>
  </div>

  <div class="col-4">
    <p>
    <strong>Distribution/Transportation</strong><br>
    @if ($invoice->distuributor != null)
    <strong>{{ $invoice->distuributor->companyname }}</strong><br>
    {{ $invoice->distuributor->address1 }}, {{ $invoice->distuributor->address2 }}<br>
    {{ $invoice->distuributor->city }}, {{ $invoice->distuributor->state_name != null?$invoice->distuributor->state_name->name:'' }} {{ $invoice->distuributor->zipcode }}  <br>
    Phone: {{ $invoice->distuributor->phone }} <br>
    Email: {{ $invoice->distuributor->email }} <br>
    License: <strong>{{ $invoice->distuributor->license }}</strong>
    </p>
    @else
        <p>{{ Config::get('constants.order.no_distributor') }}</p>
    @endif
</div>

</div>

<div class="row">
    <div class="col-12">
        <table id="customers">
            <caption><h3>Invoice Items</h3></caption>
            <thead>
              <tr>
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
                <th>Total</th>
                <th>COA</th>
              </tr>
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
                            {{ $coa['coa'] }}
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
    </div>
    <!-- /.col -->
</div>
<div>

    <div style="float:right;line-height:16px;padding: 5px;border: 1px solid #dad2d2;">
        <span style="text-align: left;">Total Base Price: $</span>
        <span style="text-align: right;">{{ $invoice->TotalInfo['base_price'] }}</span> <br>
        <span style="text-align: left;">Discount Amount: $</span>
        <span style="text-align: right;">{{ $invoice->TotalInfo['discount'] }}</span> <br>
        <span style="text-align: left;">Extra Discount Amount: $</span>
        <span style="text-align: right;">{{ $invoice->TotalInfo['e_discount'] }}</span> <br>
        <span style="text-align: left;">Promotion Value: $</span>
        <span style="text-align: right;">{{ $invoice->TotalInfo['promotion'] }}</span> <br>
        <span style="text-align: left;">Sub Total: $</span>
        <span style="text-align: right;">{{ $invoice->TotalInfo['extended'] }}</span><br>
        <span style="text-align: left;">CA Excise Tax Based On Total Base Price @27%: $</span>
        <span  style="text-align: right;">{{ $invoice->TotalInfo['tax'] }}</span><br>
        <span style="text-align: left;">Total Due: $</span>
        <span style="text-align: right;">{{ $invoice->TotalInfo['adjust_price'] }}</span><br>
    </div>
    <div class="col-6">
      <label for="">Note:</label>
      <p>{{ $invoice->note }}</p>
    </div>
</div>
</body>
</html>
