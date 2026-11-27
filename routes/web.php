<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderVerificationController;
use App\Http\Controllers\Admin\TicketCheckInController;
use App\Http\Controllers\FestivalController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FestivalController::class, 'index'])->name('home');
Route::get('/tickets/{ticket}/validate', [OrderController::class, 'validateTicket'])->name('tickets.validate');
Route::get('/dashboard', function () {
    return auth()->user()->isAdmin() ? redirect()->route('admin.dashboard') : app(OrderController::class)->dashboard();
})->middleware('auth')->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{order}/payment-proof', [OrderController::class, 'uploadProof'])->name('orders.proof');
    Route::get('/orders/{order}/ticket.pdf', [OrderController::class, 'download'])->name('orders.download');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/sales.xlsx', [AdminDashboardController::class, 'export'])->name('export');
    Route::patch('/tiers/{ticketTier}', [AdminDashboardController::class, 'updateTier'])->name('tiers.update');
    Route::get('/orders/{order}/proof', [OrderVerificationController::class, 'proof'])->name('orders.proof');
    Route::post('/orders/{order}/approve', [OrderVerificationController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/reject', [OrderVerificationController::class, 'reject'])->name('orders.reject');
    Route::get('/check-in', [TicketCheckInController::class, 'index'])->name('checkin');
    Route::post('/check-in', [TicketCheckInController::class, 'store'])->name('checkin.store');
});
require __DIR__.'/auth.php';
