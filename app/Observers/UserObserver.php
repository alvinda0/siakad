<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        ActivityLog::record(
            action: 'created',
            model:  $user,
            newData: $user->getAttributes(),
            label:  $user->name,
        );
    }

    public function updated(User $user): void
    {
        $dirty = $user->getDirty();

        // Tidak ada yang berubah (di luar field hidden) — skip
        $visible = array_diff_key($dirty, array_flip(ActivityLog::HIDDEN_FIELDS));
        if (empty($visible)) return;

        // Bangun old_data dari original
        $old = [];
        $new = [];
        foreach ($visible as $field => $newVal) {
            $old[$field] = $user->getOriginal($field);
            $new[$field] = $newVal;
        }

        ActivityLog::record(
            action:  'updated',
            model:   $user,
            oldData: $old,
            newData: $new,
            label:   $user->name,
        );
    }

    public function deleted(User $user): void
    {
        ActivityLog::record(
            action:  'deleted',
            model:   $user,
            oldData: $user->getAttributes(),
            label:   $user->name,
        );
    }
}
