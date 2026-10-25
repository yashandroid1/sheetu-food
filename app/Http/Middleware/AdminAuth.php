<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\AdminUser;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized admin access.'], 401);
            }
            return redirect()->route('admin.login')->with('flash', [
                'type' => 'danger',
                'msg'  => 'Please sign in to access the Admin Panel.'
            ]);
        }

        // Fetch latest admin data to verify user still exists and check payment priority flags
        $adminId = session('admin_id');
        $admin = AdminUser::find($adminId);

        if (!$admin) {
            session()->forget(['admin_id', 'admin_username', 'admin_name']);
            return redirect()->route('admin.login')->with('flash', [
                'type' => 'danger',
                'msg'  => 'Admin account not found or has been revoked.'
            ]);
        }

        // Share current admin and payment flags with all admin views
        View::share('currentAdmin', $admin);
        View::share('showHighPayment', (bool) $admin->payment_h);
        View::share('showLowPayment', (bool) $admin->payment_l);

        return $next($request);
    }
}
