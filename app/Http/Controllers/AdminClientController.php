<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\ClientDocument;
use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminClientController extends Controller
{
    public function index()
    {
        $clients = User::where('role', UserRole::Client)->get();
        return view('admin.clients', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = UserRole::Client;
        User::create($data);

        return redirect()->route('admin.clients');
    }

    public function edit(User $client)
    {
        $profile = ClientProfile::firstOrNew(['user_id' => $client->id]);
        $staff = User::where('role', UserRole::Staff)->orderBy('name')->get();
        return view('admin.clients.edit', compact('client', 'profile', 'staff'));
    }

    public function update(Request $request, User $client)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'password' => 'nullable|string|min:8',
            'sales_rep_id' => 'nullable|exists:users,id',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $client->update(collect($data)->only(['name','email','password'])->toArray());

        // Update client profile for sales rep assignment
        $profile = ClientProfile::firstOrNew(['user_id' => $client->id]);
        $profile->fill(['sales_rep_id' => $data['sales_rep_id'] ?? null]);
        if (!$profile->name) { $profile->name = $client->name; }
        if (!$profile->email) { $profile->email = $client->email; }
        $profile->save();

        return redirect()->route('admin.clients');
    }

    public function destroy(User $client)
    {
        $client->delete();
        return redirect()->route('admin.clients');
    }

    public function documents(User $client)
    {
        $documents = ClientDocument::where('user_id', $client->id)->get();
        return view('admin.clients.documents', compact('client', 'documents'));
    }

    public function validateDocument(User $client, ClientDocument $document)
    {
        if ($document->user_id !== $client->id) {
            abort(404);
        }

        $document->validated = true;
        $document->save();

        return redirect()->back();
    }
}
