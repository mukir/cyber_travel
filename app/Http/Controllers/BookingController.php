<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Job;
use App\Models\JobPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Settings;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'job_id' => ['required', 'exists:service_jobs,id'],
            'job_package_id' => ['required', 'exists:job_packages,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $package = JobPackage::where('id', $data['job_package_id'])
            ->where('job_id', $data['job_id'])
            ->firstOrFail();

        $total = round(((float)$package->price) * (int)$data['quantity'], 2);
        $job = Job::findOrFail($data['job_id']);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'referred_by_id' => optional(Auth::user())->is_staff() ? Auth::id() : (\App\Models\User::where('referral_code', request()->cookie('ref'))->value('id') ?? null),
            'referral_code' => request()->cookie('ref'),
            'job_id' => $data['job_id'],
            'job_package_id' => $data['job_package_id'],
            'quantity' => $data['quantity'],
            'start_date' => $data['start_date'] ?? null,
            'total_amount' => $total,
            'currency' => Settings::get('default_currency', config('app.currency', env('APP_CURRENCY', 'KES'))),
            'status' => 'pending',
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('jobs.show', $job)
            ->with('success', 'Booking placed successfully. Reference #'.$booking->id);
    }
}
