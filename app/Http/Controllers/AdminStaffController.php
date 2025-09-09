<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;

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

