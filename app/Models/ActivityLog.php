<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_label',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_data'   => 'array',
        'new_data'   => 'array',
        'created_at' => 'datetime',
    ];

    // Field yang tidak perlu dicatat (password, token, dsb.)
    public const HIDDEN_FIELDS = [
        'password', 'remember_token', 'email_verified_at',
        'current_step', 'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label warna per aksi untuk tampilan badge.
     */
    public function actionBadge(): array
    {
        return match ($this->action) {
            'created'  => ['label' => 'Buat',   'color' => 'bg-emerald-100 text-emerald-700'],
            'updated'  => ['label' => 'Ubah',   'color' => 'bg-blue-100 text-blue-700'],
            'deleted'  => ['label' => 'Hapus',  'color' => 'bg-red-100 text-red-700'],
            'login'    => ['label' => 'Login',  'color' => 'bg-violet-100 text-violet-700'],
            'logout'   => ['label' => 'Logout', 'color' => 'bg-slate-100 text-slate-600'],
            default    => ['label' => ucfirst($this->action), 'color' => 'bg-gray-100 text-gray-600'],
        };
    }

    /**
     * Nama singkat model (tanpa namespace).
     */
    public function modelName(): string
    {
        if (!$this->model_type) return '—';
        return class_basename($this->model_type);
    }

    /**
     * Catat aktivitas secara statis.
     */
    public static function record(
        string  $action,
        ?object $model  = null,
        ?array  $oldData = null,
        ?array  $newData = null,
        ?string $label   = null,
    ): void {
        $request = request();

        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model?->getKey(),
            'model_label' => $label ?? static::resolveLabel($model),
            'old_data'    => $oldData ? static::filterHidden($oldData) : null,
            'new_data'    => $newData ? static::filterHidden($newData) : null,
            'ip_address'  => $request->ip(),
            'user_agent'  => substr($request->userAgent() ?? '', 0, 255),
            'created_at'  => now(),
        ]);
    }

    private static function resolveLabel(?object $model): ?string
    {
        if (!$model) return null;
        return $model->name ?? $model->email ?? $model->title ?? (string) $model->getKey();
    }

    private static function filterHidden(array $data): array
    {
        return array_diff_key($data, array_flip(self::HIDDEN_FIELDS));
    }
}
