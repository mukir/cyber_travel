<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Job;

class CommissionRules
{
    public static function computeRegion(?Job $job): ?string
    {
        if (!$job) return null;
        if (!empty($job->region)) {
            return strtolower($job->region);
        }
        $country = trim((string)$job->country);
        if ($country === '') return null;
        $c = strtolower($country);

        // Try countries table mapping first
        try {
            $hit = \App\Models\Country::whereRaw('LOWER(name) = ?', [$c])->orWhereRaw('LOWER(code) = ?', [$c])->first();
            if ($hit && $hit->region) {
                return strtolower($hit->region);
            }
        } catch (\Throwable $e) {}

        // Gulf Cooperation Council countries
        $gulf = ['saudi arabia','united arab emirates','uae','qatar','bahrain','kuwait','oman'];
        if (in_array($c, $gulf, true)) return 'gulf';

        // Americas (North, Central, South)
        $americas = [
            'united states','united states of america','usa','us','canada','mexico','guatemala','belize','honduras','el salvador','nicaragua','costa rica','panama',
            'colombia','venezuela','ecuador','peru','bolivia','chile','argentina','uruguay','paraguay','brazil','guyana','suriname','french guiana',
            'cuba','dominican republic','haiti','jamaica','trinidad and tobago','bahamas','barbados','saint lucia','grenada','antigua and barbuda','saint kitts and nevis','saint vincent and the grenadines'
        ];
        if (in_array($c, $americas, true)) return 'americas';

        // Europe (wide list, includes non-EU like UK, Norway, Switzerland)
        $europe = [
            'albania','andorra','armenia','austria','azerbaijan','belarus','belgium','bosnia and herzegovina','bulgaria','croatia','cyprus','czech republic','czechia',
            'denmark','estonia','finland','france','georgia','germany','greece','hungary','iceland','ireland','italy','kazakhstan','kosovo','latvia','liechtenstein','lithuania','luxembourg','malta','moldova','monaco','montenegro','netherlands','north macedonia','norway','poland','portugal','romania','russia','san marino','serbia','slovakia','slovenia','spain','sweden','switzerland','turkey','ukraine','united kingdom','uk','england','scotland','wales','northern ireland','vatican city'
        ];
        if (in_array($c, $europe, true)) return 'europe';

        return null;
    }

    public static function computeRegionCommission(?Job $job): ?float
    {
        $region = self::computeRegion($job);
        $europe   = (float) \App\Helpers\Settings::get('commission.region.europe', 10000);
        $gulf     = (float) \App\Helpers\Settings::get('commission.region.gulf', 5000);
        $americas = (float) \App\Helpers\Settings::get('commission.region.americas', 10000);
        if ($region === 'europe') return $europe;
        if ($region === 'gulf') return $gulf;
        if ($region === 'americas') return $americas;
        return null;
    }

    /**
     * Determine if a booking is fully paid (paid up).
     */
    public static function isBookingPaidUp(Booking $booking): bool
    {
        return ($booking->payment_status === 'full') || ((float)$booking->amount_paid + 0.01 >= (float)$booking->total_amount);
    }
}
