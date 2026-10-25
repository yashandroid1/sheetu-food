<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paymentsys;
use App\Models\FdDistribute;
use App\Models\Issues;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $counts = [
            'users'             => User::count(),
            'active'            => User::where('status', 'Active')->count(),
            'payments'          => Paymentsys::count(),
            'payments_sum'      => (float) Paymentsys::sum('price'),
            'distributes'       => FdDistribute::count(),
            'issues'            => Issues::count(),
            'today_distributes' => FdDistribute::where('date', $today)->count(),
        ];

        $recentUsers = User::orderBy('id', 'desc')->limit(5)->get();
        $recentPayments = Paymentsys::orderBy('date_time', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('counts', 'recentUsers', 'recentPayments'));
    }
}
