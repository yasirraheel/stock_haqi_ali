<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'users';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'user_image', 'mobile', 'remember_token','usertype','paypal_email'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array
     */
    protected $attributes = [
        'usertype' => 'Sub_Admin',
    ];

    public static function getUserInfo($id)
    {
        return User::find($id);
    }

    public static function getUserFullname($id)
    {
        $userinfo = User::find($id);

        if ($userinfo) {
            return $userinfo->name;
        } else {
            return '';
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPassword($token));
    }
}

class CustomPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url('password/reset/' . $this->token);

        return (new MailMessage)
            ->subject('Reset Password')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.password', ['url' => $url]);
    }
}
