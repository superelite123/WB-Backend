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
        src="{{ public_path('assets/wbcolorlogo.jpg') }}"
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
            <strong style="font-size:12px">Credit Note #: {{ $creditNote->no }}&nbsp;&nbsp;</strong><br>
            <strong style="font-size:12px">Credit Note Date : {{ $creditNote->created_at->format('m/d/Y H:i') }}</strong>
            <br>
            <strong style="font-size:12px">Related : Invoice # : {{ $invoice->number }},{{ $invoice->number2 }}</strong><br>
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
      <table class="" style="table-layout: fixed;word-wrap:break-word;;width:90vw">
        <tbody>
          <tr>
            <th style="padding: 0 15px 10px 0;width:20px">No</th>
            <th style="padding: 0 15px 10px 0;text-align:center;width:70%">Item</th>
            <th style="padding: 0 15px;text-align:center;width:70px">Qty</th>
            <th style="padding: 0 0 0 15px;text-align:center;width:70px" align="right">Price</th>
          </tr>
          @foreach ($creditNote->rItems as $key => $item)
          <tr>
            <td style="padding: 5px 15px 5px 0;text-align:left;width:20px">{{ $key + 1 }}</td>
            <td style="padding: 5px 15px 5px 0;text-align:center;width:70%">{{ $item->Label }}</td>
            <td style="padding: 0 15px;text-align:center;width:70px">{{ $item->qty }}</td>
            <td style="padding: 0 0 0 15px;text-align:center;width:70px">{{ number_format($item->price,2) }}</td>
          </tr>
          @endforeach
          <tr style="border-bottom:2px solid #ecedee;text-align:left;padding:15px 0;">
            <td colspan="2">
              <p>Reason:{{ $creditNote->LReason }}</p>
              <p>Note:{{ $creditNote->detail }}</p>
            </td>
            <td colspan="2" style="font-weight:bold;text-align:right">TOTAL:${{ number_format($creditNote->total_price,2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- /.col -->
</div>
<div>
</div>
</body>
</html>
