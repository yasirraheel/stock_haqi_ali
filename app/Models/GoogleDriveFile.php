<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleDriveFile extends Model
{
    use HasFactory;

    protected $table = 'google_drive_files';

    protected $fillable = [
        'folder_id',
        'file_id',
        'name',
        'mime_type',
        'size',
        'url',
        'effect_id',
        'status'
    ];

    /**
     * Relationship with Effect model.
     */
    public function effect()
    {
        return $this->belongsTo(Effect::class, 'effect_id');
    }

    /**
     * Get human-readable file size.
     *
     * @return string
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}
