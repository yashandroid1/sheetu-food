<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paymentsys;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class PymntController extends Controller
{
    //Paid User
public function paidUser(Request $request)
{
    // Step 1: Get Bearer Token from header
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Bearer token is required in Authorization header.',
            'paid' => null
        ], 401);
    }

    // Step 2: Validate form fields first
    $validator = Validator::make($request->all(), [
        'mobile'    => 'required|string|max:15',
        'price'     => 'nullable|numeric|min:0', // made optional
        'paid'      => 'required|numeric|min:0',
        'pkg'       => 'nullable|string|max:50', // made optional
        'date_time' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        $errorMessages = collect($validator->errors()->all())->implode(', ');

        return response()->json([
            'status' => false,
            'message' => $errorMessages ?: 'Validation failed.',
            'paid' => null
        ], 422);
    }

    // Step 3: Check token + mobile
    $user = User::where('token', $token)
                ->where('mobile', $request->mobile)
                ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid token or mobile number does not belong to this user.',
            'paid' => null
        ], 403);
    }

    // Step 4: Handle pkg = null or missing (auto-detect)
    $pkg = $request->pkg;
    $price = $request->price;

    if ($pkg === "null" || $pkg === null || $pkg === "" || $price === null) {
        $checkDate = $request->date_time ? \Carbon\Carbon::parse($request->date_time) : now();
        $month = $checkDate->format('m');
        $year = $checkDate->format('Y');

        // Step 4A: Check if there is any transaction in the selected month
        $monthlyPayment = Paymentsys::where('mobile', $request->mobile)
            ->whereYear('date_time', $year)
            ->whereMonth('date_time', $month)
            ->orderBy('date_time', 'desc')
            ->first();

        // Step 4B: If no transaction found in that month → return message
        if (!$monthlyPayment) {
            return response()->json([
                'status' => false,
                'message' => 'No transaction found for the selected date month.',
                'paid' => null
            ], 404);
        }

        // Step 4C: Assign previous pkg and price
        $pkg = $monthlyPayment->pkg;
        $price = $monthlyPayment->price;
    }

    // Step 5: Create Payment
    $payment = new Paymentsys();
    $payment->token = $token;
    $payment->mobile = $request->mobile;
    $payment->price = $price;
    $payment->paid = $request->paid;
    $payment->pkg = $pkg;
    $payment->date_time = $request->date_time ?? now();
    $payment->save();

    // Step 6: Response
    return response()->json([
        'status' => true,
        'message' => 'Payment recorded successfully.',
        'paid' => [
            'id'        => $payment->id,
            'token'     => $payment->token,
            'mobile'    => $payment->mobile,
            'price'     => $payment->price,
            'paid'      => $payment->paid,
            'pkg'       => $payment->pkg,
            'date_time' => $payment->date_time,
        ]
    ], 201);
}


//
public function paymentCheck(Request $request)
{
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Bearer token is required in Authorization header.',
            'data' => null,
        ], 401);
    }

    $validator = Validator::make($request->all(), [
        'mobile' => 'required|string|max:15',
        'date' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'data' => null,
        ], 422);
    }

    $user = User::where('token', $token)
        ->where('mobile', $request->mobile)
        ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid token or mobile number does not belong to this user.',
            'data' => null,
        ], 403);
    }

    // ✅ Get all payments for this user
    $allPayments = Paymentsys::where('mobile', $request->mobile)
        ->orderBy('date_time', 'asc')
        ->get(['mobile', 'price', 'paid', 'pkg', 'date_time']);

    if ($allPayments->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No payment data found.',
            'data' => null,
        ], 404);
    }

    // Group by month (YYYY-MM)
    $grouped = $allPayments->groupBy(function ($item) {
        return \Carbon\Carbon::parse($item->date_time)->format('Y-m');
    });

    $carryOver = 0;
    $foundMonth = null;
    $finalData = [];

    foreach ($grouped as $monthKey => $payments) {
        $monthPrice = (float) $payments->first()->price;
        $monthPaid = (float) $payments->sum('paid');
        $remaining = max(0, ($monthPrice + $carryOver) - $monthPaid);

        $finalData[$monthKey] = [
            'total' => $monthPrice + $carryOver,
            'remaining' => $remaining,
            'status' => $remaining == 0 ? 'paid' : 'partial',
            'payments' => $payments->values(),
        ];

        $carryOver = $remaining;
    }

    // ✅ Determine which month to show
    if ($request->date) {
        $checkDate = \Carbon\Carbon::parse($request->date);
        $targetMonth = $checkDate->format('Y-m');
    } else {
        // ✅ If no date passed, show *last available month’s* data
        $targetMonth = array_key_last($finalData);
    }

    // Find month data
    $foundMonth = $finalData[$targetMonth] ?? null;

    // 🔥 If not found (very rare), show last one anyway
    if (!$foundMonth) {
        $lastMonthKey = array_key_last($finalData);
        $foundMonth = $finalData[$lastMonthKey];
        $targetMonth = $lastMonthKey;
    }

    // Find base purchase date
    $firstPayment = $allPayments->first();
    if ($firstPayment) {
        $baseDate = \Carbon\Carbon::parse($firstPayment->date_time);
        $monthOffset = $grouped->keys()->search($targetMonth);
        $monthOffset = $monthOffset === false ? $grouped->count() : $monthOffset + 1;
        $nextBillingDate = $baseDate->copy()->addMonths($monthOffset)->toDateString();
    } else {
        $nextBillingDate = null;
    }

    return response()->json([
        'status' => true,
        'message' => 'Payment info for the selected month retrieved successfully.',
        'data' => [
            'month' => $targetMonth,
            'total' => $foundMonth['total'],
            'remaining' => $foundMonth['remaining'],
            'status' => $foundMonth['status'],
            'payments' => $foundMonth['payments'],
            'next_billing_date' => $nextBillingDate,
        ],
    ], 200);
}



public function getPayment(Request $request)
{
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Bearer token is required in Authorization header.',
        ], 401);
    }

    $validator = Validator::make($request->all(), [
        'mobile' => 'required|string|max:15',
        'date' => 'nullable|date', // ✅ optional date filter
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    // ✅ Verify user
    $user = User::where('token', $token)
        ->where('mobile', $request->mobile)
        ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid token or mobile number does not belong to this user.',
        ], 403);
    }

    // ✅ Fetch all payments
    $allPayments = Paymentsys::where('mobile', $request->mobile)
        ->orderBy('date_time', 'asc')
        ->get(['mobile', 'price', 'paid', 'pkg', 'date_time']);

    if ($allPayments->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No payment data found.',
        ], 404);
    }

    // ✅ Group by month (YYYY-MM)
    $grouped = $allPayments->groupBy(function ($item) {
        return substr($item->date_time, 0, 7); // faster than Carbon
    });

    $finalData = [];
    $totalRemain = 0;

    foreach ($grouped as $monthKey => $payments) {
        $first = $payments->first();
        $monthPrice = (float) $first->price; // 🧠 take only first entry price
        $monthPaid = (float) $payments->sum('paid');
        $rmain = max(0, $monthPrice - $monthPaid);
        $totalRemain += $rmain;

        $finalData[] = [
            'month' => $monthKey,
            'mobile' => $first->mobile,
            'price' => number_format($monthPrice, 2),
            'total_paid' => number_format($monthPaid, 2),
            'pkg' => $first->pkg,
            'rmain' => number_format($rmain, 2),
            'date_time' => $first->date_time,
            'status_text' => $rmain > 0 ? 'partial' : 'paid',
        ];
    }

    // ✅ If date passed → filter that specific month only
    if ($request->filled('date')) {
        $targetMonth = substr($request->date, 0, 7);
        $filtered = collect($finalData)->where('month', $targetMonth)->values();

        if ($filtered->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No payment found for the selected month.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment data for the selected month retrieved successfully.',
            'payments' => $filtered,
            'total_remaining' => $filtered->first()['rmain'],
        ], 200);
    }

    // ✅ Default → show all months
    return response()->json([
        'status' => true,
        'message' => 'All months payment data retrieved successfully.',
        'payments' => $finalData,
        'total_remaining' => number_format($totalRemain, 2),
    ], 200);
}



}
