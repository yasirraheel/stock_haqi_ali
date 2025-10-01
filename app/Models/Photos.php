<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Photos extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image_name', 'image_path',
        'tags', 'category', 'status',
        'file_name', 'file_size', 'file_type', 'mime_type', 'width', 'height',
        'resolution', 'color_space', 'bit_depth', 'has_transparency',
        'camera_make', 'camera_model', 'lens_model', 'focal_length', 'aperture',
        'shutter_speed', 'iso', 'flash', 'white_balance', 'date_taken',
        'gps_latitude', 'gps_longitude', 'gps_location', 'orientation',
        'copyright', 'artist', 'keywords', 'download_count', 'view_count', 'added_by'
    ];

    protected $casts = [
        'date_taken' => 'datetime',
        'has_transparency' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    /**
     * Get the user who uploaded this photo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // Accessor to generate the image URL dynamically
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            // If image_path is already an absolute URL, return as is
            if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
                return $this->image_path;
            }
            // Otherwise, generate URL from relative path
            return url('upload/photos/' . $this->image_path);
        }
        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = (int)$this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get image dimensions as string
     */
    public function getDimensionsAttribute()
    {
        if ($this->width && $this->height) {
            return $this->width . ' x ' . $this->height;
        }

        return null;
    }

    /**
     * Scope for active photos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('tags', 'LIKE', "%{$search}%")
              ->orWhere('keywords', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope for category filter
     */
    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }

        return $query;
    }
}
