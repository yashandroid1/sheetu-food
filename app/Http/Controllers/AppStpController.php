<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\AppStp;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class AppStpController extends Controller
{
    public function getApp(Request $request)
{
    // ✅ Authorization Header से Token निकाले
    $authHeader = $request->header('Authorization');

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return response()->json([
            'status' => false,
            'message' => 'Token not provided',
            'app' => null,
        ], 401);
    }

    $token = $matches[1];

    // ✅ Token से Authenticate User ढूँढें
    $user = User::where('token', $token)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid token',
            'app' => null,
        ], 401);
    }

    // ✅ अब app_stp table से डाटा निकालें
    $appData = AppStp::first(); // अगर एक ही row है
    // या user-based data चाहिए तो ->where('user_id',$user->id)->first()

    if (!$appData) {
        return response()->json([
            'status' => false,
            'message' => 'App settings not found',
            'app' => null,
        ], 404);
    }

    // ✅ Success Response
    return response()->json([
        'status' => true,
        'message' => 'App data fetched successfully',
        'app' => [
            'version'        => $appData->version,
            'a_url'        => $appData->a_url,
            'support'        => $appData->support,
            'payment'        => (bool)$appData->payment,
            'a_update'         => (bool)$appData->a_update,
            'payment_high'   => (bool)$appData->payment_high,
            'update_high'    => (bool)$appData->update_high,
        ],
    ], 200);
}
}
