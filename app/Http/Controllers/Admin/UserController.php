<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FdPrice;
use App\Models\Paymentsys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $statusFilter = $request->query('status_filter', '');
        $action = $request->query('action');
        $id = $request->query('id');

        $userToEdit = null;
        if ($action === 'edit' && $id) {
            $userToEdit = User::find($id);
        }

        $totalUsers = User::count();
        $activeUsers = User::where('status', 'Active')->count();
        $blockedUsers = User::where('status', 'Block')->count();
        $inactiveUsers = $totalUsers - $activeUsers - $blockedUsers;

        $packages = FdPrice::orderBy('id', 'asc')->get();

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $users = $query->orderBy('id', 'desc')->get();

        // Fetch payments for client-side modal calculations grouped by token
        $userPayments = Paymentsys::orderBy('date_time', 'desc')->get()->groupBy('token');

        return view('admin.users.index', compact(
            'users',
            'userToEdit',
            'action',
            'search',
            'statusFilter',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'blockedUsers',
            'packages',
            'userPayments'
        ));
    }

    public function handleAction(Request $request)
    {
        $action = $request->input('action');
        $id = $request->input('id');

        switch ($action) {
            case 'add':
                $request->validate([
                    'name' => 'required|string|max:255',
                    'mobile' => 'nullable|string|max:50',
                    'email' => 'nullable|email|max:255',
                ]);

                $token = bin2hex(random_bytes(20));
                $rawPassword = $request->input('password') ?: '123456';

                User::create([
                    'token'    => $token,
                    'name'     => $request->input('name'),
                    'mobile'   => $request->input('mobile'),
                    'email'    => $request->input('email'),
                    'password' => Hash::make($rawPassword),
                    'status'   => $request->input('status', 'Active'),
                ]);

                return redirect()->route('admin.users.index', $request->query())->with('flash', [
                    'type' => 'success',
                    'msg'  => 'User added successfully.'
                ]);

            case 'edit':
                if (!$id) {
                    return redirect()->route('admin.users.index')->with('flash', ['type' => 'danger', 'msg' => 'Invalid user ID.']);
                }

                $user = User::findOrFail($id);

                $request->validate([
                    'name' => 'required|string|max:255',
                    'mobile' => 'nullable|string|max:50',
                    'email' => 'nullable|email|max:255',
                ]);

                $user->name = $request->input('name');
                $user->mobile = $request->input('mobile');
                $user->email = $request->input('email');

                if ($user->id != 1) {
                    $user->status = $request->input('status', 'Active');
                }

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->input('password'));
                }

                $user->save();

                return redirect()->route('admin.users.index', $request->query())->with('flash', [
                    'type' => 'success',
                    'msg'  => 'User updated successfully.'
                ]);

            case 'delete_user':
                if ($id && $id != 1) {
                    User::where('id', $id)->delete();
                    return redirect()->route('admin.users.index', $request->query())->with('flash', [
                        'type' => 'success',
                        'msg'  => 'User deleted successfully.'
                    ]);
                }
                return redirect()->route('admin.users.index', $request->query())->with('flash', [
                    'type' => 'danger',
                    'msg'  => 'Cannot delete the main administrator.'
                ]);

            case 'toggle_status':
                if ($id && $id != 1) {
                    $user = User::findOrFail($id);
                    $newStatus = ($user->status === 'Active') ? 'Deactive' : 'Active';
                    $user->status = $newStatus;
                    $user->save();

                    return redirect()->route('admin.users.index', $request->query())->with('flash', [
                        'type' => 'success',
                        'msg'  => 'User status updated to ' . $newStatus
                    ]);
                }
                return redirect()->route('admin.users.index', $request->query())->with('flash', [
                    'type' => 'danger',
                    'msg'  => 'Cannot change status of main administrator.'
                ]);

            case 'assign_package':
                if (!$id) {
                    return redirect()->route('admin.users.index')->with('flash', ['type' => 'danger', 'msg' => 'Invalid user ID.']);
                }

                $user = User::findOrFail($id);
                $selectedItems = $request->input('selected_items', []);
                $paidAmount = floatval($request->input('paid_amount', 0));

                if (empty($selectedItems)) {
                    return redirect()->route('admin.users.index', $request->query())->with('flash', [
                        'type' => 'warning',
                        'msg'  => 'Please select at least one meal option.'
                    ]);
                }

                $totalPrice = 0;
                foreach ($selectedItems as $item) {
                    $parts = explode('_', $item);
                    if (count($parts) === 2) {
                        $pkgId = intval($parts[0]);
                        $mealType = $parts[1];
                        $pkg = FdPrice::find($pkgId);
                        if ($pkg && isset($pkg->{$mealType})) {
                            $totalPrice += floatval($pkg->{$mealType});
                        }
                    }
                }

                $pkgCount = count($selectedItems);

                Paymentsys::create([
                    'token'     => $user->token,
                    'mobile'    => $user->mobile,
                    'price'     => $totalPrice,
                    'paid'      => $paidAmount,
                    'pkg'       => $pkgCount,
                    'date_time' => Carbon::now(),
                ]);

                $newStatus = ($paidAmount >= $totalPrice) ? 'Active' : 'Payment';
                $user->pkg = $pkgCount;
                $user->status = $newStatus;
                $user->save();

                $pending = $totalPrice - $paidAmount;
                if ($newStatus === 'Active') {
                    $msg = 'Package assigned! Fully paid. User status set to Active.';
                    $msgType = 'success';
                } else {
                    $msg = "Package assigned! Pending: ₹" . number_format($pending, 2) . ". Status set to Payment.";
                    $msgType = 'warning';
                }

                return redirect()->route('admin.users.index', $request->query())->with('flash', [
                    'type' => $msgType,
                    'msg'  => $msg
                ]);

            case 'add_payment':
                if (!$id) {
                    return redirect()->route('admin.users.index')->with('flash', ['type' => 'danger', 'msg' => 'Invalid user ID.']);
                }

                $user = User::findOrFail($id);
                $paidAmount = floatval($request->input('paid_amount', 0));
                $selectedMonth = $request->input('payment_month', date('Y-m'));

                if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
                    $selectedMonth = date('Y-m');
                }

                if ($paidAmount <= 0) {
                    return redirect()->route('admin.users.index', $request->query())->with('flash', [
                        'type' => 'warning',
                        'msg'  => 'Invalid payment amount.'
                    ]);
                }

                // Check payments for that user and month
                $existing = Paymentsys::where('token', $user->token)
                    ->whereRaw("DATE_FORMAT(date_time, '%Y-%m') = ?", [$selectedMonth])
                    ->first();

                if ($existing) {
                    $price = $existing->price;
                    $pkg = $existing->pkg;
                } else {
                    $latest = Paymentsys::where('token', $user->token)
                        ->orderBy('date_time', 'desc')
                        ->first();
                    if ($latest) {
                        $price = $latest->price;
                        $pkg = $latest->pkg;
                    } else {
                        return redirect()->route('admin.users.index', $request->query())->with('flash', [
                            'type' => 'warning',
                            'msg'  => 'No previous package found for this user.'
                        ]);
                    }
                }

                $paymentDate = $selectedMonth . '-01 00:00:00';

                Paymentsys::create([
                    'token'     => $user->token,
                    'mobile'    => $user->mobile,
                    'price'     => $price,
                    'paid'      => $paidAmount,
                    'pkg'       => $pkg,
                    'date_time' => $paymentDate,
                ]);

                $totalPaid = Paymentsys::where('token', $user->token)
                    ->whereRaw("DATE_FORMAT(date_time, '%Y-%m') = ?", [$selectedMonth])
                    ->sum('paid');

                $pending = $price - $totalPaid;

                if ($pending <= 0 && $user->status !== 'Active') {
                    $user->status = 'Active';
                    $user->save();
                    $msg = "Payment added for {$selectedMonth}. Month fully paid, user activated.";
                } else {
                    $msg = "Payment added for {$selectedMonth}. Pending: ₹" . number_format($pending, 2);
                }

                return redirect()->route('admin.users.index', $request->query())->with('flash', [
                    'type' => 'success',
                    'msg'  => $msg
                ]);

            default:
                return redirect()->route('admin.users.index');
        }
    }
}
