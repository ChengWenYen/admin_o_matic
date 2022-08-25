<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Permission;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['role:super-admin|admin']);
    }

    public function index()
    {
        return Inertia::render('Admins/Permissions/Index', [
            'permissions' => Permission::latest()->paginate(5)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'max:25', 'unique:permissions'],
            'description' => ['required', 'max:25'],
        ]);
        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => 'web'
        ]);
        return back();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $this->validate($request, [
            'name' => ['required', 'max:25'],
            'description' => ['required', 'max:25'],
        ]);
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return back();
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back();
    }
}
