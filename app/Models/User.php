<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'login_code', 'password', 'role', 'phone', 'position', 'department', 'hire_date', 'address', 'gender', 'synced'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->login_code)) {
                $prefix = $user->role === 'admin' ? 'ADM' : 'EMP';

                // Trouve le dernier utilisateur avec ce préfixe pour calculer le numéro suivant
                $latestUser = self::where('login_code', 'like', $prefix . '-%')
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($latestUser && preg_match('/-(\d+)$/', $latestUser->login_code, $matches)) {
                    $nextNumber = ((int)$matches[1]) + 1;
                }

                $user->login_code = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
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
            'hire_date' => 'date',
        ];
    }
}
