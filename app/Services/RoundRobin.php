<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoundRobin
{
    /**
     * Get the next staff user id in round-robin order and persist the pointer.
     * Uses settings key round_robin.{key}_last_id (default: sales_rep).
     * Returns null when no staff users exist.
     */
    public static function nextStaffId(string $key = 'sales_rep'): ?int
    {
        $settingKey = "round_robin.{$key}_last_id";

        return DB::transaction(function () use ($settingKey) {
            $row = Setting::where('key', $settingKey)->lockForUpdate()->first();
            $lastId = $row ? (int) $row->value : null;

            $baseQuery = User::where('role', UserRole::Staff)
                ->where('is_active', true)
                ->orderBy('id');

            $candidate = null;
            if (!empty($lastId)) {
                $candidate = (clone $baseQuery)->where('id', '>', $lastId)->first();
            }
            if (!$candidate) {
                $candidate = $baseQuery->first();
            }
            if (!$candidate) {
                return null; // no staff available
            }

            Setting::updateOrCreate(['key' => $settingKey], ['value' => (string) $candidate->id]);
            return (int) $candidate->id;
        }, 5);
    }
}
