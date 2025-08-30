<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function clients()
    {
        return view('admin.clients');
    }

    public function sales()
    {
        return view('admin.sales');
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
