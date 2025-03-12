<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;
use Veelasky\LaravelHashId\Eloquent\HashableId;

use App\Notifications\PasswordResetNotif;
use App\Notifications\SendEmailVerification;

use App\Traits\PaginatesTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HashableId, PaginatesTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["username", "email", "password", "fails", "email_verified_at", "disabled_at"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "disabled_at" => "datetime",
            "password" => "hashed",
        ];
    }

    public function isSuperman(): bool
    {
        return $this->hasRole(config("mitd.superman"));
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function resetFailedLoginAttempts()
    {
        $this->fails = 0;
        $this->save();
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token)
    {
        $this->notify(new PasswordResetNotif($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new SendEmailVerification());
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
