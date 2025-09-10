<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staff = User::where('role', UserRole::Staff)->orderBy('name')->get();

        // Enrich with simple metrics
        $stats = [];
        foreach ($staff as $u) {
            $clients = \App\Models\ClientProfile::where('sales_rep_id', $u->id)->count();
            $openLeads = \App\Models\Lead::where('sales_rep_id', $u->id)->where('status', 'open')->count();
            $targetModel = \App\Models\SalesTarget::where('staff_id', $u->id)
                ->where('start_date', '<=', today())->where('end_date', '>=', today())
                ->orderByDesc('start_date')->first();
            $target = optional($targetModel)->target_amount ?: 0;
            $achieved = 0;
            if ($targetModel) {
                $achieved = \App\Models\Payment::whereHas('booking', function($q) use ($u){
                    $q->where('referred_by_id', $u->id);
                })->whereBetween('created_at', [$targetModel->start_date, $targetModel->end_date])->sum('amount');
            }
            $stats[$u->id] = [
                'clients' => $clients,
                'openLeads' => $openLeads,
                'target' => (float)$target,
                'achieved' => (float)$achieved,
            ];
        }

        return view('admin.staff.index', compact('staff','stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // hashed by cast
            'role' => UserRole::Staff,
            'is_active' => (bool)($validated['is_active'] ?? true),
        ]);

        try {
            // Mark as verified to allow immediate access if verification is enforced later
            $user->forceFill(['email_verified_at' => now()])->save();
        } catch (\Throwable $e) {
            // ignore if column missing
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
    }

    public function toggle(User $staff)
    {
        if (!$staff || ($staff->role !== UserRole::Staff && $staff->role !== 'staff')) {
            abort(404);
        }
        $staff->is_active = (bool)!$staff->is_active;
        $staff->save();
        return back()->with('success', $staff->name.' is now '.($staff->is_active ? 'active' : 'inactive'));
    }
}
