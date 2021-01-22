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
<div style="padding: 7px;background-color: #e2e2e2;"><img src="http://cultivision.us/walnut/assets/wbcolorlogo.jpg" alt="Logo" style="width:90px;height:69px;"><span style="color: #5c5d5d;font-size: 20px;padding-left: 20px;position: absolute;padding-top: 24px;">Walnut LLC</span><span style="float:right;padding-right:20px;padding-top: 27px;color: #5c5d5d;"><b>Invoice Date:</b> {{ $invoice->date }}</span>
</div>


<div class="row">
  <div class="col-4">
        <p>
          <strong>{{ $invoice->company_detail->companyname }}</strong><br>
          {{ $invoice->company_detail-> address1}},<br>
          {{ $invoice->company_detail->city }}, {{ $invoice->company_detail->state }} {{ $invoice->company_detail->zip }}<br>
          Phone: {{ $invoice->company_detail->phone }}<br>
          Rep: {{ $invoice->salesperson->firstname.' '.$invoice->salesperson->lastname }}.<br>
          Rep Phone: {{ $invoice->salesperson->telephone }}<br>
          Cultivation License: <b>{{ $invoice->company_detail->license }}</b>
        </p>
  </div>
  <div class="col-4">
        <p>
         <strong>{{ $invoice->customer->clientname }}</strong><br>
          {{ $invoice->customer->address1 }}<br>
          {{ $invoice->customer->city }}, {{ $invoice->customer->state_name->name }} {{ $invoice->customer->zip }}<br>
          Phone: {{ $invoice->customer->companyphone }}<br>
          Email: {{ $invoice->customer->companyemail }}<br>
          License: <b>{{ $invoice->customer->licensenumber }}</b>
        </p>
  </div>
    <div class="col-4">
        <p>
        <strong>Distribution/Transportation</strong><br>
        @if ($invoice->distuributor != null)
          <strong>{{ $invoice->distuributor->companyname }}</strong><br>
          {{ $invoice->distuributor->address1 }}, {{ $invoice->distuributor->address2 }}<br>
          {{ $invoice->distuributor->city }}, {{ $invoice->distuributor->state_name->name }} {{ $invoice->distuributor->zipcode }}  <br>
          Phone: {{ $invoice->distuributor->phone }} <br>
          Email: {{ $invoice->distuributor->email }} <br>
          License: <strong>{{ $invoice->distuributor->license }}</strong>
          </p>
        @else
            <p>{{ Config::get('constants.order.no_distributor') }}</p>
        @endif
  </div>

</div>
<div class="row" style="margin-left:20px">
  <div>
      <b>Invoice Date:</b> {{ $invoice->date }} <br>
      <b>{{ $invoice->Term != null?$invoice->Term->term:'No Term' }}</b><br>
  </div>
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
            <th style="width:50%">Extra Discount Amount:</th>
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
</body>
</html>
