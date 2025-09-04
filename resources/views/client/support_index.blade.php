<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Support Tickets</h2>
      <a href="{{ route('client.support') }}" class="inline-flex items-center gap-2 rounded bg-emerald-600 px-3 py-2 text-white text-sm font-medium hover:bg-emerald-700">Open Support Form</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow sm:rounded-lg">
        <div class="p-6">
          @if($tickets->count() === 0)
            <p class="text-sm text-gray-700">No tickets yet. <a class="text-emerald-700 hover:underline" href="{{ route('client.support') }}">Open your first ticket</a>.</p>
          @else
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="text-left text-gray-600 border-b">
                    <th class="py-2 pr-4">Reference</th>
                    <th class="py-2 pr-4">Subject</th>
                    <th class="py-2 pr-4">Status</th>
                    <th class="py-2 pr-4">Priority</th>
                    <th class="py-2 pr-4">Created</th>
                    <th class="py-2 pr-4">Updated</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tickets as $t)
                    @php($statusPill = match($t->status){
                      'open' => 'bg-blue-100 text-blue-800',
                      'pending' => 'bg-amber-100 text-amber-800',
                      'resolved' => 'bg-emerald-100 text-emerald-800',
                      'closed' => 'bg-gray-200 text-gray-800',
                      default => 'bg-slate-100 text-slate-800'
                    })
                    <tr class="border-b last:border-b-0">
                      <td class="py-2 pr-4">
                        <a href="{{ route('client.support.tickets.show', $t) }}" class="text-emerald-700 hover:underline font-medium">{{ $t->reference }}</a>
                      </td>
                      <td class="py-2 pr-4">{{ $t->subject }}</td>
                      <td class="py-2 pr-4"><span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $statusPill }}">{{ ucfirst($t->status) }}</span></td>
                      <td class="py-2 pr-4">{{ ucfirst($t->priority) }}</td>
                      <td class="py-2 pr-4">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                      <td class="py-2 pr-4">{{ $t->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-4">{{ $tickets->links() }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

