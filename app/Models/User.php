<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmail, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Email verifikasi memakai template custom (emails.verify-email).
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function accessRequests()
    {
        return $this->hasMany(DocumentAccessRequest::class, 'requested_by');
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === Role::NAME_ADMIN || $this->isSuperAdmin();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === Role::NAME_SUPER_ADMIN;
    }

    public function isLegal(): bool
    {
        return $this->role?->name === Role::NAME_LEGAL;
    }

    public function canReview(): bool
    {
        return $this->isLegal() || $this->isAdmin();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
