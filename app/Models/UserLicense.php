<?php

namespace App\Models;

use App\Movies;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLicense extends Model
{
    use HasFactory;

    // Define the table name (if it's not pluralized automatically)
    protected $table = 'users_licenses';

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'buyer_email',
        'buyer_name',
        'video_id',
        'gateway',
        'license_price',
        'license_key',
        'payment_id',
        'author_paypal_email',
        'admin_commission',
        'amount_paid_to_author',
        'author_payment_id',
    ];

    /**
     * Relationship: Each license belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Each license is linked to a movie (video).
     */
    public function movie()
    {
        return $this->belongsTo(Movies::class, 'video_id');
    }
}
