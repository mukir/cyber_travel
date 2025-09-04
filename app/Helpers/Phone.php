<?php

namespace App\Helpers;

class Phone
{
    /**
     * Normalize a phone number to E.164 digits (without plus),
     * defaulting to a given country code (e.g., '254' for Kenya).
     * This is a lightweight heuristic suitable for local numbers like 07XXXXXXXX.
     */
    public static function toE164Digits(?string $phone, string $defaultCountryCode = '254'): ?string
    {
        if (!$phone) return null;
        $p = trim($phone);
        // Keep only digits and plus for initial parsing
        $p = preg_replace('/[^\d\+]/', '', $p) ?? '';
        if ($p === '') return null;

        // Normalize to digits only
        $digits = preg_replace('/\D/', '', $p) ?? '';
        if ($digits === '') return null;

        // Handle cases like "+2547..." (already international) — already handled by $digits
        // Handle local formats starting with 0 (e.g., 07XXXXXXXX)
        if (str_starts_with($digits, '0')) {
            $digits = $defaultCountryCode . substr($digits, 1);
        }

        // Handle mistakenly stored numbers like 25407XXXXXXXX -> 2547XXXXXXXX
        $cc = $defaultCountryCode;
        if (str_starts_with($digits, $cc . '0')) {
            // e.g., 2540XXXXXXXXX => drop the 0 after country code
            $digits = $cc . substr($digits, strlen($cc) + 1);
        }

        // If a bare local mobile like 7XXXXXXXX exists, prefix country code
        if (preg_match('/^7\d{8}$/', $digits)) {
            $digits = $cc . $digits;
        }

        // If it already starts with a country code (including non-default), leave as is
        // Final validation: must be 8-15 digits and not start with 0 (WhatsApp requirement)
        if (!preg_match('/^[1-9]\d{7,14}$/', $digits)) {
            return null;
        }

        return $digits;
    }
}
