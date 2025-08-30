<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with(['packages' => function ($q) { $q->orderByDesc('is_default'); }])
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        $job->load(['packages' => function ($q) { $q->orderByDesc('is_default'); }]);
        return view('jobs.show', compact('job'));
    }
}

