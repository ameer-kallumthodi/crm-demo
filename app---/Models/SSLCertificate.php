<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SSLCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sslc_certificates';

    protected $fillable = [
        'lead_detail_id',
        'converted_student_detail_id',
        'certificate_path',
        'original_filename',
        'file_type',
        'file_size',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the lead detail that owns the SSLC certificate.
     */
    public function leadDetail(): BelongsTo
    {
        return $this->belongsTo(LeadDetail::class);
    }

    /**
     * Get the converted student detail that owns the SSLC certificate.
     */
    public function convertedStudentDetail(): BelongsTo
    {
        return $this->belongsTo(ConvertedStudentDetail::class);
    }

    /**
     * Get the user who verified the certificate.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the full URL for the certificate file.
     */
    public function getCertificateUrlAttribute(): string
    {
        return asset('storage/' . $this->certificate_path);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the certificate is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if the certificate is pending verification.
     */
    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

}