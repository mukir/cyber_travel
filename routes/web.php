<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminClientController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminSaleController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Referral capture
Route::get('/r/{code}', function ($code) {
    return redirect()->route('jobs.index')->withCookie(cookie('ref', $code, 60*24*30));
})->name('ref');

// Public job list and details
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job:slug}', [JobController::class, 'show'])->name('jobs.show');
// Services aliases
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::post('/applications', [\App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && method_exists($user, 'is_admin') && $user->is_admin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user && method_exists($user, 'is_staff') && $user->is_staff()) {
        return redirect()->route('staff.dashboard');
    }
    return redirect()->route('client.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/client/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/client/biodata', [ClientController::class, 'editBiodata'])->name('client.biodata');
    Route::post('/client/biodata', [ClientController::class, 'storeBiodata'])->name('client.biodata.store');
    Route::get('/client/enquiry', [ClientController::class, 'enquiry'])->name('client.enquiry');
    Route::post('/client/enquiry', [ClientController::class, 'storeEnquiry'])->name('client.enquiry.store');
    Route::get('/client/support', [ClientController::class, 'support'])->name('client.support');
    Route::post('/client/support', [ClientController::class, 'storeSupport'])->name('client.support.store');
    Route::get('/client/support/tickets', [ClientController::class, 'supportTickets'])->name('client.support.tickets');
    Route::get('/client/support/tickets/{ticket}', [ClientController::class, 'showSupportTicket'])->name('client.support.tickets.show');
    Route::get('/client/documents', [ClientController::class, 'documents'])->name('client.documents');
    Route::post('/client/documents', [ClientController::class, 'uploadDocument'])->name('client.documents.upload');
    Route::get('/client/documents/{document}/view', [ClientController::class, 'viewDocument'])->name('client.documents.view');
    Route::delete('/client/documents/{document}', [ClientController::class, 'deleteDocument'])->name('client.documents.delete');
    Route::post('/client/documents/{document}/replace', [ClientController::class, 'replaceDocument'])->name('client.documents.replace');
    Route::post('/client/documents/{document}/note', [ClientController::class, 'updateDocumentNote'])->name('client.documents.note');
    Route::get('/client/applications', [ClientController::class, 'applications'])->name('client.applications');
    Route::post('/client/applications/{booking}/pay', [PaymentController::class, 'payBooking'])->name('client.applications.pay');
    Route::post('/client/applications/{booking}/verify', [PaymentController::class, 'verifyBooking'])->name('client.applications.verify');
    Route::get('/client/applications/{booking}/checkout', [PaymentController::class, 'checkout'])->name('client.applications.checkout');
    Route::post('/client/applications/{booking}/paypal-complete', [PaymentController::class, 'paypalComplete'])->name('client.applications.paypalComplete');
    Route::get('/client/applications/{booking}/invoice', [PaymentController::class, 'invoice'])->name('client.applications.invoice');
    Route::get('/client/applications/{booking}/receipt', [PaymentController::class, 'receipt'])->name('client.applications.receipt');

    // Polling endpoint for M-Pesa STK status by CheckoutRequestID (reference)
    Route::get('/payments/mpesa/status/{ref}', [PaymentController::class, 'mpesaStatus'])->name('payments.mpesa.status');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients');
        Route::get('/clients/create', [AdminClientController::class, 'create'])->name('clients.create');
        Route::get('/clients/{client}', [AdminClientController::class, 'show'])->name('clients.show');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [AdminClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('clients.destroy');
        Route::post('/clients/assign', [AdminClientController::class, 'bulkAssign'])->name('clients.assign');
        Route::get('/clients/{client}/documents', [AdminClientController::class, 'documents'])->name('clients.documents');
        Route::get('/clients/{client}/documents/{document}/view', [AdminClientController::class, 'viewDocument'])->name('clients.documents.view');
        Route::post('/clients/{client}/documents/{document}/validate', [AdminClientController::class, 'validateDocument'])->name('clients.documents.validate');

        Route::get('/sales', [AdminSaleController::class, 'index'])->name('sales');
        Route::get('/sales/create', [AdminSaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [AdminSaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}/edit', [AdminSaleController::class, 'edit'])->name('sales.edit');
        Route::put('/sales/{sale}', [AdminSaleController::class, 'update'])->name('sales.update');
        Route::delete('/sales/{sale}', [AdminSaleController::class, 'destroy'])->name('sales.destroy');
        Route::get('/payments', [\App\Http\Controllers\AdminPaymentController::class, 'index'])->name('payments');
        Route::get('/payments/overdue', [\App\Http\Controllers\AdminPaymentController::class, 'overdue'])->name('payments.overdue');
        Route::post('/payments/reminders', [\App\Http\Controllers\AdminPaymentController::class, 'sendReminders'])->name('payments.reminders');
        Route::get('/payments/export/csv', [\App\Http\Controllers\AdminPaymentController::class, 'exportCsv'])->name('payments.export.csv');
        Route::get('/payments/export/pdf', [\App\Http\Controllers\AdminPaymentController::class, 'exportPdf'])->name('payments.export.pdf');

        // Admin manual payment recording / mark as paid
        Route::get('/applications/{booking}/manual-payment', [\App\Http\Controllers\AdminPaymentController::class, 'manualPaymentForm'])->name('applications.manualPayment');
        Route::post('/applications/{booking}/manual-payment', [\App\Http\Controllers\AdminPaymentController::class, 'manualPayment'])->name('applications.manualPayment.store');
        // Settings
        Route::get('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'update'])->name('settings.update');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        // Services management
        Route::resource('services', \App\Http\Controllers\AdminServiceController::class)->only(['index','store','update','destroy'])->names('services');
        // Service categories
        Route::resource('service-categories', \App\Http\Controllers\AdminServiceCategoryController::class)->only(['index','store','update','destroy'])->names('service_categories');
        // Document types management
        Route::resource('document-types', \App\Http\Controllers\AdminDocumentTypeController::class)->only(['index','store','update','destroy'])->names('document_types');
        Route::get('/reports/applications/{period}.{format}', function($period, $format) {
            // Convenience redirect to named routes
            if ($format === 'csv') {
                return app(\App\Http\Controllers\AdminReportController::class)->bookingsCsv($period);
            }
            return app(\App\Http\Controllers\AdminReportController::class)->bookingsPdf($period);
        })->where(['period' => 'daily|weekly|monthly', 'format' => 'csv|pdf'])->name('reports.applications');
        Route::get('/reports/applications/{period}/csv', [AdminController::class, 'reports'])->name('reports.applications.csv');
        Route::get('/reports/applications/{period}/pdf', [AdminController::class, 'reports'])->name('reports.applications.pdf');
        Route::get('/reports/commissions/{period}/csv', [AdminController::class, 'reports'])->name('reports.commissions.csv');
        Route::get('/reports/commissions/{period}/pdf', [AdminController::class, 'reports'])->name('reports.commissions.pdf');
        Route::post('/reports/email', [\App\Http\Controllers\AdminReportController::class, 'emailReport'])->name('reports.email');
        Route::get('/communications', [AdminController::class, 'communications'])->name('communications');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
        Route::get('/notes', [AdminController::class, 'notes'])->name('notes');

        // Staff management
        Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff.index');
        Route::post('/staff/{staff}/toggle', [AdminStaffController::class, 'toggle'])->name('staff.toggle');

        // Jobs management
        Route::resource('jobs', \App\Http\Controllers\AdminJobController::class)->except(['show']);
        // Leads management
        Route::resource('leads', \App\Http\Controllers\AdminLeadController::class)->except(['show'])->names('leads');
        // Sales targets management
        Route::resource('targets', \App\Http\Controllers\AdminTargetController::class)->except(['show'])->names('targets');
        // Job packages (minimal inline management)
        Route::post('/jobs/{job}/packages', [\App\Http\Controllers\AdminJobPackageController::class, 'store'])->name('jobs.packages.store');
        Route::put('/jobs/{job}/packages/{package}', [\App\Http\Controllers\AdminJobPackageController::class, 'update'])->name('jobs.packages.update');
        Route::delete('/jobs/{job}/packages/{package}', [\App\Http\Controllers\AdminJobPackageController::class, 'destroy'])->name('jobs.packages.destroy');
    });

    Route::middleware('staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        Route::match(['get','post'],'/leads', [StaffController::class, 'leads'])->name('leads');
        Route::get('/notes', [StaffController::class, 'notes'])->name('notes');
        Route::get('/reminders', [StaffController::class, 'reminders'])->name('reminders');
        Route::post('/leads/{lead}/note', [StaffController::class, 'saveLeadNote'])->name('leads.note');
        Route::get('/commissions', [StaffController::class, 'commissions'])->name('commissions');
        Route::get('/reports', [StaffController::class, 'reports'])->name('reports');
        Route::get('/reports/commissions.csv', [StaffController::class, 'commissionsCsv'])->name('reports.commissions.csv');
        Route::get('/reports/commissions.pdf', [StaffController::class, 'commissionsPdf'])->name('reports.commissions.pdf');
        Route::get('/conversions', [StaffController::class, 'conversions'])->name('conversions');
        Route::get('/payments', [StaffController::class, 'payments'])->name('payments');
        Route::get('/targets', [StaffController::class, 'targets'])->name('targets');
        Route::get('/referrals', [StaffController::class, 'referrals'])->name('referrals');
        Route::get('/clients', [StaffController::class, 'clients'])->name('clients');
        Route::get('/clients/{client}', [StaffController::class, 'showClient'])->name('clients.show');
    });
});

require __DIR__.'/auth.php';

// Public endpoint for Safaricom callback (should not require auth)
Route::post('/payments/mpesa/stk/callback', [PaymentController::class, 'stkCallback'])->name('payments.mpesa.stk.callback');
