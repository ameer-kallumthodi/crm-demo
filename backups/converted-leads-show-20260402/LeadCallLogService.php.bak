<?php

namespace App\Services;

use App\Models\ConvertedLead;
use App\Models\VoxbayCallLog;
use Illuminate\Support\Collection;

class LeadCallLogService
{
    /**
     * Fetch recent call logs related to the given converted lead using all known phone numbers.
     */
    public static function forConvertedLead(ConvertedLead $convertedLead, int $limit = 50): Collection
    {
        $phoneNumbers = self::extractPhoneNumbers($convertedLead);

        if ($phoneNumbers->isEmpty()) {
            return collect();
        }

        $callLogs = VoxbayCallLog::query()
            ->where(function ($query) use ($phoneNumbers) {
                foreach ($phoneNumbers as $number) {
                    $query->orWhere('destinationNumber', $number)
                          ->orWhere('calledNumber', $number)
                          ->orWhere('callerNumber', $number);
                }
            })
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->limit($limit)
            ->get();

        return $callLogs->map(function ($log) {
            $log->telecaller_name = $log->getTelecallerName();
            return $log;
        });
    }

    /**
     * Collect all possible phone numbers for a converted lead.
     */
    public static function extractPhoneNumbers(ConvertedLead $convertedLead): Collection
    {
        $numbers = collect();

        $numbers->push(self::mergePhone($convertedLead->code, $convertedLead->phone));

        if ($convertedLead->lead) {
            $numbers->push(self::mergePhone($convertedLead->lead->code, $convertedLead->lead->phone));
            $numbers->push(self::mergePhone($convertedLead->lead->whatsapp_code, $convertedLead->lead->whatsapp));
        }

        if ($convertedLead->leadDetail) {
            $numbers->push(self::mergePhone($convertedLead->leadDetail->personal_code, $convertedLead->leadDetail->personal_number));
            $numbers->push(self::mergePhone($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number));
            $numbers->push(self::mergePhone($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number));
        }

        return $numbers->filter()->unique()->values();
    }

    protected static function mergePhone(?string $code, ?string $number): ?string
    {
        if (!$code || !$number) {
            return null;
        }

        return trim($code) . trim($number);
    }
}

