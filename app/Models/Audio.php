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
        'downloads_count',
        'views_count'
    ];

    protected $casts = [
        'license_price' => 'decimal:2',
        'is_active' => 'boolean',
        'downloads_count' => 'integer',
        'views_count' => 'integer',
        'bitrate' => 'integer',
        'sample_rate' => 'integer'
    ];

    // Accessor to generate the audio URL dynamically (same as web version)
    public function getAudioUrlAttribute()
    {
        if ($this->audio_path) {
            // If audio_path is already an absolute URL, return as is
            if (filter_var($this->audio_path, FILTER_VALIDATE_URL)) {
                return $this->audio_path;
            }
            // Use the same URL building method as the web version
            return asset('storage/' . $this->audio_path);
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
