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

// Monthly commission payout: run on the 15th to pay previous month's commissions
Artisan::command('commissions:payout {--date=} {--dry}', function () {
    $dateOpt = (string) ($this->option('date') ?? '');
    $runDate = $dateOpt ? now()->parse($dateOpt) : now();
    $payoutDay = (int) \App\Helpers\Settings::get('commission.payout.day', 15);

    $start = (clone $runDate)->subMonthNoOverflow()->startOfMonth();
    $end   = (clone $runDate)->subMonthNoOverflow()->endOfMonth();
    $batch = $start->format('Y-m');

    // Ensure monthly retainer commissions exist for staff who hit targets ending in the month
    $retainers = [];
    try {
        $retAmount = (float) \App\Helpers\Settings::get('commission.retainer.amount', 30000);
        $targets = \App\Models\SalesTarget::whereBetween('end_date', [$start, $end])->get();
        foreach ($targets as $t) {
            // One retainer per staff per month
            $exists = \App\Models\Commission::where('type', 'retainer')
                ->where('staff_id', $t->staff_id)
                ->whereBetween('created_at', [$start, $end])
                ->exists();
            if ($exists) continue;

            $fromT = $t->start_date?->startOfDay() ?: $start;
            $toT   = $t->end_date?->endOfDay() ?: $end;
            $achieved = (float) \App\Models\Payment::where('status','paid')
                ->whereBetween('created_at', [$fromT, $toT])
                ->whereHas('booking', fn($q) => $q->where('referred_by_id', $t->staff_id))
                ->sum('amount');
            if ($achieved + 0.01 < (float) $t->target_amount) continue;

            $pay = \App\Models\Payment::where('status','paid')
                ->whereBetween('created_at', [$fromT, $toT])
                ->whereHas('booking', fn($q) => $q->where('referred_by_id', $t->staff_id))
                ->latest('created_at')->first();
            if (!$pay) continue;

            $c = \App\Models\Commission::create([
                'payment_id' => $pay->id,
                'staff_id' => $t->staff_id,
                'rate' => 0,
                'amount' => $retAmount,
                'type' => 'retainer',
            ]);
            $c->created_at = $toT; $c->updated_at = $toT; $c->save();
            $staffName = optional(\App\Models\User::find($t->staff_id))->name ?: ('#'.$t->staff_id);
            $retainers[] = [$staffName, $retAmount];
        }
    } catch (\Throwable $e) {}

    $eligible = \App\Models\Commission::whereNull('paid_at')
        ->whereBetween('created_at', [$start, $end])
        ->get()
        ->groupBy('staff_id');

    if ($eligible->isEmpty()) {
        $this->info("No unpaid commissions for {$batch}.");
        return 0;
    }

    $this->info("Preparing payout for {$batch} (records: ".$eligible->flatten()->count().").");
    foreach ($eligible as $staffId => $rows) {
        $sum = (float) $rows->sum('amount');
        $name = optional(\App\Models\User::find($staffId))->name ?: ('#'.$staffId);
        $this->line(str_pad($name, 24).' KES '.number_format($sum, 2));
    }

    if ($this->option('dry')) {
        $this->warn('Dry-run only. No changes applied.');
        return 0;
    }

    $now = now();
    $totalAmount = (float) \App\Models\Commission::whereNull('paid_at')->whereBetween('created_at', [$start, $end])->sum('amount');
    $totalCount = (int) \App\Models\Commission::whereNull('paid_at')->whereBetween('created_at', [$start, $end])->count();
    $batchRow = \App\Models\PayoutBatch::create([
        'month' => $batch,
        'total_amount' => $totalAmount,
        'total_count' => $totalCount,
        'processed_by' => null,
        'emailed' => false,
    ]);

    \App\Models\Commission::whereNull('paid_at')
        ->whereBetween('created_at', [$start, $end])
        ->update(['paid_at' => $now, 'payout_month' => $batch, 'payout_batch_id' => $batchRow->id]);

    $this->info('Payout marked paid_at='.$now->toDateTimeString().' for month '.$batch.' (batch #'.$batchRow->id.').');

    // Email admin retainer summary when applicable
    if (!empty($retainers)) {
        try {
            $adminEmail = \App\Helpers\Settings::get('company.admin_email');
            if (!$adminEmail) {
                $adminEmail = optional(\App\Models\User::where('role','admin')->first())->email;
            }
            if ($adminEmail) {
                $lines = array_map(fn($r) => ($r[0].' - KES '.number_format($r[1], 2)), $retainers);
                \Illuminate\Support\Facades\Mail::raw(
                    "Retainers created for {$batch}:\n\n".implode("\n", $lines),
                    function ($m) use ($adminEmail, $batch) { $m->to($adminEmail)->subject('Retainers Created - '.$batch); }
                );
            }
        } catch (\Throwable $e) {}
    }
    return 0;
})->purpose('Mark commissions as paid for the previous month (run on 15th)');
