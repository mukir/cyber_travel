<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class AdminServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('admin.service_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required','string','max:100','regex:/^[a-z0-9-]+$/','unique:service_categories,slug'],
            'name' => ['required','string','max:150'],
            'description' => ['nullable','string','max:1000'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = (bool)($data['active'] ?? true);
        ServiceCategory::create($data);
        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, ServiceCategory $service_category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'description' => ['nullable','string','max:1000'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = (bool)($data['active'] ?? false);
        $service_category->update($data);
        return back()->with('success', 'Category updated.');
    }

    public function destroy(ServiceCategory $service_category)
    {
        $service_category->delete();
        return back()->with('success', 'Category deleted.');
    }
}

