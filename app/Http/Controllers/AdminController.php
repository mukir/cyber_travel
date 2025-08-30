<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function dashboard()
    {
        $clients = \App\Models\User::where('role', \App\Enums\UserRole::Client)->count();
        $pendingDocs = \App\Models\ClientDocument::where('validated', false)->count();
        $bookingsPending = \App\Models\Booking::where('status', 'pending')->count();
        $paymentsToday = \App\Models\Payment::whereDate('created_at', today())->sum('amount');
        $paymentsMonth = \App\Models\Payment::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');

        // Simple timeseries last 7 days payments
        $daily = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $sum = \App\Models\Payment::whereDate('created_at', $date)->sum('amount');
            $daily[] = ['date' => $date, 'amount' => (float)$sum];
        }

        return view('admin.dashboard', compact('clients', 'pendingDocs', 'bookingsPending', 'paymentsToday', 'paymentsMonth', 'daily'));
    }

    public function payments()
    {
        return redirect()->route('admin.payments');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function communications()
    {
        return view('admin.communications');
    }

    public function logs()
    {
        return view('admin.logs');
    }

    public function notes()
    {
        return view('admin.notes');
    }
}
