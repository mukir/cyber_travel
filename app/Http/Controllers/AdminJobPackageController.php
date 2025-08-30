<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobPackage;
use Illuminate\Http\Request;

class AdminJobPackageController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['is_default']) && $data['is_default']) {
            // unset existing defaults
            $job->packages()->update(['is_default' => false]);
        }

        $job->packages()->create($data);

        return redirect()->route('admin.jobs.edit', $job)->with('success', 'Package added');
    }

    public function update(Request $request, Job $job, JobPackage $package)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['is_default']) && $data['is_default']) {
            $job->packages()->where('id', '!=', $package->id)->update(['is_default' => false]);
        }

        $package->update($data);

        return redirect()->route('admin.jobs.edit', $job)->with('success', 'Package updated');
    }

    public function destroy(Job $job, JobPackage $package)
    {
        $package->delete();
        return redirect()->route('admin.jobs.edit', $job)->with('success', 'Package removed');
    }
}

