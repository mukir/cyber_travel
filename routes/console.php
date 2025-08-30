<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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
