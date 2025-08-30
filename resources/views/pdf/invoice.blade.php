<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Invoice {{ $number }}</title>
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
        <h2>Invoice</h2>
        <div class="muted">{{ config('app.name') }}</div>
      </div>
      <div>
        <div><strong>No:</strong> {{ $number }}</div>
        <div class="muted">Date: {{ now()->format('Y-m-d') }}</div>
      </div>
    </div>

    <div>
      <div><strong>Billed To:</strong> {{ $booking->customer_name }}</div>
      <div class="muted">{{ $booking->customer_email }} {{ $booking->customer_phone ? '• '.$booking->customer_phone : '' }}</div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Description</th>
          <th class="right">Qty</th>
          <th class="right">Unit Price</th>
          <th class="right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $booking->job?->name }} — {{ $booking->package?->name }}</td>
          <td class="right">{{ $booking->quantity }}</td>
          <td class="right">{{ number_format(($booking->total_amount / max($booking->quantity,1)), 2) }} {{ $booking->currency }}</td>
          <td class="right">{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="right">Total</th>
          <th class="right">{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</th>
        </tr>
        <tr>
          <th colspan="3" class="right">Paid</th>
          <th class="right">{{ number_format($booking->amount_paid, 2) }} {{ $booking->currency }}</th>
        </tr>
        <tr>
          <th colspan="3" class="right">Balance</th>
          <th class="right">{{ number_format(max($booking->total_amount - $booking->amount_paid, 0), 2) }} {{ $booking->currency }}</th>
        </tr>
      </tfoot>
    </table>
  </body>
</html>

