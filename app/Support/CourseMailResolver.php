<?php

namespace App\Support;

use App\Models\ConvertedLead;
use App\Models\CourseMail;

class CourseMailResolver
{
    /**
     * Resolve course mail template for a converted lead (course + batch + admission batch).
     * Prefers an exact admission-batch match, then falls back to "all admission batches" (null).
     */
    public static function resolveForConvertedLead(ConvertedLead $convertedLead): ?CourseMail
    {
        $courseId = (int) $convertedLead->course_id;
        $batchId = (int) $convertedLead->batch_id;

        if (! $courseId || ! $batchId) {
            return null;
        }

        $baseQuery = CourseMail::query()
            ->where('course_id', $courseId)
            ->where('batch_id', $batchId);

        $admissionBatchId = $convertedLead->admission_batch_id
            ? (int) $convertedLead->admission_batch_id
            : null;

        if ($admissionBatchId) {
            $exact = (clone $baseQuery)
                ->where('admission_batch_id', $admissionBatchId)
                ->first();

            if ($exact) {
                return $exact;
            }
        }

        $allBatches = (clone $baseQuery)
            ->whereNull('admission_batch_id')
            ->first();

        if ($allBatches) {
            return $allBatches;
        }

        return (clone $baseQuery)
            ->whereNotNull('admission_batch_id')
            ->orderByDesc('updated_at')
            ->first();
    }

    public static function defaultSubject(ConvertedLead $convertedLead): string
    {
        $courseTitle = $convertedLead->course?->title ?? 'Course';

        return $courseTitle.' - Important Information';
    }
}
