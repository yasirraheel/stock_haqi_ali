<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScannedFolder extends Model
{
    use HasFactory;

    protected $table = 'scanned_folders';

    protected $fillable = [
        'type',
        'folder_id',
        'folder_name',
        'folder_url',
        'total_files',
        'imported_files',
        'pending_files',
        'blocked_files',
        'last_scanned_at',
    ];

    protected $dates = [
        'last_scanned_at',
        'created_at',
        'updated_at',
    ];

    public function scopeForType($query, $type)
    {
        return $query->where('type', $type);
    }
}