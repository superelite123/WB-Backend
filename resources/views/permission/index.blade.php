@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Administrator:Permission')
@section('content_header')
<h1>
    Permission
    <small>User Management</small>
  </h1>
@stop

@section('content')
<!--start edit form-->
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Permissions</h3>
        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    
    <div class="box-body">
        <div class="box-body">
            <div class="row" style='margin-bottom:20px'>
                <div class="col-md-12">
                    <button class='btn btn-info' id='btnNewPermission'><i class="fas fa-plus"></i>&nbsp;New Permission</button>
                </div>
            </div>
            <!--Table Row-->
            <div class="row">
                <div class="col-md-12">
                    <table class='table table-bordered'>
                        <thead>
                            <th>No</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $key => $permission)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $permission->display_name }}</td>
                                    <td>{{ $permission->created_at }}</td>
                                    <td>
                                        <!--<button class='btn btn-info btn-xs'>view</button>-->
                                        <button class='btn btn-warning btn-xs' onclick="EditPermission({{ $permission->id }},'{{ $permission->display_name }}')">edit</button>
                                        <button class='btn btn-danger btn-xs' onclick='DeletePermission({{ $permission->id }})'>delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--/.Table Row-->
        </div>
        <!-- /.box-body -->
    </div>
</div>
<div class="modal fade" id='modalNewPermission'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Permission</h4>
            </div>
            <div class="modal-body">
                <div class="row">    
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="form-group">
                            <label>New Permission Name:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-sign"></i>
                                </div>
                                <input type="text" class="form-control" id="inputNewPermission">
                            </div>
                            <!-- /.input group -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                <button class="btn btn-info pull-right" id="btnSavePermission">Ok</button>
                </div>
            </div>
            <!--./modal footer-->
        </div>
    </div>
</div>
@stop
<script>
    let permissionId = -1
    window.addEventListener('load', function() {
        $(() => {
            $('table').DataTable()
        })
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $('#btnNewPermission').click(() => {
            permissionId = -1
            $('#modalNewPermission').modal()
            $('#inputNewPermission').focus()
        })
        $('#btnSavePermission').click(() => {
            let permissionName = $('#inputNewPermission').val()
            if(permissionName == '')
            {
                alret('Enter the Correct Name')
                return false
            }
            $.ajax({
                url:'_save_permission',
                type:'post',
                data:'id='+permissionId+'&name='+permissionName,
                success:() => {
                    location.reload()
                }
            })
        })
    })
    function EditPermission(id,permission)
    {
        permissionId = id
        $('#inputNewPermission').val(permission)
        $('#modalNewPermission').modal()
        $('#inputNewPermission').focus()
    }
    function DeletePermission(id)
        {
            if(confirm('Are you going to Delete this Permission?'))
            {
                $.get({
                    url:'_delete_permission/'+id,
                    success:() => {
                        location.reload()
                    }
                })
            }
        }
</script>   