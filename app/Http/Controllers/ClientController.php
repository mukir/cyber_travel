<?php

namespace App\Http\Controllers;

use App\Models\ClientDocument;
use App\Models\ClientProfile;
use App\Models\Booking;
use App\Models\Job;
use App\Models\JobPackage;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewEnquiryMail;
use App\Models\SupportTicket;

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

    public function support()
    {
        return view('client.support');
    }

    public function storeSupport(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:200',
            'category' => 'nullable|string|max:100',
            'message' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ]);

        $user = Auth::user();

        // Generate a simple human-friendly reference like ST-20250904-ABCDE
        $ref = 'ST-'.now()->format('Ymd').'-'.strtoupper(str()->random(5));

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'reference' => $ref,
            'subject' => $data['subject'],
            'category' => $data['category'] ?? null,
            'message' => $data['message'],
            'status' => 'open',
            'priority' => $data['priority'] ?? 'normal',
        ]);

        // Optionally notify support email (best-effort). Uses SUPPORT_EMAIL or MAIL_FROM_ADDRESS.
        $supportTo = env('SUPPORT_EMAIL', config('mail.from.address'));
        if ($supportTo) {
            try {
                Mail::raw(
                    "New Support Ticket \n\n".
                    "Reference: {$ticket->reference}\n".
                    "From: {$user->name} <{$user->email}>\n".
                    "Subject: {$ticket->subject}\n".
                    ($ticket->category ? ("Category: {$ticket->category}\n") : '').
                    "Priority: {$ticket->priority}\n\n".
                    "Message:\n{$ticket->message}\n",
                    function ($m) use ($supportTo, $ticket) {
                        $m->to($supportTo)->subject('[Support] '.$ticket->reference.' - '.$ticket->subject);
                    }
                );
            } catch (\Throwable $e) {
                // ignore email failures
            }
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Support request sent. Your ticket reference is '.$ticket->reference.'.');
    }

    public function supportTickets(Request $request)
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        return view('client.support_index', compact('tickets'));
    }

    public function showSupportTicket(SupportTicket $ticket)
    {
        abort_unless((int)$ticket->user_id === (int)Auth::id(), 403);
        return view('client.support_show', compact('ticket'));
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

        // Normalize phone to WhatsApp-friendly digits (e.g., 07.. -> 2547..)
        if (!empty($data['phone'])) {
            $normalized = \App\Helpers\Phone::toE164Digits($data['phone']);
            // Only replace if we can normalize; otherwise keep original so user can fix later
            $data['phone'] = $normalized ?? $data['phone'];
        }

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

    public function enquiry()
    {
        $profile = ClientProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $jobs = Job::where('active', true)->orderBy('name')->get(['id','name']);
        $packages = JobPackage::whereIn('job_id', $jobs->pluck('id'))->orderBy('name')->get(['id','job_id','name','price']);
        $packagesByJob = $packages->groupBy('job_id')->map(function($list){
            return $list->map(function($p){
                return ['id' => $p->id, 'name' => $p->name, 'price' => (float)$p->price];
            })->values();
        });

        return view('client.enquiry', [
            'profile' => $profile,
            'jobs' => $jobs,
            'packagesByJob' => $packagesByJob,
        ]);
    }

    public function storeEnquiry(Request $request)
    {
        $type = $request->input('service_type');
        $rules = [
            'service_type' => 'required|in:job,tour',
            'message' => 'nullable|string|max:1000',
        ];
        if ($type === 'job') {
            $rules = array_merge($rules, [
                'job_id' => 'required|exists:service_jobs,id',
                'package_id' => 'nullable|exists:job_packages,id',
                'experience_years' => 'nullable|numeric|min:0|max:60',
                'available_from' => 'nullable|date',
                'has_passport' => 'nullable|boolean',
                'education' => 'nullable|string|max:255',
            ]);
        } else {
            $rules = array_merge($rules, [
                'destination' => 'required|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'adults' => 'nullable|integer|min:1|max:20',
                'children' => 'nullable|integer|min:0|max:20',
                'budget' => 'nullable|numeric|min:0',
                'accommodation' => 'nullable|string|max:255',
            ]);
        }

        $data = $request->validate($rules);

        $profile = ClientProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        // Choose a sales rep: prefer assigned on profile; fallback to first staff, then first admin
        $salesRepId = $profile->sales_rep_id;
        if (!$salesRepId) {
            $salesRepId = optional(\App\Models\User::where('role', \App\Enums\UserRole::Staff)->orderBy('id')->first())->id
                ?: optional(\App\Models\User::where('role', \App\Enums\UserRole::Admin)->orderBy('id')->first())->id
                ?: Auth::id();
        }

        $user = Auth::user();

        // Prepare structured notes as JSON
        $payload = ['service_type' => $data['service_type']];
        if ($data['service_type'] === 'job') {
            $payload += [
                'job_id' => $data['job_id'] ?? null,
                'package_id' => $data['package_id'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
                'available_from' => $data['available_from'] ?? null,
                'has_passport' => (bool)($data['has_passport'] ?? false),
                'education' => $data['education'] ?? null,
            ];
        } else {
            $payload += [
                'destination' => $data['destination'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'adults' => isset($data['adults']) ? (int)$data['adults'] : null,
                'children' => isset($data['children']) ? (int)$data['children'] : null,
                'budget' => isset($data['budget']) ? (float)$data['budget'] : null,
                'accommodation' => $data['accommodation'] ?? null,
            ];
        }
        if (!empty($data['message'] ?? null)) {
            $payload['message'] = $data['message'];
        }

        Lead::create([
            'sales_rep_id' => $salesRepId,
            'client_id' => $user->id,
            'name' => $profile->name ?: $user->name,
            'email' => $profile->email ?: $user->email,
            'phone' => $profile->phone,
            'stage' => 'new',
            'status' => 'open',
            'notes' => json_encode($payload),
        ]);

        // Notify assigned sales rep by email (best-effort)
        $rep = \App\Models\User::find($salesRepId);
        if ($rep && $rep->email) {
            $enquirer = [
                'name' => $profile->name ?: $user->name,
                'email' => $profile->email ?: $user->email,
                'phone' => $profile->phone,
            ];
            try {
                Mail::to($rep->email)->send(new NewEnquiryMail($enquirer, $payload));
            } catch (\Throwable $e) {
                // ignore email failures
            }
        }

        return redirect()->route('client.dashboard')->with('success', 'Your enquiry was sent. Our team will contact you.');
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
