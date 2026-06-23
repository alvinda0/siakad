<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\KandidatProfile;

class KandidatProfileObserver
{
    public function created(KandidatProfile $profile): void
    {
        ActivityLog::record(
            action:  'created',
            model:   $profile,
            newData: $profile->getAttributes(),
            label:   $profile->nama_lengkap ?? "Profil #{$profile->id}",
        );
    }

    public function updated(KandidatProfile $profile): void
    {
        $dirty = $profile->getDirty();

        $visible = array_diff_key($dirty, array_flip(ActivityLog::HIDDEN_FIELDS));
        if (empty($visible)) return;

        $old = [];
        $new = [];
        foreach ($visible as $field => $newVal) {
            $old[$field] = $profile->getOriginal($field);
            $new[$field] = $newVal;
        }

        ActivityLog::record(
            action:  'updated',
            model:   $profile,
            oldData: $old,
            newData: $new,
            label:   $profile->nama_lengkap ?? "Profil #{$profile->id}",
        );
    }

    public function deleted(KandidatProfile $profile): void
    {
        ActivityLog::record(
            action:  'deleted',
            model:   $profile,
            oldData: $profile->getAttributes(),
            label:   $profile->nama_lengkap ?? "Profil #{$profile->id}",
        );
    }
}
