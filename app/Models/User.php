<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Twilio\Rest\Client;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mobile_number',
        'user_name',
        'email',
        'password',
        'code',
        'expiry_date',
        'is_verified',
        'role',
        'expire',
        'birthdate'
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



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function generateCode(){
        $this->timestamps = false;
        $this->code = rand(1000,9999);
        $this->expiry_date = now()->addMinutes(10);
        $this->sendSms('+2'.$this->mobile_number, "Your verification code is {$this->code}");
        $this->save();
    }
    public function removeGeneratedCode(){
        $this->code = null;
        $this->expiry_date = null;
        $this->save();
    }
    public function verifyCode($code)
    {
        return $this->code === $code && now()->lessThanOrEqualTo($this->code_expiry_date);
    }

    private function sendSms($to, $message)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');
        $client = new Client($sid, $token);

        $client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }
}
