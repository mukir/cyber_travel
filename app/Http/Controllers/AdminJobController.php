<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminJobController extends Controller
{
    public function index()
    {
        $jobs = Job::with('categories')->orderByDesc('created_at')->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('active', true)->orderBy('name')->get();
        $countries = \App\Models\Country::where('active', true)->orderBy('name')->get();
        return view('admin.jobs.create', compact('categories','countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:service_jobs,slug'],
            'description' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'in:europe,gulf,americas,other'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer','exists:service_categories,id'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['active'] = (bool)($data['active'] ?? true);

        $categories = (array)($data['categories'] ?? []);
        unset($data['categories']);

        $job = Job::create($data);
        if (!empty($categories)) {
            $job->categories()->sync($categories);
        }

        return redirect()->route('admin.jobs.index')->with('success', 'Job created');
    }

    public function edit(Job $job)
    {
        $job->load(['packages','categories']);
        $categories = ServiceCategory::orderBy('name')->get();
        $countries = \App\Models\Country::where('active', true)->orderBy('name')->get();
        return view('admin.jobs.edit', compact('job','categories','countries'));
    }

    public function update(Request $request, Job $job)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:service_jobs,slug,' . $job->id],
            'description' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'in:europe,gulf,americas,other'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer','exists:service_categories,id'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['active'] = (bool)($data['active'] ?? false);

        $categories = (array)($data['categories'] ?? []);
        unset($data['categories']);

        $job->update($data);
        $job->categories()->sync($categories);

        return redirect()->route('admin.jobs.edit', $job)->with('success', 'Job updated');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted');
    }
}
