<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send follow-up reminders to sales staff for due leads
Artisan::command('sales:remind-leads', function () {
    $today = today();
    $leads = \App\Models\Lead::with('salesRep')
        ->whereNotNull('next_follow_up')
        ->whereDate('next_follow_up', '<=', $today)
        ->orderBy('next_follow_up')
        ->get()
        ->groupBy('sales_rep_id');

    foreach ($leads as $staffId => $items) {
        $staff = optional($items->first())->salesRep;
        if (!$staff || !$staff->email) continue;

        $lines = $items->map(function ($l) {
            return sprintf('%s | %s | %s | Next: %s', $l->name, $l->email ?: '-', $l->phone ?: '-', optional($l->next_follow_up)->format('Y-m-d'));
        })->implode("\n");

        \Illuminate\Support\Facades\Mail::raw(
            "You have ".$items->count()." lead(s) due for follow-up today:\n\n".$lines,
            function ($m) use ($staff) {
                $m->to($staff->email)->subject('Lead Follow-up Reminders');
            }
        );

        $this->info('Reminder sent to '.$staff->email.' ('.$items->count().' leads)');
    }
})->purpose('Email sales staff about due lead follow-ups');

// Weekly passport bonus: for every 3 fully-paid clients with validated passport submitted this week per staff, pay 5000 KES
Artisan::command('sales:weekly-passport-bonus', function (?string $weekStart = null) {
    $start = $weekStart ? now()->parse($weekStart)->startOfWeek() : now()->startOfWeek();
    $end = (clone $start)->endOfWeek();

    $staffUsers = \App\Models\User::whereIn('role', ['staff', \App\Enums\UserRole::Staff->value])->get();

    foreach ($staffUsers as $staff) {
        // Qualifying client users for this staff in the week
        $clientIds = DB::table('bookings')
            ->where('referred_by_id', $staff->id)
            ->where(function($q){ $q->where('payment_status','full')->orWhere('status','paid'); })
            ->whereBetween('paid_at', [$start, $end])
            ->pluck('user_id')
            ->unique()
            ->filter();

        if ($clientIds->isEmpty()) continue;

        // Clients with validated passport in the same week
        $passportClients = DB::table('client_documents')
            ->whereIn('user_id', $clientIds)
            ->where('type', 'passport')
            ->where('validated', 1)
            ->whereBetween('created_at', [$start, $end])
            ->pluck('user_id')
            ->unique();

        $count = $passportClients->count();
        $blocks = intdiv($count, 3);
        if ($blocks <= 0) continue;

        $unit = (float) \App\Helpers\Settings::get('commission.bonus.passport_weekly', 5000);
        $amount = $unit * $blocks;

        // Avoid duplicate bonus for same staff within this week
        $already = \App\Models\Commission::where('staff_id', $staff->id)
            ->where('type', 'passport_weekly_bonus')
            ->whereBetween('created_at', [$start, $end])
            ->exists();
        if ($already) {
            $this->info("Bonus already exists for {$staff->name} this week");
            continue;
        }

        // Link to a recent paid payment for this staff within the range
        $paymentId = DB::table('payments')
            ->join('bookings','payments.booking_id','=','bookings.id')
            ->where('payments.status','paid')
            ->where('bookings.referred_by_id', $staff->id)
            ->whereBetween('payments.created_at', [$start, $end])
            ->orderByDesc('payments.id')
            ->value('payments.id');
        if (!$paymentId) {
            // fallback: any paid payment for referred bookings
            $paymentId = DB::table('payments')
                ->join('bookings','payments.booking_id','=','bookings.id')
                ->where('payments.status','paid')
                ->where('bookings.referred_by_id', $staff->id)
                ->orderByDesc('payments.id')
                ->value('payments.id');
        }
        if (!$paymentId) {
            $this->warn("No payment reference to attach bonus for {$staff->name}, skipping.");
            continue;
        }

        \App\Models\Commission::create([
            'payment_id' => $paymentId,
            'staff_id' => $staff->id,
            'rate' => 0,
            'amount' => $amount,
            'type' => 'passport_weekly_bonus',
        ]);

        $this->info("Awarded KES {$amount} bonus to {$staff->name} for {$count} passports in week starting {$start->toDateString()}");
    }
})->purpose('Award weekly passport bonus to staff');
