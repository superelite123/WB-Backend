<div class="col-xs-8">
    <h4>
      Signature
    </h4>
    <div class="col-xs-12 sign_panel" id="sign_panel">
      @if( Storage::disk('public')->has($invoice->number.'/sign.png') )
        <img src="{{ asset('storage/'.$invoice->number.'/sign.png') }}" alt="">
      @else
         No Signature recorded for Invoice.
      @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="table-responsive">
      <table class="table">
        <tr>
          <th style="width:50%">Total Base Price:</th>
          <td>${{ $invoice->TotalInfo['base_price'] }}</td>
        </tr>
        <tr>
          <th style="width:50%">Discount Amount:</th>
          <td>${{ $invoice->TotalInfo['discount'] }}</td>
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