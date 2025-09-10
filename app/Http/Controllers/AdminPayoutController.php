<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPayoutController extends Controller
{
    public function index(Request $request)
    {
        $month = (string) ($request->input('month') ?: now()->subMonthNoOverflow()->format('Y-m'));
        [$start, $end] = [now()->parse($month.'-01')->startOfMonth(), now()->parse($month.'-01')->endOfMonth()];

        $pendingRows = Commission::with('staff')
            ->whereBetween('created_at', [$start, $end])
            ->whereNull('paid_at')
            ->get()
            ->groupBy('staff_id');

        $paidRows = Commission::with('staff')
            ->where('payout_month', $month)
            ->get()
            ->groupBy('staff_id');

        $pending = $pendingRows->map(function ($rows) {
            /** @var \Illuminate\Support\Collection $rows */
            return [
                'staff' => optional($rows->first()->staff)->name,
                'amount' => (float) $rows->sum('amount'),
                'count' => (int) $rows->count(),
            ];
        });

        $paid = $paidRows->map(function ($rows) {
            /** @var \Illuminate\Support\Collection $rows */
            return [
                'staff' => optional($rows->first()->staff)->name,
                'amount' => (float) $rows->sum('amount'),
                'count' => (int) $rows->count(),
            ];
        });

        $totals = [
            'pending' => (float) $pending->sum('amount'),
            'paid'    => (float) $paid->sum('amount'),
        ];

        $batches = \App\Models\PayoutBatch::orderByDesc('created_at')->limit(12)->get();
        return view('admin.payouts.index', compact('month','pending','paid','totals','batches'));
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'month' => ['required','date_format:Y-m'],
            'status' => ['required','in:pending,paid'],
            'mode' => ['nullable','in:aggregate,detailed'],
        ]);
        $month = (string) $request->input('month');
        $status = (string) $request->input('status');
        $mode = (string) ($request->input('mode') ?: 'aggregate');
        [$start, $end] = [now()->parse($month.'-01')->startOfMonth(), now()->parse($month.'-01')->endOfMonth()];

        if ($mode === 'aggregate') {
            $q = Commission::query();
            if ($status === 'pending') {
                $q->whereBetween('created_at', [$start, $end])->whereNull('paid_at');
            } else {
                $q->where('payout_month', $month);
            }
            $rows = $q->get()->groupBy('staff_id');

            $out = fopen('php://temp', 'w+');
            fputcsv($out, ['Staff','Month','Count','Total']);
            foreach ($rows as $staffId => $items) {
                $name = optional(User::find($staffId))->name ?: ('#'.$staffId);
                fputcsv($out, [
                    $name,
                    $month,
                    $items->count(),
                    number_format((float) $items->sum('amount'), 2),
                ]);
            }
            rewind($out);
            $csv = stream_get_contents($out);
            fclose($out);
            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="payouts_'.$status.'_'.$month.'.csv"',
            ]);
        }

        // Detailed mode
        $q = Commission::with(['staff','payment.booking']);
        if ($status === 'pending') {
            $q->whereBetween('created_at', [$start, $end])->whereNull('paid_at');
        } else {
            $q->where('payout_month', $month);
        }
        $records = $q->orderBy('staff_id')->orderBy('created_at')->get();

        $out = fopen('php://temp', 'w+');
        fputcsv($out, ['Date','Staff','Type','Booking','Client','Method','Payment','Commission']);
        foreach ($records as $c) {
            $p = $c->payment; $b = optional($p)->booking; $staff = optional($c->staff)->name;
            fputcsv($out, [
                optional($c->created_at)->format('Y-m-d H:i'),
                $staff,
                $c->type,
                $b ? ('BK'.$b->id) : '',
                optional($b)->customer_name,
                optional($p)->method,
                $p ? number_format($p->amount, 2) : '0.00',
                number_format($c->amount, 2),
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payouts_'.$status.'_details_'.$month.'.csv"',
        ]);
    }

    public function markPaid(Request $request)
    {
        $data = $request->validate([
            'month' => ['required','date_format:Y-m'],
            'confirm' => ['required','in:CONFIRM'],
            'notify' => ['nullable','boolean'],
        ]);
        $month = (string) $data['month'];
        $start = now()->parse($month.'-01')->startOfMonth();
        $end   = now()->parse($month.'-01')->endOfMonth();
        $batch = $month;

        // Create retainer commissions for targets ending in the month if achieved
        $retainers = [];
        try {
            $retAmount = (float) \App\Helpers\Settings::get('commission.retainer.amount', 30000);
            $targets = \App\Models\SalesTarget::whereBetween('end_date', [$start, $end])->get();
            foreach ($targets as $t) {
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
            ->get();

        if ($eligible->isEmpty()) {
            return redirect()->route('admin.payouts.index', ['month' => $month])
                ->with('success', 'No pending commissions for '.$batch.'.');
        }

        $totalAmount = (float) $eligible->sum('amount');
        $totalCount = (int) $eligible->count();

        $batchRow = \DB::transaction(function () use ($eligible, $batch, $totalAmount, $totalCount) {
            $row = \App\Models\PayoutBatch::create([
                'month' => $batch,
                'total_amount' => $totalAmount,
                'total_count' => $totalCount,
                'processed_by' => optional(auth()->user())->id,
                'emailed' => false,
            ]);

            \App\Models\Commission::whereIn('id', $eligible->pluck('id'))
                ->update(['paid_at' => now(), 'payout_month' => $batch, 'payout_batch_id' => $row->id]);

            return $row;
        });

        // Optional notify staff by email
        $notified = false;
        if (!empty($data['notify'])) {
            $groups = $eligible->groupBy('staff_id');
            foreach ($groups as $staffId => $items) {
                $staff = \App\Models\User::find($staffId);
                if (!$staff || !$staff->email) continue;
                $sum = (float) $items->sum('amount');
                $cnt = (int) $items->count();
                try {
                    \Illuminate\Support\Facades\Mail::raw(
                        "Dear {$staff->name},\n\nYour commission payout for {$batch} is KES ".number_format($sum, 2)." (".$cnt." item(s)).\n\nThank you.",
                        function ($m) use ($staff, $batch) {
                            $m->to($staff->email)->subject('Commission Payout for '.$batch);
                        }
                    );
                    $notified = true;
                } catch (\Throwable $e) {}
            }

            if ($notified) {
                $batchRow->update(['emailed' => true, 'emailed_at' => now()]);
            }
        }

        // Email admin retainer summary when applicable
        if (!empty($retainers)) {
            try {
                $adminEmail = \App\Helpers\Settings::get('company.admin_email') ?: optional(auth()->user())->email;
                if ($adminEmail) {
                    $lines = array_map(fn($r) => ($r[0].' - KES '.number_format($r[1], 2)), $retainers);
                    \Illuminate\Support\Facades\Mail::raw(
                        "Retainers created for {$batch}:\n\n".implode("\n", $lines),
                        function ($m) use ($adminEmail, $batch) { $m->to($adminEmail)->subject('Retainers Created - '.$batch); }
                    );
                }
            } catch (\Throwable $e) {}
        }

        $msg = $totalCount.' commission(s) marked paid for '.$batch.'.';
        if ($notified) { $msg .= ' Staff statements emailed.'; }

        return redirect()->route('admin.payouts.index', ['month' => $month])
            ->with('success', $msg);
    }
}
