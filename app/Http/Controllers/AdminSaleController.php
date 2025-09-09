<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['client', 'staff'])->get();
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $clients = User::where('role', UserRole::Client)->get();
        $staff   = User::where('role', UserRole::Staff)->where('is_active', true)->get();
        return view('admin.sales.create', compact('clients', 'staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'  => 'required|exists:users,id',
            'staff_id'   => [
                'required',
                \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1)),
            ],
            'amount'     => 'required|numeric',
            'commission' => 'required|numeric',
            'status'     => 'required|string',
        ]);

        Sale::create($data);

        return redirect()->route('admin.sales');
    }

    public function edit(Sale $sale)
    {
        $clients = User::where('role', UserRole::Client)->get();
        $staff   = User::where('role', UserRole::Staff)->where('is_active', true)->get();
        return view('admin.sales.edit', compact('sale', 'clients', 'staff'));
    }

    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'client_id'  => 'required|exists:users,id',
            'staff_id'   => [
                'required',
                \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1)),
            ],
            'amount'     => 'required|numeric',
            'commission' => 'required|numeric',
            'status'     => 'required|string',
        ]);

        $sale->update($data);

        return redirect()->route('admin.sales');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('admin.sales');
    }
}
