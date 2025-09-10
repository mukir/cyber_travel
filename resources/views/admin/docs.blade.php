<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">System Documentation</h2>
      <a href="{{ route('admin.docs.pdf') }}" class="rounded bg-indigo-600 px-4 py-2 text-white text-sm font-semibold">Download PDF</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6 text-sm leading-6">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Overview</h3>
        <p>This document explains the roles, features, and workflows of the system, including staff management, lead handling, reception tools, commissions, payouts, countries/regions, and payments.</p>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">User Roles & Portals</h3>
        <ul class="list-disc pl-5">
          <li><b>Admin</b>: Full access to all management areas, reports, payouts, and settings.</li>
          <li><b>Staff</b>: Leads, clients, commissions, reports, referrals.</li>
          <li><b>Reception</b>: Client status and visitors book; view assigned staff contact.</li>
          <li><b>Client</b>: Applications, documents, and payments.</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Staff Management (Admin → Staff)</h3>
        <ul class="list-disc pl-5">
          <li>Add staff (name, email, phone, password) and send invite/reset link.</li>
          <li>Edit details, activate/deactivate (affects round-robin assignment).</li>
          <li>Promote to Admin; convert Staff ⇆ Reception with safety checks.</li>
          <li>Search, filter (active/inactive), paginate (15 per page).</li>
          <li>Staff Show page centralizes actions and metrics.</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Leads</h3>
        <p><b>Staff:</b> Filter by stage/status/date, add new leads, add notes with follow-up dates, and view lead details and history. <b>Admin:</b> Manage leads with filters, edit/delete, and add notes on the lead view page.</p>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Reception</h3>
        <ul class="list-disc pl-5">
          <li><b>Dashboard</b>: Today’s visitors, count of open applications.</li>
          <li><b>Client Status</b>: Search name/email/phone/ID; see status and assigned staff; email/call staff.</li>
          <li><b>Visitors Book</b>: Record visitor (name, national ID, phone, email, notes) and browse recent entries.</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Commissions</h3>
        <ul class="list-disc pl-5">
          <li><b>Region-based (fixed)</b>: Awarded once when a referred booking is fully paid: Europe (KES configurable), Gulf (KES configurable), Americas (KES configurable).</li>
          <li><b>Weekly Passport Bonus</b>: Every 3 fully paid clients with validated passport in the week → bonus (KES configurable). Command: <code>php artisan sales:weekly-passport-bonus</code>.</li>
          <li><b>Retainer</b>: If staff meets target within its period, a retainer commission (KES configurable) is created for the month.</li>
          <li><b>Staff View</b>: Commissions page shows amounts (type, outstanding, totals) with CSV/PDF exports.</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Payouts (Admin → Payouts)</h3>
        <ul class="list-disc pl-5">
          <li><b>Monthly</b>: Run on payout day (default 15th) for previous month; configurable in Settings.</li>
          <li><b>Pending/Paid</b>: Review per-staff totals; export aggregate/detailed CSV.</li>
          <li><b>Mark As Paid</b>: Requires typing CONFIRM; optional staff payout emails. Batches are logged for audit.</li>
          <li><b>Console</b>: <code>php artisan commissions:payout</code> (schedules recommended).</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Countries & Job Regions</h3>
        <ul class="list-disc pl-5">
          <li><b>Countries (Admin → Countries)</b>: Manage name, code, region (Europe/Gulf/Americas/Other).</li>
          <li><b>Jobs</b>: Set Destination Country (with suggestions) and optional Region override; used for region commissions.</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Payments & Outstanding</h3>
        <ul class="list-disc pl-5">
          <li>Outstanding = Total − Paid shown across staff (clients, referrals, commissions) and admin (sales, payments).</li>
          <li>Exports include Outstanding columns where applicable (CSV/PDF).</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Settings (Admin → Settings → Commissions/Company)</h3>
        <ul class="list-disc pl-5">
          <li>Region fixed amounts, Weekly Passport Bonus, Retainer amount.</li>
          <li>Payout Day (1–28) and Company Admin Email for internal notifications.</li>
        </ul>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Automation & Scheduling (server cron)</h3>
        <pre class="bg-gray-50 p-3 rounded border overflow-auto">0 2 15 * * cd /path/to/app && php artisan commissions:payout >> storage/logs/payout.log 2>&1
0 3 * * 0 cd /path/to/app && php artisan sales:weekly-passport-bonus >> storage/logs/bonus.log 2>&1</pre>
      </div>
    </div>
  </div>
</x-app-layout>

