<?php

namespace App\Models;

use App\Models\Traits\User\UserRelationships;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, UserRelationships;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new EmailVerificationNotification);
    }

    /**
     * The attributes that are mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_type', 'email', 'password', 'address', 'city', 'postal_code', 'phone', 'country', 'provider_id',
        'email_verified_at', 'verification_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function homePage(): string
    {
        if ($this->isAdmin() || $this->isStaff()) {
            return route('admin.dashboard');
        } elseif ($this->isSeller()) {
            return route('seller.dashboard');
        } elseif ($this->isCustomer()) {
            return route('home');
        }
        throw new \RuntimeException('Unknown user type');
    }

    public function isAdmin(): bool
    {
        return $this->user_type == 'admin';
    }

    public function isStaff(): bool
    {
        return $this->user_type == 'staff';
    }

    public function isSeller(): bool
    {
        return $this->user_type == 'seller';
    }

    public function isCustomer(): bool
    {
        return $this->user_type == 'customer';
    }

    public function isDeliveryBoy(): bool
    {
        return $this->user_type == 'delivery_boy';
    }


    public function IsShopApproved(): bool
    {
        return $this->shop->registration_approval == 1;
    }
}
