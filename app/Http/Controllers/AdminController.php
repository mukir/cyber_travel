<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function payments()
    {
        return view('admin.payments');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function communications()
    {
        return view('admin.communications');
    }

    public function logs()
    {
        return view('admin.logs');
    }

    public function notes()
    {
        return view('admin.notes');
    }
}
