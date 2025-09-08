<?php

namespace App\Http\Controllers;

use App\Models\Job;

class ServiceController extends Controller
{
    public function index()
    {
        $category = request('category');
        $query = Job::where('active', true);
        if ($category) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('slug', $category)->where('active', true);
            });
        }
        $jobs = $query->orderBy('name')->get();
        $categories = \App\Models\ServiceCategory::where('active', true)->orderBy('name')->get();
        return view('jobs.index', compact('jobs', 'categories', 'category'));
    }

    public function show(Job $service)
    {
        // Route model binding uses {service:slug} mapped to Job
        $job = $service;
        return view('jobs.show', compact('job'));
    }
}
