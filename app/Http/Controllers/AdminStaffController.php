<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Password;
use App\Helpers\Phone as PhoneHelper;

class AdminStaffController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $status = (string) $request->input('status', 'all'); // all|active|inactive

        $query = User::whereIn('role', [UserRole::Staff, UserRole::Reception]);
        if ($q !== '') {
            $query->where(function($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $staff = $query->orderBy('name')->paginate(15)->withQueryString();

        // Enrich with simple metrics (for current page only)
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

        $filters = ['q' => $q, 'status' => $status];
        return view('admin.staff.index', compact('staff','stats','filters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_active' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $phone = null;
        if (!empty($validated['phone'])) {
            $normalized = PhoneHelper::toE164Digits($validated['phone']);
            if (!$normalized) {
                return back()->withInput()->withErrors(['phone' => 'Enter a valid phone number.']);
            }
            $phone = $normalized;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // hashed by cast
            'role' => UserRole::Staff,
            'is_active' => (bool)($validated['is_active'] ?? true),
            'phone' => $phone,
        ]);

        try {
            // Mark as verified to allow immediate access if verification is enforced later
            $user->forceFill(['email_verified_at' => now()])->save();
        } catch (\Throwable $e) {
            // ignore if column missing
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
    }

    public function edit(User $staff)
    {
        if (!$staff || ($staff->role !== UserRole::Staff && $staff->role !== 'staff')) {
            abort(404);
        }

        // quick metrics similar to index
        $clients = \App\Models\ClientProfile::where('sales_rep_id', $staff->id)->count();
        $openLeads = \App\Models\Lead::where('sales_rep_id', $staff->id)->where('status', 'open')->count();

        return view('admin.staff.edit', compact('staff','clients','openLeads'));
    }

    public function update(Request $request, User $staff)
    {
        if (!$staff || ($staff->role !== UserRole::Staff && $staff->role !== 'staff')) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$staff->id],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', 'in:staff,reception'],
        ]);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->is_active = (bool)($validated['is_active'] ?? false);
        if (array_key_exists('phone', $validated)) {
            if ($validated['phone'] === null || $validated['phone'] === '') {
                $staff->phone = null;
            } else {
                $normalized = PhoneHelper::toE164Digits($validated['phone']);
                if (!$normalized) {
                    return back()->withInput()->withErrors(['phone' => 'Enter a valid phone number.']);
                }
                $staff->phone = $normalized;
            }
        }
        if (!empty($validated['password'])) {
            // hashed by cast
            $staff->password = $validated['password'];
        }
        if (!empty($validated['role'])) {
            // allow switching between staff and reception
            $staff->role = $validated['role'];
        }
        $staff->save();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated.');
    }

    public function show(User $staff)
    {
        if (!$staff || !in_array($staff->role, [UserRole::Staff, UserRole::Reception, 'staff', 'reception'], true)) {
            abort(404);
        }

        $clients = \App\Models\ClientProfile::where('sales_rep_id', $staff->id)->count();
        $openLeads = \App\Models\Lead::where('sales_rep_id', $staff->id)->where('status', 'open')->count();
        $targetModel = \App\Models\SalesTarget::where('staff_id', $staff->id)
            ->where('start_date', '<=', today())->where('end_date', '>=', today())
            ->orderByDesc('start_date')->first();
        $target = optional($targetModel)->target_amount ?: 0;
        $achieved = 0;
        if ($targetModel) {
            $achieved = \App\Models\Payment::whereHas('booking', function($q) use ($staff){
                $q->where('referred_by_id', $staff->id);
            })->whereBetween('created_at', [$targetModel->start_date, $targetModel->end_date])->sum('amount');
        }

        $stats = [
            'clients' => $clients,
            'openLeads' => $openLeads,
            'target' => (float)$target,
            'achieved' => (float)$achieved,
        ];

        return view('admin.staff.show', compact('staff','stats'));
    }

    public function destroy(User $staff)
    {
        if (!$staff || ($staff->role !== UserRole::Staff && $staff->role !== 'staff')) {
            abort(404);
        }

        // Prevent deletion if staff has assignments; suggest deactivation instead
        $clients = \App\Models\ClientProfile::where('sales_rep_id', $staff->id)->count();
        $leads = \App\Models\Lead::where('sales_rep_id', $staff->id)->count();
        if ($clients > 0 || $leads > 0) {
            return back()->with('error', 'Cannot delete staff with assigned clients or leads. Deactivate instead.');
        }

        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted.');
    }

    public function promote(User $staff)
    {
        if (!$staff || ($staff->role !== UserRole::Staff && $staff->role !== 'staff')) {
            abort(404);
        }
        $staff->role = UserRole::Admin;
        $staff->save();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member promoted to admin.');
    }

    public function invite(User $staff)
    {
        if (!$staff || ($staff->role !== UserRole::Staff && $staff->role !== 'staff')) {
            abort(404);
        }

        $status = Password::sendResetLink(['email' => $staff->email]);
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Invitation email (password setup link) sent.');
        }
        return back()->with('error', __($status));
    }

    public function makeReception(User $staff)
    {
        if (!$staff) {
            abort(404);
        }
        // Allow converting from staff to reception (and from reception back to staff if needed later)
        if (!($staff->role === UserRole::Staff || $staff->role === 'staff' || $staff->role === UserRole::Reception || $staff->role === 'reception')) {
            abort(403);
        }
        $staff->role = UserRole::Reception;
        $staff->save();
        return redirect()->route('admin.staff.index')->with('success', $staff->name.' is now Reception.');
    }

    public function makeStaff(User $staff)
    {
        if (!$staff) {
            abort(404);
        }
        // Allow converting from reception to staff (or keep staff as staff)
        if (!($staff->role === UserRole::Reception || $staff->role === 'reception' || $staff->role === UserRole::Staff || $staff->role === 'staff')) {
            abort(403);
        }
        $staff->role = UserRole::Staff;
        $staff->save();
        return redirect()->route('admin.staff.index')->with('success', $staff->name.' is now Staff.');
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
