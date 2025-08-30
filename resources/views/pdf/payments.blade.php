<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Payments</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111 }
    table { width: 100%; border-collapse: collapse; margin-top: 12px }
    th, td { border: 1px solid #ddd; padding: 6px; text-align: left }
    th { background: #f5f5f5 }
    .right { text-align: right }
  </style>
</head>
<body>
  <h2>Payments Report</h2>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Booking</th>
        <th>Client</th>
        <th>Method</th>
        <th>Status</th>
        <th class="right">Amount</th>
        <th>Reference</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $p)
        <tr>
          <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
          <td>BK{{ $p->booking_id }}</td>
          <td>{{ $p->booking?->customer_name }}</td>
          <td class="capitalize">{{ $p->method }}</td>
          <td class="capitalize">{{ $p->status }}</td>
          <td class="right">{{ number_format($p->amount, 2) }}</td>
          <td>{{ $p->receipt_number ?? $p->reference }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

