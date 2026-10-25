<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FdDistribute;
use App\Models\FdPrice;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class FdDistController extends Controller
{

    //Store food
public function storefood(Request $request)
{
    // 1️⃣ Get token from Authorization header
    $token = $request->bearerToken();
    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized: No token provided.'
        ], 401);
    }

    // 2️⃣ Find user by token
    $user = User::where('token', $token)->first();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized: Invalid token.'
        ], 401);
    }

    // 3️⃣ Validate form-data body using Validator
    $validator = Validator::make($request->all(), [
        'mobile' => 'required|string',
        'pkg' => 'required|numeric',
        'brkf' => 'nullable|boolean',
        'luh' => 'nullable|boolean',
        'dinr' => 'nullable|boolean',
        'date' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        $errorMessages = collect($validator->errors()->all())->implode(', ');
        return response()->json([
            'status' => false,
            'message' => $errorMessages ?: 'Validation failed.',
            'meal' => null
        ], 422);
    }

    // 4️⃣ Mobile must match authenticated user
    if ($user->mobile !== $request->mobile) {
        return response()->json([
            'status' => false,
            'message' => 'Mobile number does not match authenticated user.'
        ], 403);
    }

    // 5️⃣ Use current date if not passed
    $date = $request->date ?? Carbon::now()->toDateString();

    // 6️⃣ Calculate per-day and per-meal price
    $pkg = $request->pkg;
    $daysInMonth = Carbon::now()->daysInMonth;

    // 🔥 mealsPerDay = user table value (prevent division by zero)
    $mealsPerDay = max(1, (int) ($user->pkg ?? 1));

    $oneDayPrice = $pkg / $daysInMonth;
    $mealPrice = $oneDayPrice / $mealsPerDay;

    // 7️⃣ Find existing meal for today
    $meal = FdDistribute::where('token', $token)
        ->where('mobile', $request->mobile)
        ->whereDate('date', $date)
        ->first();

    $fields = ['brkf', 'luh', 'dinr'];
    $amountChange = 0;
    $warnings = '';

    if ($meal) {
        // Update existing record
        $updateData = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $newValue = $request->boolean($field);
                $currentValue = (bool) $meal->$field;

                if ($newValue === true && $currentValue === true) {
                    $warnings = ucfirst($field) . ' is already selected.';
                } elseif ($newValue === true && $currentValue === false) {
                    $amountChange += $mealPrice;
                    $updateData[$field] = true;
                } elseif ($newValue === false && $currentValue === true) {
                    $amountChange -= $mealPrice;
                    $updateData[$field] = false;
                }
            }
        }

        $meal->update(array_merge($updateData, [
            'amount' => max(0, $meal->amount + $amountChange),
            'time' => Carbon::now()->format('H:i:s'),
        ]));

        $msg = $warnings ?: 'Meal record updated successfully.';
    } else {
        // Create new record
        $createData = [
            'token' => $token,
            'mobile' => $request->mobile,
            'name' => $user->name,
            'date' => $date,
            'time' => Carbon::now()->format('H:i:s'),
            'amount' => 0,
        ];

        foreach ($fields as $field) {
            $value = $request->has($field) ? $request->boolean($field) : false;
            $createData[$field] = $value;
            if ($value === true) $createData['amount'] += $mealPrice;
        }

        $meal = FdDistribute::create($createData);
        $msg = 'Meal record created successfully.';
    }

    // Prepare response data without id or token
    $responseData = [
        'mobile' => $meal->mobile,
        'brkf' => $meal->brkf,
        'luh' => $meal->luh,
        'dinr' => $meal->dinr,
        'date' => $meal->date,
        'time' => $meal->time,
        'amount' => $meal->amount,
    ];

    return response()->json([
        'status' => true,
        'message' => $msg,
        'meal' => $responseData,
    ]);
}



public function getfood(Request $request)
{
    // 1️⃣ Get Bearer Token
    $token = $request->bearerToken();
    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized: No token provided.'
        ], 401);
    }

    // 2️⃣ Validate Token (Check User)
    $user = User::where('token', $token)->first();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized: Invalid token.'
        ], 401);
    }

    // 3️⃣ Custom validation for mobile only
    $validator = Validator::make($request->all(), [
        'mobile' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(), // First error only
        ], 422);
    }

    // 4️⃣ Validate Mobile Belongs to Authenticated User
    if ($user->mobile !== $request->mobile) {
        return response()->json([
            'status' => false,
            'message' => 'Mobile number does not match authenticated user.'
        ], 403);
    }

    // 5️⃣ Determine Date Range
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    if (!$startDate && !$endDate) {
        $startDate = Carbon::now()->startOfMonth()->toDateString();
        $endDate = Carbon::now()->endOfMonth()->toDateString();
    } elseif ($startDate && !$endDate) {
        $endDate = Carbon::now()->toDateString();
    } elseif (!$startDate && $endDate) {
        $startDate = Carbon::now()->startOfMonth()->toDateString();
    }

    // 6️⃣ Fetch Data from Database
    $meals = FdDistribute::where('token', $token)
        ->where('mobile', $request->mobile)
        ->whereBetween('date', [$startDate, $endDate])
        ->orderBy('date', 'asc')
        ->get(['mobile', 'brkf', 'luh', 'dinr', 'date', 'time', 'amount']);

    // 7️⃣ Calculate Total Amount
    $totalAmount = $meals->sum('amount');

    if ($meals->isEmpty()) {
        return response()->json([
            'status' => true,
            'message' => 'No records found for the given date range.',
            'meal' => [],
            'total_amount' => 0
        ]);
    }

    // 8️⃣ Return Response
    return response()->json([
        'status' => true,
        'message' => 'Meal data retrieved successfully.',
        'meal' => $meals,
        'total_amount' => round($totalAmount, 2)
    ]);
}

//Helper

public function foodieHelper(Request $request)
{
    // 1️⃣ Custom validation
    $validator = Validator::make($request->all(), [
        'mobile' => 'required|string',
        'meal'   => 'required|string|in:brkf,luh,dinr',
        'date'   => 'nullable|date',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'meal' => [],
        ], 422);
    }

    $helperMobile = $request->mobile;
    $mealField = $request->meal;
    $date = $request->date ?? Carbon::now()->toDateString();

    // 2️⃣ Check helper exists and status = true
    $helper = DB::table('helpers')
        ->where('mobile', $helperMobile)
        ->where('status', 1)
        ->first();

    if (!$helper) {
        return response()->json([
            'status' => false,
            'message' => 'Helper not found or inactive.',
            'meal' => [],
        ]);
    }

    // 3️⃣ Fetch students + JOIN users by token
    $students = FdDistribute::where('fd_distributes.date', $date)
        ->where("fd_distributes.$mealField", true)
        ->join('users', 'users.token', '=', 'fd_distributes.token')
        ->get([
            'fd_distributes.mobile',
            'fd_distributes.brkf',
            'fd_distributes.luh',
            'fd_distributes.dinr',
            'fd_distributes.date',
            'fd_distributes.time',
            'users.name'
        ]);

    // 4️⃣ Handle empty result
    if ($students->isEmpty()) {
        return response()->json([
            'status' => true,
            'message' => ucfirst($mealField) . " data retrieved successfully.",
            'meal' => [],
        ]);
    }

    // 5️⃣ Convert fields
    $students = $students->map(function ($student) {
        return [
            'mobile' => $student->mobile,
            'name'   => $student->name,  // NAME FROM users table
            'brkf'   => (bool) $student->brkf,
            'luh'    => (bool) $student->luh,
            'dinr'   => (bool) $student->dinr,
            'date'   => $student->date,
            'time'   => $student->time,
        ];
    });

    // 6️⃣ Return response
    return response()->json([
        'status' => true,
        'message' => ucfirst($mealField) . ' data retrieved successfully.',
        'meal' => $students,
    ]);
}


 public function getfdPrice(Request $request)
    {
        // 1️⃣ Get Bearer Token
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: No token provided.'
            ], 401);
        }

        // 2️⃣ Validate Token (Check User)
        $user = User::where('token', $token)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Invalid token.'
            ], 401);
        }

        try {
            // 3️⃣ Get latest FD price record
            $price = FdPrice::latest()->first();

            if (!$price) {
                return response()->json([
                    'status' => true,
                    'message' => 'No price data available.',
                    'price' => [],
                ]);
            }

            // 4️⃣ Format Output
            $data = [
                'id'    => $price->id,
                'brkf'  => $price->brkf,
                'luh'   => $price->luh,
                'dinr'  => $price->dinr,
                'date'  => $price->created_at ? Carbon::parse($price->created_at)->format('Y-m-d') : null,
            ];

            // 5️⃣ Success Response
            return response()->json([
                'status'  => true,
                'message' => 'FD price data retrieved successfully.',
                'price'   => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'Price' => []
            ], 500);
        }
    }


}
