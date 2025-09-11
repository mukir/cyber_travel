<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class AdminLeadController extends Controller
{
    public function index(Request $request)
    {
        $q = Lead::with('salesRep')->orderByDesc('created_at');
        if ($request->filled('stage')) { $q->where('stage', $request->string('stage')); }
        if ($request->filled('status')) { $q->where('status', $request->string('status')); }
        if ($request->filled('staff_id')) { $q->where('sales_rep_id', $request->integer('staff_id')); }
        if ($request->filled('from')) { $q->whereDate('created_at', '>=', now()->parse($request->string('from'))->startOfDay()); }
        if ($request->filled('to')) { $q->whereDate('created_at', '<=', now()->parse($request->string('to'))->endOfDay()); }
        if ($request->filled('follow_from')) { $q->whereDate('next_follow_up', '>=', now()->parse($request->string('follow_from'))->startOfDay()); }
        if ($request->filled('follow_to')) { $q->whereDate('next_follow_up', '<=', now()->parse($request->string('follow_to'))->endOfDay()); }
        $leads = $q->paginate(20)->appends($request->query());

        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.leads.index', compact('leads','staff'));
    }

    public function show(Lead $lead)
    {
        $lead->load(['salesRep','client','leadNotes' => function($q){ $q->orderByDesc('created_at'); }]);
        return view('admin.leads.show', compact('lead'));
    }

    public function saveNote(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'next_follow_up' => 'nullable|date',
            'stage' => 'nullable|in:new,contacted,qualified,won,lost',
            'status' => 'nullable|in:open,closed',
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

        return back()->with('success', 'Note saved');
    }

    public function create()
    {
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.leads.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sales_rep_id' => ['required', \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1))],
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:255'],
            'stage' => ['nullable','in:new,contacted,qualified,won,lost'],
            'status' => ['nullable','in:open,closed'],
            'next_follow_up' => ['nullable','date'],
            'notes' => ['nullable','string'],
        ]);
        $data['stage'] = $data['stage'] ?? 'new';
        $data['status'] = $data['status'] ?? 'open';
        Lead::create($data);
        return redirect()->route('admin.leads.index')->with('success', 'Lead created');
    }

    public function edit(Lead $lead)
    {
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.leads.edit', compact('lead','staff'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'sales_rep_id' => ['required', \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1))],
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:255'],
            'stage' => ['required','in:new,contacted,qualified,won,lost'],
            'status' => ['required','in:open,closed'],
            'next_follow_up' => ['nullable','date'],
            'notes' => ['nullable','string'],
        ]);
        $lead->update($data);
        return redirect()->route('admin.leads.index')->with('success', 'Lead updated');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted');
    }

    public function approve(Lead $lead)
    {
        // Approve only if the lead is associated with a client account
        if (!$lead->client_id) {
            return back()->with('error', 'This lead has no client account yet. Create the account first.');
        }

        $profile = \App\Models\ClientProfile::firstOrNew(['user_id' => $lead->client_id]);
        if (!$profile->name) { $profile->name = $lead->name; }
        if (!$profile->email) { $profile->email = $lead->email; }
        if (!$profile->phone && $lead->phone) { $profile->phone = $lead->phone; }
        if (!$profile->sales_rep_id && $lead->sales_rep_id) { $profile->sales_rep_id = $lead->sales_rep_id; }
        $profile->status = 'Confirmed';
        $profile->save();

        // Update lead stage/status to won/closed on approval and clear follow-up
        $lead->update(['stage' => 'won', 'status' => 'closed', 'next_follow_up' => null]);

        try {
            \App\Models\LeadNote::create([
                'lead_id' => $lead->id,
                'sales_rep_id' => auth()->id(),
                'content' => 'Lead approved: client confirmed; stage=won, status=closed.',
                'next_follow_up' => null,
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        return back()->with('success', 'Client confirmed from lead.');
    }

    public function createAccount(Lead $lead)
    {
        if ($lead->client_id) {
            return back()->with('info', 'Lead is already linked to a client account.');
        }

        // If an existing client account matches the email, link instead of creating
        $existing = null;
        if ($lead->email) {
            $existing = \App\Models\User::where('email', $lead->email)->first();
        }

        if ($existing && (method_exists($existing, 'is_client') ? $existing->is_client() : ($existing->role === 'client'))) {
            $lead->client_id = $existing->id;
            $lead->save();
            \App\Models\ClientProfile::updateOrCreate(
                ['user_id' => $existing->id],
                [
                    'name' => $lead->name ?: $existing->name,
                    'email' => $lead->email ?: $existing->email,
                    'phone' => $lead->phone,
                    'sales_rep_id' => $lead->sales_rep_id,
                ]
            );
            return back()->with('success', 'Linked lead to existing client account.');
        }

        // Require an email to create an account
        if (!$lead->email) {
            return back()->with('error', 'Email is required to create a client account. Please add an email to this lead.');
        }

        // Create a new client user with a random password
        $password = \Illuminate\Support\Str::random(12);
        $user = \App\Models\User::create([
            'name' => $lead->name ?: 'Client',
            'email' => $lead->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => \App\Enums\UserRole::Client,
        ]);

        \App\Models\ClientProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $lead->name ?: $user->name,
                'email' => $lead->email ?: $user->email,
                'phone' => $lead->phone,
                'sales_rep_id' => $lead->sales_rep_id,
            ]
        );

        $lead->client_id = $user->id;
        $lead->save();

        return back()->with('success', 'Client account created and linked to lead.');
    }
}
