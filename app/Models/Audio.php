<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'audio_path',
        'license_price',
        'duration',
        'file_size',
        'format',
        'bitrate',
        'sample_rate',
        'genre',
        'mood',
        'tags',
        'is_active',
        'status',
        'added_by',
        'downloads_count',
        'views_count'
    ];

    /**
     * User who submitted/uploaded this audio.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'added_by');
    }

    protected $casts = [
        'license_price' => 'decimal:2',
        'is_active' => 'boolean',
        'downloads_count' => 'integer',
        'views_count' => 'integer',
        'bitrate' => 'integer',
        'sample_rate' => 'integer'
    ];

    // Accessor to generate the audio URL dynamically (with CORS-enabled streaming proxy for GDrive)
    public function getAudioUrlAttribute()
    {
        if ($this->audio_path) {
            // If it's a Google Drive file, use our non-blocking streaming proxy
            if ($this->drive_file_id || strpos($this->audio_path, 'drive.google.com') !== false) {
                return route('api.v1.audio.stream', $this->id);
            }
            if (filter_var($this->audio_path, FILTER_VALIDATE_URL)) {
                return $this->audio_path;
            }
            return asset('storage/' . $this->audio_path);
        }
        return null;
    }

    /**
     * Extract Google Drive File ID if audio_path points to Google Drive.
     *
     * @return string|null
     */
    public function getDriveFileIdAttribute()
    {
        if (!$this->audio_path) {
            return null;
        }

        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $this->audio_path, $matches)) {
            return $matches[1];
        }

        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $this->audio_path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    // Scope for active audios
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for premium audios
    public function scopePremium($query)
    {
        return $query->whereNotNull('license_price')->where('license_price', '>', 0);
    }

    // Scope for free audios
    public function scopeFree($query)
    {
        return $query->where(function($q) {
            $q->whereNull('license_price')->orWhere('license_price', 0);
        });
    }
}