<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('position')->orderBy('title')->get();
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required','string','max:100','regex:/^[a-z0-9-]+$/','unique:services,slug'],
            'title' => ['required','string','max:150'],
            'summary' => ['nullable','string','max:2000'],
            'position' => ['nullable','integer','min:0'],
            'active' => ['nullable','boolean'],
        ]);
        $data['position'] = $data['position'] ?? 0;
        $data['active'] = (bool)($data['active'] ?? true);
        Service::create($data);
        return back()->with('success','Service added.');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'title' => ['required','string','max:150'],
            'summary' => ['nullable','string','max:2000'],
            'position' => ['nullable','integer','min:0'],
            'active' => ['nullable','boolean'],
        ]);
        $data['position'] = $data['position'] ?? 0;
        $data['active'] = (bool)($data['active'] ?? false);
        $service->update($data);
        return back()->with('success','Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success','Service deleted.');
    }
}

