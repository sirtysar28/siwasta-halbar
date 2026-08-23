<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\LogOptions;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, CausesActivity, HasApiTokens;

    protected $fillable = [
        'name', 'nip', 'email', 'phone', 'avatar', 'opd_id',
        'is_active', 'last_login_at', 'last_login_ip', 'device_info',
        'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationCustom::class);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->hasMany(NotificationCustom::class)->where('dibaca', false)->count();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0d6efd&color=fff';
    }

    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? '-';
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        return $initials;
    }
}
