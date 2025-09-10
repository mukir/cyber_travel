<html>
<head>
    <meta charset="utf-8" />
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
    <title>{{ $title ?? 'Commissions' }}</title>
    </head>
<body>
    <h1>{{ $title ?? 'Commissions' }}</h1>
    @if(!empty($period))
        <p>Period: {{ $period[0]->format('Y-m-d') }} to {{ $period[1]->format('Y-m-d') }}</p>
    @endif
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Booking</th>
                <th>Client</th>
                <th>Method</th>
                <th>Amount</th>
                <th>Outstanding</th>
                <th>Type</th>
                <th>Rate %</th>
                <th>Commission</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td>{{ $r['date'] }}</td>
                    <td>{{ $r['booking'] }}</td>
                    <td>{{ $r['client'] }}</td>
                    <td>{{ $r['method'] }}</td>
                    <td>{{ $r['amount'] }}</td>
                    <td>{{ $r['outstanding'] ?? '0.00' }}</td>
                    <td>{{ $r['type'] ?? '' }}</td>
                    <td>{{ $r['rate'] }}</td>
                    <td>{{ $r['commission'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
