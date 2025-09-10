<?php

namespace App\Http\Controllers;

class StaffController extends Controller
{
    public function clients()
    {
        $user = auth()->user();
        // Join client profiles to allow sorting by latest assignment and searching phone
        $q = trim((string) request('q'));
        $query = \App\Models\User::leftJoin('client_profiles as cp', 'cp.user_id', '=', 'users.id')
            ->where('users.role', \App\Enums\UserRole::Client)
            ->where('cp.sales_rep_id', $user->id)
            ->select('users.*', 'cp.phone as cp_phone', 'cp.updated_at as cp_updated_at');
        if ($q !== '') {
            $digits = preg_replace('/\D+/', '', $q);
            $query->where(function ($w) use ($q, $digits) {
                $w->where('users.name', 'like', '%' . $q . '%')
                  ->orWhere('users.email', 'like', '%' . $q . '%')
                  ->orWhere('cp.phone', 'like', '%' . $digits . '%');
            });
        }
        $clients = $query->orderByDesc('cp_updated_at')->orderByDesc('users.created_at')
            ->paginate(15)->withQueryString();
        // Map phone from profile and latest booking summary
        $profiles = \App\Models\ClientProfile::whereIn('user_id', $clients->pluck('id'))->get()->keyBy('user_id');
        $latestBookings = \App\Models\Booking::with(['job','package'])
            ->whereIn('user_id', $clients->pluck('id'))
            ->select('user_id','id','total_amount','amount_paid','currency','job_id','job_package_id','created_at')
            ->latest()->get()->groupBy('user_id');

        return view('staff.clients', compact('clients','profiles','latestBookings','q'));
    }
    public function dashboard()
    {
        $user = auth()->user();
        $leadsCount = \App\Models\Lead::where('sales_rep_id', $user->id)->count();
        $dueCount = \App\Models\Lead::where('sales_rep_id', $user->id)
            ->whereNotNull('next_follow_up')->whereDate('next_follow_up', '<=', today())->count();
        $commissionMonth = \App\Models\Commission::where('staff_id', $user->id)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');
        $wonCount = \App\Models\Lead::where('sales_rep_id', $user->id)->where('stage', 'won')->count();

        $targetModel = \App\Models\SalesTarget::where('staff_id', $user->id)
            ->where('start_date', '<=', today())->where('end_date', '>=', today())
            ->orderByDesc('start_date')->first();
        $target = optional($targetModel)->target_amount ?: 0;
        $achieved = \App\Models\Payment::whereHas('booking', function($q) use ($user){
            $q->where('referred_by_id', $user->id);
        })->whereBetween('created_at', [optional($targetModel)->start_date ?? now()->startOfMonth(), optional($targetModel)->end_date ?? now()->endOfMonth()])->sum('amount');
        $targetPeriod = $targetModel ? $targetModel->start_date->format('M d').' - '.$targetModel->end_date->format('M d, Y') : now()->startOfMonth()->format('M d').' - '.now()->endOfMonth()->format('M d, Y');

        return view('staff.dashboard', compact('leadsCount','dueCount','commissionMonth','wonCount','target','achieved','targetPeriod'));
    }

    public function leads()
    {
        $user = auth()->user();
        $q = \App\Models\Lead::where('sales_rep_id', $user->id)->orderByDesc('created_at');
        if (request('stage')) $q->where('stage', request('stage'));
        if (request('status')) $q->where('status', request('status'));
        if (request('from')) $q->whereDate('created_at', '>=', now()->parse(request('from'))->startOfDay());
        if (request('to')) $q->whereDate('created_at', '<=', now()->parse(request('to'))->endOfDay());
        if (request('follow_from')) $q->whereDate('next_follow_up', '>=', now()->parse(request('follow_from'))->startOfDay());
        if (request('follow_to')) $q->whereDate('next_follow_up', '<=', now()->parse(request('follow_to'))->endOfDay());

        if (request()->isMethod('post')) {
            $data = request()->validate([
                'name' => 'required|string',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'next_follow_up' => 'nullable|date',
            ]);
            $data['sales_rep_id'] = $user->id;
            \App\Models\Lead::create($data);
            return redirect()->route('staff.leads')->with('success', 'Lead added');
        }

        $leads = $q->paginate(15)->appends(request()->query());
        return view('staff.leads', compact('leads'));
    }

    public function leadShow(\App\Models\Lead $lead)
    {
        $user = auth()->user();
        abort_unless($lead && $lead->sales_rep_id === $user->id, 404);
        $lead->load(['leadNotes' => function($q){ $q->orderByDesc('created_at'); }]);
        return view('staff.lead_show', compact('lead'));
    }

    public function notes()
    {
        return redirect()->route('staff.leads');
    }

    public function reminders()
    {
        $user = auth()->user();
        $leads = \App\Models\Lead::where('sales_rep_id', $user->id)
            ->whereNotNull('next_follow_up')->whereDate('next_follow_up', '<=', today())
            ->orderBy('next_follow_up')->paginate(15);
        return view('staff.reminders', compact('leads'));
    }

    public function commissions()
    {
        $user = auth()->user();
        $base = \App\Models\Payment::where('status', 'paid')
            ->whereHas('booking', function($q) use ($user){ $q->where('referred_by_id', $user->id); });
        $payments = (clone $base)->with('booking')->orderByDesc('created_at')->paginate(15);
        $total = (clone $base)->sum('amount');
        $rate = (float)config('sales.commission_rate', (float)env('SALES_COMMISSION_RATE', 10));
        $commission = (float) \App\Models\Commission::where('staff_id', $user->id)->sum('amount');
        return view('staff.commissions', compact('payments','total','commission','rate'));
    }

    public function reports()
    {
        return view('staff.reports');
    }

    // Export commissions CSV for the authenticated staff
    public function commissionsCsv()
    {
        $user = auth()->user();
        $from = request('from') ? now()->parse(request('from'))->startOfDay() : now()->startOfMonth();
        $to   = request('to') ? now()->parse(request('to'))->endOfDay() : now()->endOfMonth();

        $payments = \App\Models\Payment::with('booking')
            ->where('status', 'paid')
            ->whereHas('booking', fn($q) => $q->where('referred_by_id', $user->id))
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $rateDefault = (float)config('sales.commission_rate', (float)env('SALES_COMMISSION_RATE', 10));

        $out = fopen('php://temp', 'w+');
        fputcsv($out, ['Date','Booking','Client','Method','Amount','Outstanding','Type','Rate %','Commission']);
        foreach ($payments as $p) {
            $comm = \App\Models\Commission::where('payment_id', $p->id)->first();
            $rate = $comm?->rate ?? 0.0;
            $commission = $comm?->amount ?? 0.0;
            $outstanding = 0.0;
            if ($p->booking) {
                $outstanding = max(((float)$p->booking->total_amount) - ((float)$p->booking->amount_paid), 0);
            }
            fputcsv($out, [
                $p->created_at->format('Y-m-d H:i'),
                'BK'.$p->booking_id,
                optional($p->booking)->customer_name,
                $p->method,
                number_format($p->amount, 2),
                number_format($outstanding, 2),
                $comm?->type ?? '',
                $rate,
                number_format($commission, 2),
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="commissions.csv"',
        ]);
    }

    // Export commissions PDF for the authenticated staff
    public function commissionsPdf()
    {
        $user = auth()->user();
        $from = request('from') ? now()->parse(request('from'))->startOfDay() : now()->startOfMonth();
        $to   = request('to') ? now()->parse(request('to'))->endOfDay() : now()->endOfMonth();

        $payments = \App\Models\Payment::with('booking')
            ->where('status', 'paid')
            ->whereHas('booking', fn($q) => $q->where('referred_by_id', $user->id))
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $rateDefault = (float)config('sales.commission_rate', (float)env('SALES_COMMISSION_RATE', 10));
        $rows = $payments->map(function ($p) use ($rateDefault) {
            $comm = \App\Models\Commission::where('payment_id', $p->id)->first();
            $rate = $comm?->rate ?? 0.0;
            $commission = $comm?->amount ?? 0.0;
            $outstanding = 0.0;
            if ($p->booking) {
                $outstanding = max(((float)$p->booking->total_amount) - ((float)$p->booking->amount_paid), 0);
            }
            return [
                'date' => $p->created_at->format('Y-m-d H:i'),
                'booking' => 'BK'.$p->booking_id,
                'client' => optional($p->booking)->customer_name,
                'method' => $p->method,
                'amount' => number_format($p->amount, 2),
                'outstanding' => number_format($outstanding, 2),
                'type' => $comm?->type ?? '',
                'rate' => $rate,
                'commission' => number_format($commission, 2),
            ];
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.commissions', [
            'rows' => $rows,
            'title' => 'Commission Report',
            'period' => [$from, $to],
        ]);
        return $pdf->download('commissions.pdf');
    }

    public function conversions()
    {
        $user = auth()->user();
        $won = \App\Models\Lead::where('sales_rep_id', $user->id)->where('stage', 'won')->count();
        $total = \App\Models\Lead::where('sales_rep_id', $user->id)->count();
        $rate = $total ? round($won * 100 / $total, 1) : 0;
        return view('staff.conversions', compact('won','total','rate'));
    }

    public function payments()
    {
        return redirect()->route('staff.commissions');
    }

    public function targets()
    {
        $user = auth()->user();
        $target = \App\Models\SalesTarget::where('staff_id', $user->id)
            ->where('start_date', '<=', today())->where('end_date', '>=', today())
            ->orderByDesc('start_date')->first();
        $achieved = \App\Models\Payment::whereHas('booking', function($q) use ($user){
            $q->where('referred_by_id', $user->id);
        })->whereBetween('created_at', [optional($target)->start_date ?? now()->startOfMonth(), optional($target)->end_date ?? now()->endOfMonth()])->sum('amount');
        return view('staff.targets', compact('target','achieved'));
    }

    public function referrals()
    {
        $user = auth()->user();
        if (!$user->referral_code) {
            $user->referral_code = substr(bin2hex(random_bytes(8)),0,8);
            $user->save();
        }
        $link = route('ref', $user->referral_code);
        $referredCount = \App\Models\Booking::where('referred_by_id', $user->id)->count();
        $bookings = \App\Models\Booking::with(['job','package'])
            ->where('referred_by_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('staff.referrals', compact('link','referredCount','bookings'));
    }

    public function saveLeadNote($leadId)
    {
        $lead = \App\Models\Lead::where('id', $leadId)->where('sales_rep_id', auth()->id())->firstOrFail();
        $data = request()->validate([
            'content' => 'required|string',
            'next_follow_up' => 'nullable|date',
            'stage' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        \App\Models\LeadNote::create([
            'lead_id' => $lead->id,
            'sales_rep_id' => auth()->id(),
            'content' => $data['content'],
            'next_follow_up' => $data['next_follow_up'] ?? null,
        ]);
        $lead->update([
            'next_follow_up' => $data['next_follow_up'] ?? $lead->next_follow_up,
            'stage' => $data['stage'] ?? $lead->stage,
            'status' => $data['status'] ?? $lead->status,
        ]);
        return redirect()->route('staff.leads')->with('success', 'Note saved');
    }

    // Detailed view for an assigned client
    public function showClient(\App\Models\User $client)
    {
        $user = auth()->user();
        abort_unless($client && method_exists($client, 'is_client') && $client->is_client(), 404);
        $assigned = \App\Models\ClientProfile::where('user_id', $client->id)->where('sales_rep_id', $user->id)->exists();
        abort_unless($assigned, 403);

        $profile = \App\Models\ClientProfile::firstOrNew(['user_id' => $client->id]);
        $documents = \App\Models\ClientDocument::where('user_id', $client->id)->orderByDesc('created_at')->get();
        $bookings = \App\Models\Booking::with(['job','package','payments'])
            ->where('user_id', $client->id)
            ->orderByDesc('created_at')
            ->get();
        $payments = \App\Models\Payment::with('booking')
            ->whereHas('booking', fn($q) => $q->where('user_id', $client->id))
            ->orderByDesc('created_at')->get();
        $leads = \App\Models\Lead::with(['leadNotes','salesRep'])
            ->where('client_id', $client->id)
            ->where('sales_rep_id', $user->id)
            ->orderByDesc('created_at')->get();

        return view('staff.client_show', compact('client','profile','documents','bookings','payments','leads'));
    }
}
