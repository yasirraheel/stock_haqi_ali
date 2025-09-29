<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PhotoCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'status'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get photos that belong to this category
     */
    public function photos()
    {
        return $this->hasMany(Photos::class, 'category', 'name');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the route key name for Laravel route model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
