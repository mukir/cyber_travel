<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReceptionController extends Controller
{
    public function dashboard()
    {
        $todayVisitors = \App\Models\Visitor::whereDate('created_at', today())->count();
        $openClients = \App\Models\ClientProfile::where('status','!=','Paid')->count();
        return view('reception.dashboard', compact('todayVisitors','openClients'));
    }

    public function clients(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $query = \App\Models\ClientProfile::with(['salesRep','user'])->orderBy('created_at','desc');
        if ($q !== '') {
            $query->where(function($w) use ($q){
                $w->where('name','like',"%{$q}%")
                  ->orWhere('email','like',"%{$q}%")
                  ->orWhere('phone','like',"%{$q}%")
                  ->orWhere('id_no','like',"%{$q}%");
            });
        }
        $clients = $query->paginate(15)->withQueryString();
        return view('reception.clients', compact('clients','q'));
    }

    public function visitors(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'name' => ['required','string','max:255'],
                'national_id' => ['required','string','max:120'],
                'phone' => ['required','string','max:30'],
                'email' => ['nullable','email','max:255'],
                'notes' => ['nullable','string','max:1000'],
            ]);
            \App\Models\Visitor::create($data);
            return redirect()->route('reception.visitors')->with('success','Visitor recorded');
        }

        $visitors = \App\Models\Visitor::latest()->paginate(20);
        return view('reception.visitors', compact('visitors'));
    }
}

