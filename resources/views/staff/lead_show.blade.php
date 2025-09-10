<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lead Details</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <div class="grid md:grid-cols-2 gap-4 text-sm">
          <div>
            <div class="text-gray-600">Name</div>
            <div class="font-semibold text-lg">{{ $lead->name }}</div>
          </div>
          <div>
            <div class="text-gray-600">Email</div>
            <div class="font-mono">{{ $lead->email ?: '—' }}</div>
          </div>
          <div>
            <div class="text-gray-600">Phone</div>
            <div class="font-mono">{{ $lead->phone ?: '—' }}</div>
          </div>
          <div>
            <div class="text-gray-600">Stage / Status</div>
            <div class="capitalize">{{ $lead->stage }} / {{ $lead->status }}</div>
          </div>
          <div>
            <div class="text-gray-600">Next Follow-up</div>
            <div>{{ $lead->next_follow_up ? $lead->next_follow_up->format('Y-m-d') : '—' }}</div>
          </div>
          <div>
            <div class="text-gray-600">Created</div>
            <div>{{ $lead->created_at->format('Y-m-d H:i') }}</div>
          </div>
        </div>

        @php($meta = json_decode($lead->notes, true))
        @if(is_array($meta))
          <div class="mt-6">
            <div class="text-gray-600 text-sm">Lead Context</div>
            <pre class="mt-1 text-xs bg-gray-50 p-3 rounded border overflow-auto">{{ json_encode($meta, JSON_PRETTY_PRINT) }}</pre>
          </div>
        @endif

        <div class="mt-6">
          <a href="{{ route('staff.leads') }}" class="px-4 py-2 rounded border">Back</a>
        </div>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="font-semibold mb-3">Notes</h3>
        <form action="{{ route('staff.leads.note', $lead->id) }}" method="POST" class="flex flex-wrap gap-2 text-sm mb-4">
          @csrf
          <input type="hidden" name="stage" value="{{ $lead->stage }}" />
          <input type="hidden" name="status" value="{{ $lead->status }}" />
          <input name="content" placeholder="Add note" class="rounded border p-2 flex-1 min-w-[240px]" required />
          <input type="date" name="next_follow_up" class="rounded border p-2" />
          <button class="rounded bg-slate-700 px-4 py-2 text-white">Save Note</button>
        </form>
        <div class="space-y-3">
          @forelse($lead->leadNotes as $n)
            <div class="border rounded p-3 text-sm">
              <div class="flex justify-between">
                <div>{{ optional($n->salesRep)->name ?: '—' }}</div>
                <div class="text-gray-500">{{ $n->created_at->format('Y-m-d H:i') }}</div>
              </div>
              <div class="mt-1">{{ $n->content }}</div>
              @if($n->next_follow_up)
                <div class="text-xs text-gray-600 mt-1">Next follow-up: {{ $n->next_follow_up->format('Y-m-d') }}</div>
              @endif
            </div>
          @empty
            <div class="text-sm text-gray-500">No notes yet.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
