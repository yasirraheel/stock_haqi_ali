<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioDriveFile extends Model
{
    use HasFactory;

    protected $table = 'audio_drive_files';

    protected $fillable = [
        'folder_id',
        'file_id',
        'name',
        'mime_type',
        'size',
        'url',
        'audio_id',
        'status'
    ];

    /**
     * Relationship with Audio model.
     */
    public function audio()
    {
        return $this->belongsTo(Audio::class, 'audio_id');
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

    /**
     * Get non-blocking streaming preview URL for audio player.
     *
     * @return string
     */
    public function getStreamUrlAttribute()
    {
        return route('api.v1.audio.stream', 10000 + $this->id);
    }
}
