<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleDriveApi extends Model
{
    // Table name
    protected $table = 'google_drive_api';

    // Fillable fields
    protected $fillable = ['api_key','email','calls'];

    // Timestamps are automatically managed by Laravel (created_at, updated_at)
    public $timestamps = true;
}
