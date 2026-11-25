<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $usersCount = 0;
        $recentUsers = collect();
        try {
            $usersCount = User::count();
            $recentUsers = User::latest()->take(6)->get();
        } catch (\Throwable $e) {
            // DB hali migrate qilinmagan bo'lsa ham admin panel xatosiz ochiladi
        }

        return view('admin.dashboard', [
            'usersCount' => $usersCount,
            'recentUsers' => $recentUsers,
        ]);
    }
}
