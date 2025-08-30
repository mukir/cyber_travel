<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Receipt BK{{ $booking->id }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111 }
    .header { display:flex; justify-content:space-between; margin-bottom: 16px }
    .muted { color: #666 }
    table { width: 100%; border-collapse: collapse; margin-top: 12px }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left }
    th { background: #f5f5f5 }
    .right { text-align: right }
  </style>
  </head>
  <body>
    <div class="header">
      <div>
        <h2>Receipt</h2>
        <div class="muted">{{ config('app.name') }}</div>
      </div>
      <div>
        <div><strong>Booking:</strong> BK{{ $booking->id }}</div>
        <div class="muted">Date: {{ now()->format('Y-m-d') }}</div>
      </div>
    </div>

    <div>
      <div><strong>Received From:</strong> {{ $booking->customer_name }}</div>
      <div class="muted">{{ $booking->customer_email }} {{ $booking->customer_phone ? '• '.$booking->customer_phone : '' }}</div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Method</th>
          <th>Reference</th>
          <th class="right">Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($booking->payments()->where('status','paid')->orderBy('created_at')->get() as $p)
          <tr>
            <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
            <td class="capitalize">{{ $p->method }}</td>
            <td>{{ $p->receipt_number ?? $p->reference }}</td>
            <td class="right">{{ number_format($p->amount, 2) }} {{ $booking->currency }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4">No paid transactions yet.</td>
          </tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="right">Total Paid</th>
          <th class="right">{{ number_format($booking->amount_paid, 2) }} {{ $booking->currency }}</th>
        </tr>
      </tfoot>
    </table>
  </body>
</html>

