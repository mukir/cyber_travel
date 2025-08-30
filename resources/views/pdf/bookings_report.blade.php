<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>{{ $title }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111 }
    .muted { color: #666 }
    table { width: 100%; border-collapse: collapse; margin-top: 12px }
    th, td { border: 1px solid #ddd; padding: 6px; text-align: left }
    th { background: #f5f5f5 }
    .right { text-align: right }
  </style>
</head>
<body>
  <h2>{{ $title }}</h2>
  <div class="muted">Period: {{ $period[0]->format('Y-m-d') }} - {{ $period[1]->format('Y-m-d') }}</div>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Booking</th>
        <th>Client</th>
        <th>Job</th>
        <th>Package</th>
        <th>Status</th>
        <th class="right">Total</th>
        <th class="right">Paid</th>
        <th class="right">Balance</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rows as $b)
        <tr>
          <td>{{ $b->created_at->format('Y-m-d') }}</td>
          <td>BK{{ $b->id }}</td>
          <td>{{ $b->customer_name }}</td>
          <td>{{ $b->job?->name }}</td>
          <td>{{ $b->package?->name }}</td>
          <td class="capitalize">{{ $b->payment_status }}</td>
          <td class="right">{{ number_format($b->total_amount, 2) }} {{ $b->currency }}</td>
          <td class="right">{{ number_format($b->amount_paid, 2) }} {{ $b->currency }}</td>
          <td class="right">{{ number_format(max($b->total_amount - $b->amount_paid, 0), 2) }} {{ $b->currency }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

