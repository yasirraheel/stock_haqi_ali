<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Effect extends Model
{
    use HasFactory;

    protected $table = 'effects';

    protected $fillable = [
        'title',
        'description',
        'effect_url',
        'category',
        'license_price',
        'is_premium',
        'is_active',
        'status',
        'added_by',
        'downloads_count',
        'views_count'
    ];

    /**
     * User who submitted this effect.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'added_by');
    }

    /**
     * Extract Google Drive File ID if effect_url points to Google Drive.
     */
    public function getDriveFileIdAttribute()
    {
        if (!$this->effect_url) {
            return null;
        }

        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $this->effect_url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $this->effect_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected $casts = [
        'license_price' => 'decimal:2',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
        'downloads_count' => 'integer',
        'views_count' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
