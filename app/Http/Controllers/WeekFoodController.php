<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeekFood;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class WeekFoodController extends Controller
{
    public function weekFood(Request $request)
    {
        // 1️⃣ Token auth
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Bearer token is required in Authorization header.',
                'day' => null,
                'week' => null
            ], 401);
        }

        // 2️⃣ Validate form-data
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:15',
            'type'   => 'required|string|in:all,specific',
        ]);

        if ($validator->fails()) {
            $errorMessages = collect($validator->errors()->all())->implode(', ');
            return response()->json([
                'status' => false,
                'message' => $errorMessages ?: 'Validation failed.',
                'day' => null,
                'week' => null
            ], 422);
        }

        // 3️⃣ Check user by token + mobile
        $user = User::where('token', $token)
                    ->where('mobile', $request->mobile)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token or mobile does not belong to this user.',
                'day' => null,
                'week' => null
            ], 403);
        }

        // 4️⃣ Fetch data according to type
        $currentDay = Carbon::now()->format('l'); // Monday, Tuesday, etc.

        if ($request->type === 'all') {
            $weekData = WeekFood::select('day', 'brkf', 'luh', 'dinr')->get();

            return response()->json([
                'status' => true,
                'message' => 'All week meal details fetched successfully.',
                'day' => null,
                'week' => $weekData
            ]);
        } else {
            $dayData = WeekFood::where('day', $currentDay)
                                ->select('day', 'brkf', 'luh', 'dinr')
                                ->first();

            return response()->json([
                'status' => true,
                'message' => 'Specific day meal details fetched successfully.',
                'day' => $dayData,
                'week' => null
            ]);
        }
    }
}
