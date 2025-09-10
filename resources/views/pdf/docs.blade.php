<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>System Documentation</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
    h1 { font-size: 20px; margin: 0 0 12px; }
    h2 { font-size: 16px; margin: 18px 0 8px; }
    p, li { line-height: 1.5; }
    ul { margin: 6px 0 10px 18px; }
    .section { margin-bottom: 12px; }
    .code { background: #f5f5f5; border: 1px solid #ddd; padding: 6px; }
  </style>
  </head>
<body>
  <h1>System Documentation</h1>

  <div class="section">
    <h2>Overview</h2>
    <p>This document outlines roles, features, and workflows for staff management, leads, reception, commissions, payouts, countries/regions, and payments.</p>
  </div>

  <div class="section">
    <h2>User Roles & Portals</h2>
    <ul>
      <li><b>Admin</b>: Full management, reports, payouts, settings.</li>
      <li><b>Staff</b>: Leads, clients, commissions, reports, referrals.</li>
      <li><b>Reception</b>: Client status and visitors book; staff contact.</li>
      <li><b>Client</b>: Applications, documents, and payments.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Staff Management</h2>
    <ul>
      <li>Add staff; invite/reset password.</li>
      <li>Edit, activate/deactivate; promote to Admin; convert Staff ⇆ Reception.</li>
      <li>Search/filter/paginate; Staff Show page centralizes actions and metrics.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Leads</h2>
    <p>Staff: filters, add lead, add notes, see history. Admin: manage with filters, edit/delete, add notes on Lead view.</p>
  </div>

  <div class="section">
    <h2>Reception</h2>
    <ul>
      <li>Dashboard: today’s visitors, open applications.</li>
      <li>Client Status: search by name/email/phone/ID; status; assigned staff (email/call).</li>
      <li>Visitors Book: record visitor (name, national ID, phone, email, notes) and browse entries.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Commissions</h2>
    <ul>
      <li>Region fixed: Europe/Gulf/Americas (configurable), awarded on full payment.</li>
      <li>Weekly Passport Bonus: per 3 fully paid clients with validated passport (configurable).</li>
      <li>Retainer: granted when target achieved in its period (configurable).</li>
      <li>Staff Commissions page shows amounts, types, outstanding; CSV/PDF exports.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Payouts</h2>
    <ul>
      <li>Monthly (default 15th; configurable). Pending/Paid summaries; CSV exports.</li>
      <li>Mark As Paid: confirmation; optional staff emails. Batches logged for audit.</li>
      <li>Console: <span class="code">php artisan commissions:payout</span></li>
    </ul>
  </div>

  <div class="section">
    <h2>Countries & Job Regions</h2>
    <ul>
      <li>Admin → Countries: manage name, code, region.</li>
      <li>Jobs: set destination country + optional region override (drives commissions).</li>
    </ul>
  </div>

  <div class="section">
    <h2>Payments & Outstanding</h2>
    <ul>
      <li>Outstanding = Total − Paid across staff and admin views.</li>
      <li>Exports include Outstanding where applicable.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Settings</h2>
    <ul>
      <li>Region fixed amounts, Weekly Passport Bonus, Retainer amount.</li>
      <li>Payout Day and Admin Email (for internal notifications).</li>
    </ul>
  </div>

  <div class="section">
    <h2>Automation & Scheduling (server cron)</h2>
    <div class="code">0 2 15 * * cd /path/to/app && php artisan commissions:payout >> storage/logs/payout.log 2>&1<br>
0 3 * * 0 cd /path/to/app && php artisan sales:weekly-passport-bonus >> storage/logs/bonus.log 2>&1</div>
  </div>
</body>
</html>

