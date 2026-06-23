<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'label',
        'description',
    ];

    // Role constants — gunakan ini di seluruh aplikasi
    const STUDENT    = 'student';
    const TEACHER    = 'teacher';
    const CANDIDATE  = 'candidate';
    const ADMIN      = 'admin';
    const SUPERADMIN = 'superadmin';

    /**
     * Users yang memiliki role ini.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
