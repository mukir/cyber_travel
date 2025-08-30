<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class AdminReportController extends Controller
{
    private function range(string $period): array
    {
        return match ($period) {
            'daily' => [now()->startOfDay(), now()->endOfDay()],
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->subYear(), now()],
        };
    }

    public function bookingsCsv(string $period)
    {
        [$from, $to] = $this->range($period);
        $rows = Booking::with(['user','job','package'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $out = fopen('php://temp', 'w+');
        fputcsv($out, ['Date','Booking','Client','Job','Package','Status','Total','Paid','Balance']);
        foreach ($rows as $b) {
            fputcsv($out, [
                $b->created_at->format('Y-m-d H:i'),
                'BK'.$b->id,
                $b->customer_name,
                optional($b->job)->name,
                optional($b->package)->name,
                $b->payment_status,
                number_format($b->total_amount, 2),
                number_format($b->amount_paid, 2),
                number_format(max($b->total_amount - $b->amount_paid, 0), 2),
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_'.$period.'.csv"',
        ]);
    }

    public function bookingsPdf(string $period)
    {
        [$from, $to] = $this->range($period);
        $rows = Booking::with(['user','job','package'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bookings_report', [
            'rows' => $rows,
            'title' => ucfirst($period).' Bookings Report',
            'period' => [$from, $to],
        ]);
        return $pdf->download('bookings_'.$period.'.pdf');
    }

    public function emailReport(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:payments,bookings',
            'period' => 'required|in:daily,weekly,monthly',
            'email' => 'required|email',
        ]);

        [$from, $to] = $this->range($data['period']);

        if ($data['type'] === 'bookings') {
            $rows = \App\Models\Booking::with(['user','job','package'])
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at')->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bookings_report', [
                'rows' => $rows,
                'title' => ucfirst($data['period']).' Bookings Report',
                'period' => [$from, $to],
            ]);
            $content = $pdf->output();
            $filename = 'bookings_'.$data['period'].'.pdf';
        } else {
            $rows = \App\Models\Payment::with('booking')
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at')->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payments', ['rows' => $rows]);
            $content = $pdf->output();
            $filename = 'payments_'.$data['period'].'.pdf';
        }

        \Illuminate\Support\Facades\Mail::raw('Please find the attached report.', function ($m) use ($data, $content, $filename) {
            $m->to($data['email'])->subject('Auto Report: '.$filename)
              ->attachData($content, $filename, ['mime' => 'application/pdf']);
        });

        return back()->with('success', 'Report emailed to '.$data['email']);
    }

    public function commissionsCsv(string $period)
    {
        [$from, $to] = $this->range($period);
        $rows = \App\Models\Commission::with(['payment.booking','staff'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $out = fopen('php://temp', 'w+');
        fputcsv($out, ['Date','Staff','Booking','Client','Method','Payment','Rate %','Commission']);
        foreach ($rows as $c) {
            $p = $c->payment; $b = optional($p)->booking;
            fputcsv($out, [
                optional($c->created_at)->format('Y-m-d H:i'),
                optional($c->staff)->name,
                $b ? ('BK'.$b->id) : '',
                optional($b)->customer_name,
                optional($p)->method,
                $p ? number_format($p->amount, 2) : '0.00',
                $c->rate,
                number_format($c->amount, 2),
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="commissions_'.$period.'.csv"',
        ]);
    }

    public function commissionsPdf(string $period)
    {
        [$from, $to] = $this->range($period);
        $rows = \App\Models\Commission::with(['payment.booking','staff'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get()
            ->map(function ($c) {
                $p = $c->payment; $b = optional($p)->booking;
                return [
                    'date' => optional($c->created_at)->format('Y-m-d H:i'),
                    'booking' => $b ? ('BK'.$b->id) : '',
                    'client' => optional($b)->customer_name,
                    'method' => optional($p)->method,
                    'amount' => $p ? number_format($p->amount, 2) : '0.00',
                    'rate' => $c->rate,
                    'commission' => number_format($c->amount, 2),
                ];
            });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.commissions', [
            'rows' => $rows,
            'title' => 'Commission Report ('.ucfirst($period).')',
            'period' => [$from, $to],
        ]);
        return $pdf->download('commissions_'.$period.'.pdf');
    }
}
