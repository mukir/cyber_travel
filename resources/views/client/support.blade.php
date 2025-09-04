<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Support Request</h2>
      <a href="{{ route('client.dashboard') }}" class="text-sm text-emerald-700 hover:underline">&larr; Back to Dashboard</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      @if(session('success'))
        <div class="mb-4 rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800">Please fix the errors below.</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <form method="POST" action="{{ route('client.support.store') }}" class="space-y-6">
          @csrf
          <div>
            <label class="block text-sm font-medium text-gray-700">Subject</label>
            <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="200" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Brief summary of your issue" />
            @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Category (optional)</label>
              <input type="text" name="category" value="{{ old('category') }}" maxlength="100" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Billing, Technical, Account" />
              @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Priority</label>
              <select name="priority" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500">
                <option value="normal" {{ old('priority', 'normal')==='normal' ? 'selected' : '' }}>Normal</option>
                <option value="low" {{ old('priority')==='low' ? 'selected' : '' }}>Low</option>
                <option value="high" {{ old('priority')==='high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ old('priority')==='urgent' ? 'selected' : '' }}>Urgent</option>
              </select>
              @error('priority')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" rows="6" required maxlength="5000" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Describe your issue or question in detail...">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
          </div>

          <div class="flex items-center justify-end gap-3">
            <a href="{{ route('client.dashboard') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Submit Ticket</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>

