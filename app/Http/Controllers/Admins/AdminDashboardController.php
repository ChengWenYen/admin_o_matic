<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    //
    public function index()
    {
        $now = Carbon::now();
        $ago = $now->subDays(3);
        return Inertia::render('Admins/Dashboard', [
            'users' => User::where('is_admin', 0)->whereDate('created_at', '>', $ago)->count()
        ]);
    }
}
