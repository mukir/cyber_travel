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
    public function index(\Illuminate\Http\Request $request)
    {
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        $query = User::where('role', UserRole::Client)->orderBy('name');
        $selectedRep = $request->get('rep');
        if ($selectedRep === 'unassigned') {
            $ids = \App\Models\ClientProfile::whereNull('sales_rep_id')->pluck('user_id');
            $query->whereIn('id', $ids);
        } elseif (!empty($selectedRep)) {
            $ids = \App\Models\ClientProfile::where('sales_rep_id', $selectedRep)->pluck('user_id');
            $query->whereIn('id', $ids);
        }
        $clients = $query->paginate(20)->withQueryString();
        return view('admin.clients', compact('clients','staff','selectedRep'));
    }

    public function create()
    {
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.clients.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'sales_rep_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1)),
            ],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role'] = UserRole::Client;
        $user = User::create($data);

        if (!empty($data['sales_rep_id'])) {
            \App\Models\ClientProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'sales_rep_id' => $data['sales_rep_id'],
                ]
            );
        }

        return redirect()->route('admin.clients');
    }

    public function edit(User $client)
    {
        $profile = ClientProfile::firstOrNew(['user_id' => $client->id]);
        $staff = User::where('role', UserRole::Staff)->where('is_active', true)->orderBy('name')->get();
        return view('admin.clients.edit', compact('client', 'profile', 'staff'));
    }

    public function show(User $client)
    {
        $profile = ClientProfile::firstOrNew(['user_id' => $client->id]);
        $documents = ClientDocument::where('user_id', $client->id)->orderByDesc('created_at')->get();
        $bookings = \App\Models\Booking::with(['job','package','payments'])
            ->where('user_id', $client->id)
            ->orderByDesc('created_at')
            ->get();
        $payments = \App\Models\Payment::with('booking')
            ->whereHas('booking', function($q) use ($client){ $q->where('user_id', $client->id); })
            ->orderByDesc('created_at')
            ->get();

        return view('admin.clients.show', compact('client','profile','documents','bookings','payments'));
    }

    public function update(Request $request, User $client)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'password' => 'nullable|string|min:8',
            'sales_rep_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('users','id')->where(fn($q)=>$q->where('role', UserRole::Staff->value)->where('is_active', 1)),
            ],
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

    public function viewDocument(User $client, ClientDocument $document)
    {
        abort_unless((int)$document->user_id === (int)$client->id, 404);
        $path = $document->path;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
        }
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('local')->response($path);
        }
        abort(404);
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'assign_to' => ['required','integer','exists:users,id'],
            'client_ids' => ['required','array','min:1'],
            'client_ids.*' => ['integer','exists:users,id'],
        ]);

        // Ensure target is a staff user
        $staff = User::where('id', $data['assign_to'])->where('role', \App\Enums\UserRole::Staff)->where('is_active', true)->first();
        if (!$staff) {
            return back()->with('error', 'Selected user is not a staff member.');
        }

        $count = 0;
        foreach ($data['client_ids'] as $cid) {
            $u = User::find($cid);
            if (!$u || !$u->is_client()) continue;
            ClientProfile::updateOrCreate(['user_id' => $u->id], [
                'name' => $u->name,
                'email' => $u->email,
                'sales_rep_id' => $staff->id,
            ]);
            $count++;
        }

        return back()->with('success', 'Assigned '.$count.' client(s) to '.$staff->name.'.');
    }
}
