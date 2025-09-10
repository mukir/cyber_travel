<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Commission Payouts</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        @if(session('success'))
          <div class="mb-3 rounded bg-emerald-100 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="mb-3 rounded bg-red-100 text-red-800 px-4 py-2 text-sm">
            <ul class="list-disc pl-4">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form method="GET" action="{{ route('admin.payouts.index') }}" class="text-sm grid md:grid-cols-3 gap-3">
          <div>
            <label class="block text-gray-600">Month</label>
            <input type="month" name="month" value="{{ $month }}" class="mt-1 rounded border p-2" />
          </div>
          <div class="md:col-span-2 flex items-end gap-2">
            <button class="rounded bg-slate-700 px-4 py-2 text-white font-semibold">Apply</button>
            <a class="rounded border px-3 py-2" href="{{ route('admin.payouts.export.csv', ['month'=>$month,'status'=>'pending','mode'=>'aggregate']) }}">Export Pending (Aggregate)</a>
            <a class="rounded border px-3 py-2" href="{{ route('admin.payouts.export.csv', ['month'=>$month,'status'=>'pending','mode'=>'detailed']) }}">Export Pending (Detailed)</a>
            <a class="rounded border px-3 py-2" href="{{ route('admin.payouts.export.csv', ['month'=>$month,'status'=>'paid','mode'=>'aggregate']) }}">Export Paid (Aggregate)</a>
            <a class="rounded border px-3 py-2" href="{{ route('admin.payouts.export.csv', ['month'=>$month,'status'=>'paid','mode'=>'detailed']) }}">Export Paid (Detailed)</a>
          </div>
        </form>
        <div class="mt-3 flex items-center justify-between">
          <p class="text-xs text-gray-500">Payouts mark commissions created in the selected month. Use the console command to mark paid on the 15th.</p>
          <form method="POST" action="{{ route('admin.payouts.mark') }}" onsubmit="return confirm('Are you sure? This will mark all pending commissions as PAID.');">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}" />
            <label class="text-sm text-gray-700 mr-2">Type CONFIRM to proceed</label>
            <input type="text" name="confirm" class="rounded border p-2 text-sm" placeholder="CONFIRM" required />
            <label class="text-sm text-gray-700 ml-3 inline-flex items-center">
              <input type="hidden" name="notify" value="0" />
              <input type="checkbox" name="notify" value="1" class="rounded mr-1" /> Email staff payout statements
            </label>
            <button class="ml-3 rounded bg-emerald-600 px-4 py-2 text-white text-sm font-semibold">Mark As Paid Now</button>
          </form>
        </div>
      </div>

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b text-sm font-semibold">Pending ({{ $month }}) — Total: KES {{ number_format($totals['pending'] ?? 0, 2) }}</div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($pending as $row)
              <tr>
                <td class="px-6 py-3">{{ $row['staff'] ?? '—' }}</td>
                <td class="px-6 py-3">{{ $row['count'] ?? 0 }}</td>
                <td class="px-6 py-3">KES {{ number_format($row['amount'] ?? 0, 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="px-6 py-4 text-gray-500">No pending commissions for this month.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b text-sm font-semibold">Paid ({{ $month }}) — Total: KES {{ number_format($totals['paid'] ?? 0, 2) }}</div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($paid as $row)
              <tr>
                <td class="px-6 py-3">{{ $row['staff'] ?? '—' }}</td>
                <td class="px-6 py-3">{{ $row['count'] ?? 0 }}</td>
                <td class="px-6 py-3">KES {{ number_format($row['amount'] ?? 0, 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="px-6 py-4 text-gray-500">No paid commissions recorded for this month.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b text-sm font-semibold">Recent Batches</div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emailed</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse(($batches ?? []) as $b)
              <tr>
                <td class="px-6 py-3">{{ $b->id }}</td>
                <td class="px-6 py-3">{{ $b->month }}</td>
                <td class="px-6 py-3">{{ $b->total_count }}</td>
                <td class="px-6 py-3">KES {{ number_format($b->total_amount, 2) }}</td>
                <td class="px-6 py-3">{{ $b->emailed ? 'Yes' : 'No' }}</td>
                <td class="px-6 py-3 text-right">
                  <a href="{{ route('admin.payouts.export.csv', ['month'=>$b->month,'status'=>'paid','mode'=>'aggregate']) }}" class="underline">Export</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="px-6 py-4 text-gray-500">No batches yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
