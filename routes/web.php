<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route with authentication
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.updateEmail');
    Route::patch('/profile/update-name', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Post routes
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{post}/like', [PostController::class, 'likePost']);
    Route::post('/posts/{post}/comment', [PostController::class, 'addComment']);

    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index'); // Fetch notifications
        Route::post('/', [NotificationController::class, 'store'])->name('store'); // Store new notification
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread'); // Fetch unread notifications
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead'); // Mark notification as read
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy'); // Delete notification
    });
});

require __DIR__.'/auth.php';
