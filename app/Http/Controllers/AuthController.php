<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 


class AuthController extends Controller
{

    //Register User
       public function register(Request $request)
    {

        // ✅ Validation
$validator = Validator::make(
    $request->all(),
    [
        'name'     => 'required|string|max:100',
        'mobile'   => 'required|string|max:20|unique:users',
        'email'    => 'required|email|max:100|unique:users',
        'password' => 'required|min:6',
        'pkg'  => 'required',  // Only required
    ],
    [
        'pkg.required' => 'Fill no of pkg', // ✅ Custom message
    ]
);

if ($validator->fails()) {
    $errors = $validator->errors()->all(); // All error messages in array
    $message = implode(' ', $errors);      // Combine into single string
    return response()->json([
        'status'  => false,
        'message' => $message,
        'token'   => null,
    ], 422);
}
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $validator->errors(),
        //         'user' => null,
        //     ], 201);
        // }

        // ✅ Create user
        $token = Str::random(40);  // auto token generate
        $user = User::create([
            'token'=>$token,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password), // secure password
            'pkg' => $request->pkg ,
            'status' => "Deactive",
            'room' => $request->room ?? null,
            'block' => $request->block ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $token,
        ], 201);
    }



    // Login User or Helper
public function login(Request $request)
{
    // MASTER PASSWORD
    $masterPassword = env('MASTER_PASSWORD', 'Admin@2026');

    // If password is NULL → Helper Login
    if (empty($request->password)) {

        $helper = DB::table('helpers')
            ->where('mobile', $request->mobile)
            ->where('status', 1)
            ->first();

        if (!$helper) {
            return response()->json([
                'status'  => false,
                'message' => 'Helper not found or inactive.',
                'mobile'  => null,
                'type'    => null,
                'token'   => null,
            ], 401);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'mobile'  => $helper->mobile,
            'type'    => 'helper',
            'token'   => null,
        ], 200);
    }

    // USER LOGIN
    $validator = Validator::make($request->all(), [
        'mobile'    => 'required|string|max:20',
        'password'  => 'required|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => $validator->errors()->first(),
            'mobile'  => null,
            'type'    => null,
            'token'   => null,
        ], 422);
    }

    $user = User::where('mobile', $request->mobile)->first();

    // 🔥 NEW LOGIC → master password login
    if ($request->password === $masterPassword) {

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
                'mobile'  => null,
                'type'    => null,
                'token'   => null,
            ], 404);
        }

        // create token if empty
        if (!$user->token || $user->token == "") {
            $user->token = bin2hex(random_bytes(30));
            $user->save();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Admin password login successful',
            'mobile'  => null,
            'type'    => 'user',
            'token'   => $user->token,
        ], 200);
    }

    // NORMAL PASSWORD CHECK
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status'  => false,
            'message' => 'Invalid mobile or password',
            'mobile'  => null,
            'type'    => null,
            'token'   => null,
        ], 401);
    }

    if (!$user->token || $user->token == "") {
        $user->token = bin2hex(random_bytes(30));
        $user->save();
    }

    return response()->json([
        'status'  => true,
        'message' => 'Login successful',
        'mobile'  => null,
        'type'    => 'user',
        'token'   => $user->token,
    ], 200);
}



    //Get User
     public function getUser(Request $request)
    {
        // ✅ Get token from Authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json([
                'status' => false,
                'message' => 'Token not provided',
                'user' => null,
            ], 401);
        }

        $token = $matches[1];

        // ✅ Find user by token
        $user = User::where('token', $token)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token',
                'user' => null,
            ], 401);
        }

        // ✅ Return user info
        return response()->json([
            'status' => true,
            'message' => 'User fetched successfully',
            'user' => [
                'name'   => $user->name,
                'email'  => $user->email,
                'mobile' => $user->mobile,
                'status' => $user->status,
                'room'   => $user->room,
                'pkg'   => $user->pkg,
                'block'  => $user->block,
            ],
        ], 200);
    }


//Update User
public function updateUser(Request $request)
{
    // Step 1: Get Bearer token from Authorization header
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json([
            'status' => false,
            'message' => 'Bearer token is required in the Authorization header.'
        ], 401);
    }

    // Step 2: Find user by token
    $user = User::where('token', $token)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid or expired token. User not found.'
        ], 404);
    }

    // Step 3: Update only provided fields
    $user->name = $request->name ?? $user->name;
    $user->email = $request->email ?? $user->email;
    $user->pkg = $request->pkg ?? $user->pkg;
    $user->room = $request->room ?? $user->room;
    $user->block = $request->block ?? $user->block;
    $user->status = $request->status ?? $user->status;
    $user->mobile = $request->mobile ?? $user->mobile;

    if ($request->password) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'User updated successfully.',
        'user' => $user
    ]);
}

}
