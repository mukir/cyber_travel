<?php

namespace App\Http\Controllers;

use App\Models\ClientDocument;
use App\Models\ClientProfile;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // Determine progress stage based on profile + bookings
        $paidBookings = Booking::where('user_id', Auth::id())->where('status', 'paid')->count();
        $anyBookings = Booking::where('user_id', Auth::id())->exists();

        $status = strtolower((string)($profile->status ?? ''));
        $stage = 1; // 1: Registration, 2: Payment, 3: Processing, 4: Complete

        if ($paidBookings > 0) {
            $stage = max($stage, 2);
        } elseif ($anyBookings) {
            // booking exists but not paid -> still at Payment stage
            $stage = max($stage, 2);
        }

        if ($profile->interview_date || in_array($status, ['processing', 'interview', 'interview scheduled', 'approved', 'in progress'])) {
            $stage = max($stage, 3);
        }

        if ($profile->travel_date || in_array($status, ['complete', 'completed', 'travelled', 'done'])) {
            $stage = 4;
        }

        $stages = [
            ['key' => 'registration', 'label' => 'Registration', 'done' => $stage >= 1],
            ['key' => 'payment', 'label' => 'Payment', 'done' => $stage >= 2],
            ['key' => 'processing', 'label' => 'Processing', 'done' => $stage >= 3],
            ['key' => 'complete', 'label' => 'Complete', 'done' => $stage >= 4],
        ];

        // Pending actions suggestions
        $pendingActions = [];
        if ($missingDocs->isNotEmpty()) {
            $pendingActions[] = [
                'label' => 'Upload missing documents: '. $missingDocs->implode(', '),
                'route' => route('client.documents'),
            ];
        }
        if ($paidBookings === 0) {
            $pendingActions[] = [
                'label' => $anyBookings ? 'Complete payment for your booking' : 'Book a package to proceed with payment',
                'route' => route('jobs.index'),
            ];
        }
        if (empty($profile->interview_date) && $stage < 4) {
            $pendingActions[] = [
                'label' => 'Interview date not set yet',
                'route' => null,
            ];
        }

        // Latest booking and next milestone
        $latestBooking = Booking::with(['job','package'])
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        $nextMilestone = match ($stage) {
            1 => 'Payment',
            2 => 'Processing',
            3 => 'Complete',
            default => null, // Completed
        };

        return view('client.dashboard', compact(
            'profile', 'documents', 'missingDocs',
            'stages', 'stage', 'pendingActions',
            'latestBooking', 'nextMilestone'
        ));
    }

    public function editBiodata()
    {
        $profile = ClientProfile::firstOrNew(['user_id' => Auth::id()]);

        // Compute completeness (profile fields + required documents)
        $importantFields = ['name','dob','phone','email','gender','id_no','county','next_of_kin','service_package'];
        $fieldsTotal = count($importantFields);
        $fieldsFilled = collect($importantFields)->filter(function ($key) use ($profile) {
            $v = $profile->$key ?? null;
            return !is_null($v) && trim((string)$v) !== '';
        })->count();

        $requiredDocs = collect(['passport', 'good_conduct', 'cv', 'photo']);
        $uploaded = ClientDocument::where('user_id', Auth::id())->pluck('type')->map(function ($t) {
            return strtolower((string)$t);
        });
        $docsTotal = $requiredDocs->count();
        $docsUploaded = $requiredDocs->filter(fn($d) => $uploaded->contains($d))->count();
        $missingDocs = $requiredDocs->reject(fn($d) => $uploaded->contains($d))->values();

        // Weighting between profile fields and documents (60% / 40%)
        $fieldsFraction = $fieldsTotal ? ($fieldsFilled / $fieldsTotal) : 1;
        $docsFraction   = $docsTotal ? ($docsUploaded / $docsTotal) : 1;
        $completeness   = (int) round(($fieldsFraction * 0.6 + $docsFraction * 0.4) * 100);

        return view('client.biodata', compact('profile', 'fieldsFilled', 'fieldsTotal', 'docsUploaded', 'docsTotal', 'missingDocs', 'completeness'));
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
            'note' => 'nullable|string|max:500',
        ]);

        $path = $request->file('file')->store('documents');

        ClientDocument::create([
            'user_id' => Auth::id(),
            'type' => $data['type'],
            'path' => $path,
            'validated' => false,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('client.documents')->with('success', 'Document uploaded.');
    }

    public function bookings()
    {
        $bookings = Booking::with(['job', 'package'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('client.bookings', compact('bookings'));
    }

    public function deleteDocument(Request $request, ClientDocument $document)
    {
        abort_unless((int)$document->user_id === (int)Auth::id(), 403);
        try {
            if ($document->path) {
                Storage::delete($document->path);
            }
        } catch (\Throwable $e) {
            // ignore storage deletion errors
        }
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }

    public function replaceDocument(Request $request, ClientDocument $document)
    {
        abort_unless((int)$document->user_id === (int)Auth::id(), 403);
        $data = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $old = $document->path;
        $newPath = $request->file('file')->store('documents');
        $document->update([
            'path' => $newPath,
            'validated' => false, // revalidation may be required after replacement
        ]);
        try {
            if ($old) Storage::delete($old);
        } catch (\Throwable $e) {
            // ignore
        }
        return back()->with('success', 'Document replaced.');
    }

    public function updateDocumentNote(Request $request, ClientDocument $document)
    {
        abort_unless((int)$document->user_id === (int)Auth::id(), 403);
        $data = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);
        $document->update(['note' => $data['note'] ?? null]);
        return back()->with('success', 'Note updated.');
    }
}
