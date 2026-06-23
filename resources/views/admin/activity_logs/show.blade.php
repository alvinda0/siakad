@extends('admin.layouts.app')
@section('title', 'Detail Log Aktivitas')
@section('breadcrumb', 'Detail Log')

@section('content')
@php $badge = $activityLog->actionBadge(); @endphp

<div class="space-y-5 max-w-5xl">

    {{-- Back --}}
    <a href="{{ route('admin.activity-logs.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold hover:underline"
       style="color:var(--teal);">
        ← Kembali ke Log Aktivitas
    </a>

    {{-- Header card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex flex-wrap items-start gap-4 justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $badge['color'] }}">
                        {{ $badge['label'] }}
                    </span>
                    <span class="text-slate-400 text-sm">
                        {{ $activityLog->modelName() }}
                        @if($activityLog->model_id)
                            <span class="font-mono">#{{ $activityLog->model_id }}</span>
                        @endif
                    </span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800">
                    {{ $activityLog->model_label ?? 'Log #'.$activityLog->id }}
                </h2>
            </div>
            <div class="text-right text-sm text-slate-400 space-y-1">
                <p class="font-semibold text-slate-600">{{ $activityLog->created_at->format('d M Y, H:i:s') }}</p>
                <p class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $activityLog->ip_address ?? '—' }}</p>
            </div>
        </div>

        {{-- Pengguna --}}
        @if($activityLog->user)
        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                 style="background:var(--teal);">
                {{ strtoupper(substr($activityLog->user->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-slate-700 text-sm">{{ $activityLog->user->name }}</p>
                <p class="text-xs text-slate-400">{{ $activityLog->user->email }}</p>
            </div>
        </div>
        @endif

        {{-- User Agent --}}
        @if($activityLog->user_agent)
        <div class="mt-4 pt-4 border-t border-slate-100">
            <p class="text-xs text-slate-400 font-semibold uppercase mb-1">Browser / Perangkat</p>
            <p class="text-xs text-slate-500 font-mono break-all">{{ $activityLog->user_agent }}</p>
        </div>
        @endif
    </div>

    {{-- Login / Logout info --}}
    @if(in_array($activityLog->action, ['login', 'logout']) && !$activityLog->old_data && !$activityLog->new_data)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-3 text-slate-500">
            <span class="text-2xl">{{ $activityLog->action === 'login' ? '🔐' : '🔓' }}</span>
            <div>
                <p class="font-semibold text-slate-700">
                    {{ $activityLog->action === 'login' ? 'Pengguna berhasil masuk ke sistem' : 'Pengguna keluar dari sistem' }}
                </p>
                <p class="text-xs text-slate-400 mt-0.5">
                    Tidak ada data yang berubah pada aksi ini.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Data CREATE --}}
    @if($activityLog->action === 'created' && $activityLog->new_data)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-emerald-50 flex items-center gap-2">
            <span class="text-emerald-600">✅</span>
            <h3 class="font-bold text-emerald-700 text-sm">Data yang Dibuat</h3>
            <span class="ml-auto text-xs text-emerald-500 font-mono">{{ count($activityLog->new_data) }} field</span>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-50">
                @foreach($activityLog->new_data as $field => $value)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 w-48 font-semibold text-slate-500 font-mono text-xs">{{ $field }}</td>
                    <td class="px-5 py-3">
                        <x-activity-value :value="$value" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Data UPDATE --}}
    @if($activityLog->action === 'updated' && $activityLog->new_data)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-blue-50 flex items-center gap-2">
            <span class="text-blue-600">✏️</span>
            <h3 class="font-bold text-blue-700 text-sm">Field yang Diubah</h3>
            <span class="ml-auto text-xs text-blue-500 font-mono">{{ count($activityLog->new_data) }} field</span>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left bg-slate-50">
                    <th class="px-5 py-2 text-xs font-bold text-slate-400 uppercase w-48">Field</th>
                    <th class="px-5 py-2 text-xs font-bold text-slate-400 uppercase">Nilai Lama</th>
                    <th class="px-5 py-2 text-xs font-bold text-slate-400 uppercase">Nilai Baru</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($activityLog->new_data as $field => $newVal)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-semibold text-slate-500 font-mono text-xs">{{ $field }}</td>
                    <td class="px-5 py-3">
                        <span class="line-through text-red-400">
                            <x-activity-value :value="$activityLog->old_data[$field] ?? null" />
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-emerald-600 font-medium">
                            <x-activity-value :value="$newVal" />
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Data DELETE --}}
    @if($activityLog->action === 'deleted' && $activityLog->old_data)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-red-50 flex items-center gap-2">
            <span class="text-red-500">🗑️</span>
            <h3 class="font-bold text-red-600 text-sm">Data yang Dihapus</h3>
            <span class="ml-auto text-xs text-red-400 font-mono">{{ count($activityLog->old_data) }} field</span>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-50">
                @foreach($activityLog->old_data as $field => $value)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 w-48 font-semibold text-slate-500 font-mono text-xs">{{ $field }}</td>
                    <td class="px-5 py-3 text-red-400">
                        <x-activity-value :value="$value" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Fallback: no data --}}
    @if(!$activityLog->old_data && !$activityLog->new_data && !in_array($activityLog->action, ['login','logout']))
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 text-center text-slate-400 text-sm">
        Tidak ada data perubahan yang tercatat untuk log ini.
    </div>
    @endif

    {{-- Raw JSON toggle (untuk debugging / audit) --}}
    @if($activityLog->old_data || $activityLog->new_data)
    <details class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden group">
        <summary class="px-5 py-3 cursor-pointer text-sm font-semibold text-slate-500 hover:bg-slate-50 flex items-center gap-2 select-none list-none">
            <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            Lihat Raw JSON
        </summary>
        <div class="border-t border-slate-100 grid {{ $activityLog->old_data && $activityLog->new_data ? 'md:grid-cols-2' : 'grid-cols-1' }} divide-y md:divide-y-0 md:divide-x divide-slate-100">
            @if($activityLog->old_data)
            <div class="p-4">
                <p class="text-xs font-bold text-slate-400 uppercase mb-2">old_data</p>
                <pre class="text-xs text-slate-600 bg-slate-50 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap break-all">{{ json_encode($activityLog->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
            @if($activityLog->new_data)
            <div class="p-4">
                <p class="text-xs font-bold text-slate-400 uppercase mb-2">new_data</p>
                <pre class="text-xs text-slate-600 bg-slate-50 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap break-all">{{ json_encode($activityLog->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
    </details>
    @endif

</div>
@endsection
