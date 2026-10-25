<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\IssueController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\WeekFoodController;

// Redirect root to admin panel
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/sheetu-app', function () {
    return view('appdownload');
});


Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Server is alive'
    ], 200);
});

// Admin Module Routes
Route::prefix('admin')->group(function () {
    // Guest Admin Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::match(['get', 'post'], '/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'handleAction'])->name('admin.users.action');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');

        // Distributes
        Route::get('/distributes', [DistributionController::class, 'index'])->name('admin.distributes.index');

        // Issues
        Route::get('/issues', [IssueController::class, 'index'])->name('admin.issues.index');
        Route::post('/issues/update-status', [IssueController::class, 'updateStatus'])->name('admin.issues.update-status');

        // Gallery
        Route::get('/gallery', [GalleryController::class, 'index'])->name('admin.gallery.index');
        Route::post('/gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
        Route::post('/gallery/update', [GalleryController::class, 'update'])->name('admin.gallery.update');
        Route::match(['get', 'post'], '/gallery/delete/{id}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');

        // Week Foods
        Route::get('/weekfoods', [WeekFoodController::class, 'index'])->name('admin.weekfoods.index');
        Route::post('/weekfoods', [WeekFoodController::class, 'handleAction'])->name('admin.weekfoods.action');
    });
});
