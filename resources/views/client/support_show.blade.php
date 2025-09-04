<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ticket {{ $ticket->reference }}</h2>
      <div class="flex items-center gap-2">
        <a href="{{ route('client.support.tickets') }}" class="text-sm text-emerald-700 hover:underline">&larr; Back to My Tickets</a>
        <a href="{{ route('client.support') }}" class="inline-flex items-center gap-2 rounded bg-emerald-600 px-3 py-2 text-white text-sm font-medium hover:bg-emerald-700">Open New Ticket</a>
      </div>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white shadow sm:rounded-lg p-6">
        @php($statusPill = match($ticket->status){
          'open' => 'bg-blue-100 text-blue-800',
          'pending' => 'bg-amber-100 text-amber-800',
          'resolved' => 'bg-emerald-100 text-emerald-800',
          'closed' => 'bg-gray-200 text-gray-800',
          default => 'bg-slate-100 text-slate-800'
        })
        <div class="flex items-start justify-between gap-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $ticket->subject }}</h3>
            <div class="mt-2 text-sm text-gray-600">Reference: <span class="font-mono">{{ $ticket->reference }}</span></div>
            @if($ticket->category)
              <div class="text-sm text-gray-600">Category: {{ $ticket->category }}</div>
            @endif
            <div class="text-sm text-gray-600">Priority: {{ ucfirst($ticket->priority) }}</div>
          </div>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusPill }}">{{ ucfirst($ticket->status) }}</span>
        </div>

        <div class="mt-6">
          <h4 class="text-sm font-semibold text-gray-700">Your Message</h4>
          <div class="mt-2 whitespace-pre-wrap text-gray-900 border rounded p-3 bg-gray-50">{{ $ticket->message }}</div>
        </div>

        <div class="mt-6 text-xs text-gray-500">Created: {{ $ticket->created_at->format('Y-m-d H:i') }} • Last update: {{ $ticket->updated_at->format('Y-m-d H:i') }}</div>
      </div>

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h4 class="text-sm font-semibold text-gray-700">What happens next?</h4>
        <p class="mt-2 text-sm text-gray-700">Our support team will review your request and update the ticket status. You can return to this page to check progress. When resolved, the status will change to <strong>Resolved</strong> or <strong>Closed</strong>.</p>
      </div>
    </div>
  </div>
</x-app-layout>

