
<div class="col-md-12">
    <div class="col-md-4">
        Invoice: <b>{{ $invoice->number }}</b><br>
        Terms:
        @if ($invoice->customer != null)
        <b>{{ $invoice->customer->term != null?$invoice->customer->term->term:'No Term' }}</b><br>
        <b>Account:</b> {{ $invoice->customer->client_id }}
        @endif
    </div>
    <div class="col-md-4">
        <strong>Note:</strong> <br>{{ $invoice->note }}
    </div>
</div>
