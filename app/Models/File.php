<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'original_name',
        'stored_name',
        'file_path',
        'file_size',
        'mime_type',
        'extension',
        'password',
        'short_code',
        'qr_code_path',
        'expires_at',
        'max_downloads',
        'downloads_count',
        'ip_address',
        'user_agent',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'max_downloads' => 'integer',
        'downloads_count' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the downloads for the file.
     */
    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Get the activity logs for the file.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if file has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if download limit has been reached.
     */
    public function hasReachedDownloadLimit(): bool
    {
        return $this->max_downloads && $this->downloads_count >= $this->max_downloads;
    }

    /**
     * Check if file is available for download.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && !$this->isExpired() && !$this->hasReachedDownloadLimit();
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope for active files.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for available files (not expired, not reached limit).
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }
}
