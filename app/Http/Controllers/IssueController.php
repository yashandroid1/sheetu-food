<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;   // ✅ Add this
use App\Models\User;
use App\Models\Issues;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class IssueController extends Controller
{
     public function issuerequest(Request $request)
{
    try {
        // 1️⃣ Get Bearer Token
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: No token provided.'
            ], 401);
        }

        // 2️⃣ Validate Token (Check user)
        $user = User::where('token', $token)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Invalid token.'
            ], 401);
        }

        // 3️⃣ Validation manually (to customize error message)
        $validator = \Validator::make($request->all(), [
            'mobile' => 'required|string',
            'type' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            $missingFields = implode(', ', array_keys($validator->errors()->toArray()));
            return response()->json([
                'status' => false,
                'message' => "The {$missingFields} field is required.",
            ], 422);
        }

        // 4️⃣ Check mobile and token match
        if ($user->mobile !== $request->mobile) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number does not match authenticated user.'
            ], 403);
        }

        // 5️⃣ Create issue request
        $issue = Issues::create([
            'token' => $token,
            'mobile' => $request->mobile,
            'type' => $request->type,
            'description' => $request->description,
            'status' => 'Pending',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        // 6️⃣ Success Response
        return response()->json([
            'status' => true,
            'message' => 'Issue request submitted successfully.',
            'data' => $issue
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function getIssues(Request $request): JsonResponse
{
    try {
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

        // 3️⃣ Fetch issues for this token (latest first)
        $issues = Issues::where('token', $token)
            ->orderBy('id', 'desc')
            ->get(['id', 'mobile', 'type', 'description', 'status', 'created_at']);

        // 4️⃣ Handle no records
        if ($issues->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No issue requests found for this user.',
                'issues' => []
            ], 200);
        }

        // 5️⃣ Success response
        return response()->json([
            'status' => true,
            'message' => 'Issue requests retrieved successfully.',
            'issues' => $issues
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



//Delete Issue
public function deleteIssue(Request $request)
{
    try {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['status'=>false,'message'=>'No token provided'],401);
        }

        $user = User::where('token',$token)->first();
        if(!$user){
            return response()->json(['status'=>false,'message'=>'Invalid token'],401);
        }

        $request->validate([
            'id'=>'required|integer',
        ]);

        $issue = Issues::where('id',$request->id)->first();

        if(!$issue){
            return response()->json(['status'=>false,'message'=>'Issue not found'],404);
        }

        $issue->delete(); // timestamps false doesn't affect this

        return response()->json(['status'=>true,'message'=>'Issue deleted successfully']);

    } catch(\Exception $e){
        return response()->json(['status'=>false,'message'=>'Something went wrong','error'=>$e->getMessage()],500);
    }
}

}
