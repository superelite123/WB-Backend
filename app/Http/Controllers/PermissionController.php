<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //
    public function index()
    {
        $permissions = Permission::all();
        return view('permission.index',['permissions' => $permissions]);
    }

    public function save(Request $request)
    {
        $permission = Permission::find($request->id) != null?Permission::find($request->id):new Permission;
        $permission->display_name = $request->name;
        $permission->guard_name = 'web';
        $permission->save();
    }

    public function delete($id)
    {
        Permission::find($id)->delete();
    }
}
