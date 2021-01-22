@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Roles And Roles')
@section('content_header')
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Roles</h3>
        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row" style='margin-bottom:20px'>
            <div class="col-md-12">
                <button class='btn btn-info' id='btnNewRole'><i class="fas fa-plus"></i>&nbsp;New Role</button>
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
                        @foreach ($roles as $key => $role)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->created_at }}</td>
                                <td>
                                    @if ($role->name != 'admin')
                                        <button class='btn btn-info btn-xs' onclick='EditRole({{ $role->id }})'>Edit</button>
                                        <button class='btn btn-danger btn-xs' onclick='DeleteRole({{ $role->id }})'>delete</button>
                                    @endif
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
@stop
<div class="modal fade" id='modalNewRole'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Role</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="form-group">
                            <label>New Role Name:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-sign"></i>
                                </div>
                                <input type="text" class="form-control" id="inputNewRole">
                            </div>
                            <!-- /.input group -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                <button class="btn btn-info pull-right" id="btnSaveRole">Ok</button>
                </div>
            </div>
            <!--./modal footer-->
        </div>
    </div>
</div>
<script>
    let roleId = -1
    window.addEventListener('load', function() {
        $(() => {
            $('table').DataTable()
        })
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $('#btnNewRole').click(() => {
            roleId = -1
            $('#inputNewRole').focus()
            $('#modalNewRole').modal()
        })
        $('#btnSaveRole').click(() => {
            let roleName = $('#inputNewRole').val()
            if(roleName == '')
            {
                alret('Enter the Correct Name')
                return false
            }
            $.ajax({
                url:'roles',
                type:'post',
                data:'name='+roleName,
                success:() => {
                    location.reload()
                }
            })
        })
    })
    function DeleteRole(id)
    {
        if(confirm('Are you going to Delete this Role?'))
        {
            $.get({
                url:'_delete_role/'+id,
                success:() => {
                    location.reload()
                }
            })
        }
    }
    function EditRole(id)
    {
        location.href = 'edit_role/'+id
    }
</script>
