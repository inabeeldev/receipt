<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Exception;
use App\Models\UserCode;
use App\Mail\SendCodeMail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'business_name',
        'business_type',
        'password',
        'username',
        'contact',
        'address',
        'number_of_products',
        'logo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // public function enableTwoFactorAuthentication()
    // {
    //     $this->two_factor_secret = $this->hasEnabledTwoFactorAuthentication();
    //     $this->save();

    //     return $this->two_factor_secret;
    // }

    // public function disableTwoFactorAuthentication()
    // {
    //     $this->two_factor_secret = null;
    //     $this->two_factor_recovery_codes = null;
    //     $this->save();
    // }

    public function generateCode($user_id)

    {
        $user = User::find($user_id);
        $code = rand(1000, 9999);

        UserCode::updateOrCreate(

            [ 'user_id' => $user_id ],

            [ 'code' => $code ]

        );

        try {

            $details = [

                'title' => 'Mail from Receipt Management',
                'code' => $code

            ];

            Mail::to($user->email)->send(new SendCodeMail($details));

        } catch (Exception $e) {

            info("Error: ". $e->getMessage());

        }

    }



}
