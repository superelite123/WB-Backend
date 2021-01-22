<div class="row">
    <div class="col-md-12" style='margin-top:20px;'>
        <div class='col-md-11'><h4 class='pull-right'>Logged In User Name:{{ auth()->user()->name }}</h4></div>
        <div class='col-md-1'>
            <a href='{{ url('nda_signout/'.auth()->user()->id) }}' class='btn btn-info pull-right' style='margin-right:20px;'>
                <i class="fas fa-sign-out-alt"></i>&nbsp;Sign Out
            </a>
        </div>
    </div>
</div>