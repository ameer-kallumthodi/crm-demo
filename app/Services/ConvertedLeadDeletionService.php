<?php

namespace App\Services;

use App\Helpers\AuthHelper;
use App\Models\ConvertedLead;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConvertedLeadDeletionService
{
    public function deletePermanently(ConvertedLead $convertedLead): void
    {
        DB::transaction(function () use ($convertedLead) {
            $convertedLead->loadMissing([
                'idCards',
                'mentorDetails',
            ]);

            foreach ($convertedLead->idCards as $idCard) {
                $this->deleteStorageFile($idCard->file_path);
            }

            if ($convertedLead->mentorDetails?->placement_resume) {
                $this->deleteStorageFile($convertedLead->mentorDetails->placement_resume);
            }

            Invoice::withTrashed()
                ->where('student_id', $convertedLead->id)
                ->each(function (Invoice $invoice) {
                    $invoice->paymentLinks()->delete();
                    $invoice->payments()->withTrashed()->forceDelete();
                    $invoice->forceDelete();
                });

            $convertedLead->subjectAreas()->detach();

            $convertedLead->placementRemarkHistories()->delete();
            $convertedLead->placementScheduledInterviews()->delete();
            $convertedLead->placementMockTestDetails()->delete();
            $convertedLead->supportFeedbackHistory()->delete();
            $convertedLead->supportDetails()->delete();
            $convertedLead->mentorDetails()->delete();
            $convertedLead->convertedStudentActivities()->delete();
            $convertedLead->studentDetails()->delete();
            $convertedLead->idCards()->delete();

            $userId = AuthHelper::getCurrentUserId();
            if ($userId) {
                $convertedLead->deleted_by = $userId;
                $convertedLead->saveQuietly();
            }

            $convertedLead->forceDelete();
        });
    }

    private function deleteStorageFile(?string $path): void
    {
        if (! filled($path)) {
            return;
        }

        foreach (['public', 'local'] as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }
            } catch (\Throwable) {
                // Best-effort file cleanup; DB delete still proceeds.
            }
        }
    }
}
