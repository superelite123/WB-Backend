@if (!isset($invoice['logoInvisible']))
<div class="col-md-12 panel-header">
    <div class='col-md-3 col-print-4'>
        <img src="{{ asset('assets/wbcolorlogo.jpg') }}" class='panel-logo' alt="">
    </div>
    <div class="col-md-5 col-print-4"></div>
    <div class="col-md-4 col-print-4 panel-card">
        <p class='bolder'>{{$invoice->company_detail->companyname}}</p>
        <p class='light'>{{ $invoice->company_detail->address1}}</p>
        <p class='light'>
            {{ $invoice->company_detail->city}}&nbsp;
            {{ $invoice->company_detail->state }}&nbsp;
            {{ $invoice->company_detail->zip }}&nbsp;
        </p>
        <p class='light'>Cultivation LIC: CCL19-00006000</p>
        <p class='light'>{{ $invoice->company_detail->phone }}</p>
    </div>
</div>
@endif
<div class="col-md-12 col-print-12 invoice-info">

    <div class="col-md-4 col-print-4 panel-card">
        <span class='bolder' style="font-size:15px">INVOICE DATE:{{ $invoice->date }}</span><br>
        <span class='bolder' style="font-size:15px">INVOICE #:{{ $invoice->number }},{{ $invoice->number2 }}</span><br>
        <span class='bolder' style="font-size:15px">METRC MANIFEST:{{ $invoice->m_m_str }}</span><br>
        <span class='light'>TERMS:</span>
        @if ($invoice->customer != null)
        <span>{{ $invoice->customer->term != null?$invoice->customer->term->term:'No Term' }}</span>
        @endif
        <br>
        <span class='light'>REP:</span><span>{{ $invoice->salesperson != null?$invoice->salesperson->firstname.' '.$invoice->salesperson->lastname:'' }}</span><br>
        <span class='light'>REP PHONE:</span><span>{{ $invoice->salesperson != null?$invoice->salesperson->telephone:'' }}</span>
    </div>

    <div class="col-md-4 col-print-4 panel-card">
        @if($invoice->customer != null)
            <p class='bolder'>{{ $invoice->CName }}</p>
            <p class='light'>{{ $invoice->customer->address1 }}</p>
            <p class='light'>
                {{ $invoice->customer->city }},
                {{ $invoice->customer->state_name->name }}
                {{ $invoice->customer->zip }}
            </p>
            <p class='light'>Phone: {{ $invoice->customer->companyphone }}</p>
            <p class='light'>Email: {{ $invoice->customer->companyemail }}</p>
            <p class='light'>License: <strong>{{ $invoice->customer->licensenumber }}</strong></p>
        @endif
    </div>

    <div class="col-md-4 col-print-4 panel-card">
        <p class='bolder'>Distributor/Transporter:</p>
        <p class='bolder'>{{ $invoice->distuributor->companyname }}</p>
        <p class='light'>
            {{ $invoice->distuributor->address1 }},
            {{ $invoice->distuributor->address2 }}
        </p>
        <p class='light'>
            {{ $invoice->distuributor->city }},
            {{ $invoice->distuributor->state_name!=null?$invoice->distuributor->state_name->name:'No State' }}
            {{ $invoice->distuributor->zipcode }}
        </p>
        <p class='light'>Phone: {{ $invoice->distuributor->phone }}</p>
        <p class='light'>Email: {{ $invoice->distuributor->email }}</p>
        <span class='light'>License:</span><span class="bolder">{{ $invoice->distuributor->license }}</span>
    </div>
</div>
