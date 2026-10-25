<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PymntController;
use App\Http\Controllers\FdDistController;
use App\Http\Controllers\WeekFoodController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\AppStpController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'getUser']);
Route::post('/update-user', [AuthController::class, 'updateUser']);
Route::post('/paid-user', [PymntController::class, 'paidUser']);
Route::post('/paymentcheck', [PymntController::class, 'paymentCheck']);
Route::post('/getPayment', [PymntController::class, 'getPayment']);
Route::post('/store-food', [FdDistController::class, 'storefood']);
Route::post('/get-food', [FdDistController::class, 'getfood']);
Route::post('/foodie-helper', [FdDistController::class, 'foodieHelper']);
Route::get('/fdPrice', [FdDistController::class, 'getfdPrice']);
Route::post('/week-food', [WeekFoodController::class, 'weekFood']);
Route::post('/issue-request', [IssueController::class, 'issuerequest']);
Route::get('/get-issues', [IssueController::class, 'getIssues']);
Route::post('/delete-Issue', [IssueController::class, 'deleteIssue']);
Route::get('/get-app', [AppStpController::class, 'getApp']);