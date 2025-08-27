<?php

namespace App\Http\Controllers;

use App\Models\ClientDocument;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard()
    {
        $profile = ClientProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $documents = ClientDocument::where('user_id', Auth::id())->get();
        $requiredDocs = collect(['passport', 'good_conduct', 'cv', 'photo']);
        $missingDocs = $requiredDocs->diff($documents->pluck('type'));

        return view('client.dashboard', compact('profile', 'documents', 'missingDocs'));
    }

    public function editBiodata()
    {
        $profile = ClientProfile::firstOrNew(['user_id' => Auth::id()]);

        return view('client.biodata', compact('profile'));
    }

    public function storeBiodata(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'dob' => 'nullable|date',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'gender' => 'nullable|string',
            'id_no' => 'nullable|string',
            'county' => 'nullable|string',
            'next_of_kin' => 'nullable|string',
            'service_package' => 'nullable|string',
            'status' => 'nullable|string',
            'application_date' => 'nullable|date',
            'interview_date' => 'nullable|date',
            'travel_date' => 'nullable|date',
        ]);

        $data['user_id'] = Auth::id();
        ClientProfile::updateOrCreate(['user_id' => Auth::id()], $data);

        return redirect()->route('client.dashboard');
    }

    public function documents()
    {
        $documents = ClientDocument::where('user_id', Auth::id())->get();

        return view('client.documents', compact('documents'));
    }

    public function uploadDocument(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('file')->store('documents');

        ClientDocument::create([
            'user_id' => Auth::id(),
            'type' => $data['type'],
            'path' => $path,
        ]);

        return redirect()->route('client.documents');
    }
}
