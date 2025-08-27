<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
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
});

require __DIR__.'/auth.php';
