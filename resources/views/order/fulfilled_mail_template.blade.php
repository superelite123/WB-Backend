<html>
    
    <body>
        
       <h3>Thank you for your order. </h3>   
        
       Dear {{ $invoice->customer->clientname }}.</br></br>
       
       Your invoice <strong>no: {{ $invoice->number }}</strong> is being processed.<br/></br>
       
    <!-- Table row -->
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
              <thead>
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
                        <td>${{ $item->DividedBasePrice }}</td>
                        <td>${{ $item->DividedExtended }}</td>
                        <td>{{ $item->ap_item->tax_note != null?$item->ap_item->tax_note:' ' }}</td>
                        <td>${{ $item->DividedAdjustPrice }}</td>
                  </tr>
              @endforeach
              </tbody>
            </table>
          </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
       </br>
       <h3>Please find attached to this email your invoice.</h3>
    </body>
</html>