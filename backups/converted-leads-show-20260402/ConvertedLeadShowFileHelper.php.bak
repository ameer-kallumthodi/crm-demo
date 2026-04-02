<?php

namespace App\Support;

use App\Models\ConvertedLead;
use Illuminate\Support\Facades\Storage;

class ConvertedLeadShowFileHelper
{
    /**
     * Pre-resolve public disk existence for paths rendered on the converted lead show page.
     */
    public static function publicExistenceMap(ConvertedLead $convertedLead): array
    {
        $paths = [];
        $doc = $convertedLead->leadDetail;
        if ($doc) {
            foreach ([
                'passport_photo', 'adhar_front', 'adhar_back', 'signature', 'birth_certificate',
                'plustwo_certificate', 'ug_certificate', 'pg_certificate', 'other_document', 'sslc_certificate',
            ] as $field) {
                if (! empty($doc->{$field})) {
                    $paths[] = $doc->{$field};
                }
            }
            foreach ($doc->sslcCertificates ?? [] as $cert) {
                if (! empty($cert->certificate_path)) {
                    $paths[] = $cert->certificate_path;
                }
            }
        }
        $mentor = $convertedLead->mentorDetails;
        if ($mentor && ! empty($mentor->placement_resume)) {
            $paths[] = $mentor->placement_resume;
        }

        $paths = array_values(array_unique(array_filter($paths)));
        if ($paths === []) {
            return [];
        }

        $disk = Storage::disk('public');
        $meta = [];
        foreach ($paths as $path) {
            $meta[$path] = $disk->exists($path);
        }

        return $meta;
    }
}
