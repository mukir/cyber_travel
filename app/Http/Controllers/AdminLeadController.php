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
}
