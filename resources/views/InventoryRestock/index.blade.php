@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Inventory on Hold')
@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/order/index.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/component/css/sweetalert.css') }}">
@stop
@section('content_header')
@stop

@section('content')
    <!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
      <h1>Inventory on Hold</h1>

      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered" id="invoice_table" style='width:100%'>
                    <thead>
                        <th>No</th>
                        <th>Metrc Tag</th>
                        <th>Strain</th>
                        <th>Type</th>
                        <th>Approved</th>
                    </thead>
                    <tbody>
                        @foreach ($inventory as $key => $item)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $item->metrc_tag }}</td>
                                <td>{{ $item->Strain->strain }}</td>
                                <td>{{ $item->AssetType->producttype }}</td>
                                <td>
                                    <button class='btn btn-info' onclick="approve({{ $item->fgasset_id }},{{ $item->type }})">Approved</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
</div>
@stop
<script>
    window.addEventListener('load', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        approve = (id,type) => {
            swal({
                    title: "Are You Sure",
                    text: "Are You going to approve this Inventory Item?",
                    type: "info",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: false
                }, function () {
                $.ajax({
                    url:'invrestock/approve',
                    headers:{"content-type" : "application/json"},
                    data: JSON.stringify({id:id,type:type}),
                    type:'post',
                    async:false,
                    success:(res) => {
                        if(res == '1')
                        {
                            swal('Success', 'Approved Successfully', "success")
                            location.reload()
                        }
                        else
                        {
                            swal('Warning', 'Can not find This Inventory', "warning")
                        }
                    },
                    error:(e) => {
                        swal(e.statusText, e.responseJSON.message, "error")
                    }
                })
            })
        }
    })
</script>
@section('js')
    <script type="text/javascript" src="{{ asset('assets/component/js/sweetalert.js') }}"></script>
@stop
