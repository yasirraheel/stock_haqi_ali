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
        'downloads_count',
        'views_count'
    ];

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
