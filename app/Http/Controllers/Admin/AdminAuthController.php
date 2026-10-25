<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        // Ensure default administrator exists if admin_users is empty
        if (AdminUser::count() === 0) {
            AdminUser::create([
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'name'     => 'Administrator',
            ]);
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($request->input('username'));
        $password = $request->input('password');

        $admin = AdminUser::where('username', $username)->first();

        if ($admin && (Hash::check($password, $admin->password) || password_verify($password, $admin->password))) {
            // Rehash with modern Hash if needed
            if (password_needs_rehash($admin->password, PASSWORD_BCRYPT)) {
                $admin->password = Hash::make($password);
                $admin->save();
            }

            session([
                'admin_id'       => $admin->id,
                'admin_username' => $admin->username,
                'admin_name'     => $admin->name,
            ]);

            return redirect()->route('admin.dashboard')->with('flash', [
                'type' => 'success',
                'msg'  => 'Welcome back, ' . ($admin->name ?: $admin->username) . '!'
            ]);
        }

        return back()->withInput($request->only('username'))->with('flash', [
            'type' => 'danger',
            'msg'  => 'Invalid credentials.'
        ]);
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_username', 'admin_name']);
        session()->regenerateToken();

        return redirect()->route('admin.login')->with('flash', [
            'type' => 'success',
            'msg'  => 'You have been successfully logged out.'
        ]);
    }
}
