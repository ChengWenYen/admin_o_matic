<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super-admin|admin|moderator']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admins/Users/Index', [
            'users' => User::where('is_admin', 0)->paginate(5),
            'roles' => Role::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            $this->validate($request, [
                'name' => ['required', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'is_admin' => 0,
                'password' => Hash::make('password')
            ]);

            $role = Role::where('id', 5)->first();
            $user->syncRoles($role);
        }
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if(auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            $this->validate($request, [
                'name' => ['required', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:50'],
            ]);
            if($request->roles[0] === null) {
                return back()->withErrors(['roles' => 'The role field is required!']);
            }
            if($request->roles[0]['id'] != 5) {
                $adminRole = Role::where('id', $request->roles[0]['id'])->first();
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'is_admin' => 1
                ]);
                $user->syncRoles($adminRole);
            } else {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email
                ]);
            }
            
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            $user->delete();
        }
        return back();
    }
}
