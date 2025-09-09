<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTargetController extends Controller
{
    public function index(Request $request)
    {
        $q = SalesTarget::with('staff')->orderByDesc('start_date');
        if ($request->filled('staff_id')) { $q->where('staff_id', $request->integer('staff_id')); }
        if ($request->filled('period')) { $q->where('period', $request->string('period')); }
        $targets = $q->paginate(20)->appends($request->query());
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.targets.index', compact('targets','staff'));
    }

    public function create()
    {
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.targets.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'staff_id' => ['required', \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1))],
            'period' => ['required','in:monthly,quarterly'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'target_amount' => ['required','numeric','min:0'],
        ]);
        SalesTarget::create($data);
        return redirect()->route('admin.targets.index')->with('success', 'Target created');
    }

    public function edit(SalesTarget $target)
    {
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.targets.edit', compact('target','staff'));
    }

    public function update(Request $request, SalesTarget $target)
    {
        $data = $request->validate([
            'staff_id' => ['required', \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1))],
            'period' => ['required','in:monthly,quarterly'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'target_amount' => ['required','numeric','min:0'],
        ]);
        $target->update($data);
        return redirect()->route('admin.targets.index')->with('success', 'Target updated');
    }

    public function destroy(SalesTarget $target)
    {
        $target->delete();
        return redirect()->route('admin.targets.index')->with('success', 'Target deleted');
    }
}
