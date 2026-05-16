<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\BelongsToBranch;

#[Fillable(['name', 'email', 'password', 'two_factor_code', 'two_factor_expires_at', 'branch_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, BelongsToBranch;

    protected $guard_name = 'api';

    protected $appends = ['all_permissions'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function getAllPermissionsAttribute()
    {
        if (!$this->relationLoaded('roles')) {
            return $this->permissions->pluck('name');
        }
        return $this->getAllPermissions()->pluck('name');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_expires_at' => 'datetime',
        ];
    }
}
