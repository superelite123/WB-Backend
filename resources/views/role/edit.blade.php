@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Roles And Roles')
@section('content_header')
    <h1>
        Edit Role
        <small>Role</small>
    </h1>
    <ol class="breadcrumb">
        <li class="active"><a href='{{ url('roles') }}'>Go to Roles</a></li>
    </ol>
@stop

@section('content')
{!! Form::model($role, ['method' => 'PUT', 'route' => ['roles.update',  $role->id ], 'class' => 'm-b']) !!}
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Role Name:{{ $role->name }}</h3>
        <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                @foreach($permissions as $perm)
                    <?php
                        $per_found = null;

                        if( isset($role) ) {
                            $per_found = $role->hasPermissionTo($perm->name);
                        }

                        if( isset($user)) {
                            $per_found = $user->hasDirectPermission($perm->name);
                        }
                    ?>

                    <div class="col-md-3">
                        <div class="checkbox">
                            <label class="{{ str_contains($perm->name, 'delete') ? 'text-danger' : '' }}">
                                {!! Form::checkbox("permissions[]", $perm->name, $per_found, isset($options) ? $options : []) !!} {{ $perm->display_name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    </div>
</div>
{!! Form::close() !!}
@stop
