<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super-admin|admin|moderator|deveopler']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admins/Admins/Index', [
            'admins' => User::where('is_admin', 1)->get(),
            'roles' => Role::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if(auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            if (!$request->roles) {
                return back()->withErrors(['roles' => 'The role field is required!']);
            }

            if($request->roles['id'] != 5) {
                $adminRole = Role::where('id', $request->roles['id'])->first();
                $user->syncRoles($adminRole);
            } else {
                $userRole = Role::where('id', 5)->first();
                $user->update(['is_admin' => 0]);
                $user->syncRoles($userRole);
            }
        }
        return back();
    }
}
