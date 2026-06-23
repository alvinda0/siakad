<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Profil pendaftaran kandidat.
     */
    public function kandidatProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\KandidatProfile::class);
    }

    /**
     * Kelas yang diampu sebagai wali kelas.
     */
    public function waliKelas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Kelas::class, 'wali_kelas_id');
    }

    /**
     * Roles yang dimiliki user ini.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Cek apakah user memiliki role tertentu.
     *
     * @param  string|array  $role  Nama role atau array nama role
     */
    public function hasRole(string|array $role): bool
    {
        $roles = (array) $role;

        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    /**
     * Cek apakah user memiliki semua role yang disebutkan.
     *
     * @param  array  $roles
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles->whereIn('name', $roles)->count() === count($roles);
    }

    /**
     * Assign satu atau lebih role ke user.
     *
     * @param  string|array  $role
     */
    public function assignRole(string|array $role): void
    {
        $roles = Role::whereIn('name', (array) $role)->get();
        $this->roles()->syncWithoutDetaching($roles);
    }

    /**
     * Cabut role dari user.
     *
     * @param  string|array  $role
     */
    public function removeRole(string|array $role): void
    {
        $roles = Role::whereIn('name', (array) $role)->get();
        $this->roles()->detach($roles);
    }

    // ── Shortcut helpers ──────────────────────────────────────────

    public function isStudent(): bool    { return $this->hasRole(Role::STUDENT); }
    public function isTeacher(): bool    { return $this->hasRole(Role::TEACHER); }
    public function isCandidate(): bool  { return $this->hasRole(Role::CANDIDATE); }
    public function isAdmin(): bool      { return $this->hasRole(Role::ADMIN); }
    public function isSuperAdmin(): bool { return $this->hasRole(Role::SUPERADMIN); }
}
