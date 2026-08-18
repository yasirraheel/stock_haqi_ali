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
        $cachedFile = public_path('audio_previews/' . $this->file_id . '.mp3');
        if (file_exists($cachedFile) && filesize($cachedFile) > 1000) {
            return asset('audio_previews/' . $this->file_id . '.mp3');
        }
        return route('admin.audio-drive-files.stream', $this->file_id);
    }
}
