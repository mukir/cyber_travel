<?php

namespace App\Http\Controllers;

class StaffController extends Controller
{
    public function dashboard()
    {
        return view('staff.dashboard');
    }

    public function leads()
    {
        return view('staff.leads');
    }

    public function notes()
    {
        return view('staff.notes');
    }

    public function reminders()
    {
        return view('staff.reminders');
    }

    public function commissions()
    {
        return view('staff.commissions');
    }

    public function reports()
    {
        return view('staff.reports');
    }

    public function conversions()
    {
        return view('staff.conversions');
    }

    public function payments()
    {
        return view('staff.payments');
    }

    public function targets()
    {
        return view('staff.targets');
    }

    public function referrals()
    {
        return view('staff.referrals');
    }
}
