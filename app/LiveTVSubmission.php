<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveTVSubmission extends Model
{
    protected $table = 'live_tv_submissions';

    protected $fillable = [
        'user_id', 'live_tv_id', 'channel_name', 'channel_description',
        'channel_access', 'channel_cat_id', 'channel_url_type', 'channel_url',
        'channel_url2', 'channel_url3', 'channel_thumb', 'seo_title',
        'seo_description', 'seo_keyword', 'status', 'admin_notes',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function liveTv()
    {
        return $this->belongsTo(LiveTV::class, 'live_tv_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
