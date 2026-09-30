<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN_PPI   = 'admin_ppi';
    public const ROLE_AUDITOR     = 'auditor';
    public const ROLE_UNIT        = 'unit';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN_PPI   => 'Admin PPI',
        self::ROLE_AUDITOR     => 'Auditor',
        self::ROLE_UNIT        => 'Unit / Petugas',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'unit_id',
        'profession_id',
        'phone',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function audits()
    {
        return $this->hasMany(Audit::class, 'auditor_id');
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
