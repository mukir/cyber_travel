<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/client/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/client/biodata', [ClientController::class, 'editBiodata'])->name('client.biodata');
    Route::post('/client/biodata', [ClientController::class, 'storeBiodata'])->name('client.biodata.store');
    Route::get('/client/documents', [ClientController::class, 'documents'])->name('client.documents');
    Route::post('/client/documents', [ClientController::class, 'uploadDocument'])->name('client.documents.upload');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients');
        Route::get('/clients/create', [AdminClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [AdminClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('clients.destroy');
        Route::get('/clients/{client}/documents', [AdminClientController::class, 'documents'])->name('clients.documents');
        Route::post('/clients/{client}/documents/{document}/validate', [AdminClientController::class, 'validateDocument'])->name('clients.documents.validate');

        Route::get('/sales', [AdminController::class, 'sales'])->name('sales');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/communications', [AdminController::class, 'communications'])->name('communications');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
        Route::get('/notes', [AdminController::class, 'notes'])->name('notes');
    });

    Route::middleware('staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        Route::get('/leads', [StaffController::class, 'leads'])->name('leads');
        Route::get('/notes', [StaffController::class, 'notes'])->name('notes');
        Route::get('/reminders', [StaffController::class, 'reminders'])->name('reminders');
        Route::get('/commissions', [StaffController::class, 'commissions'])->name('commissions');
        Route::get('/reports', [StaffController::class, 'reports'])->name('reports');
        Route::get('/conversions', [StaffController::class, 'conversions'])->name('conversions');
        Route::get('/payments', [StaffController::class, 'payments'])->name('payments');
        Route::get('/targets', [StaffController::class, 'targets'])->name('targets');
        Route::get('/referrals', [StaffController::class, 'referrals'])->name('referrals');
    });
});

require __DIR__.'/auth.php';
