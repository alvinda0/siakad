@extends('admin.layouts.app')
@section('title', 'Log Aktivitas')
@section('breadcrumb', 'Log Aktivitas')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<style>
    /* ── Sesuaikan Choices.js dengan desain sistem ── */
    .choices__inner {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 5px 10px;
        min-height: 38px;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    .choices__input {
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    .choices[data-type*='select-one'] .choices__inner {
        padding-bottom: 5px;
    }
    .choices__list--dropdown,
    .choices__list[aria-expanded] {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.10);
        z-index: 50;
    }
    .choices__list--dropdown .choices__item--selectable.is-highlighted,
    .choices__list[aria-expanded] .choices__item--selectable.is-highlighted {
        background: #E0F4F7;
        color: #145F6E;
    }
    .choices__list--dropdown .choices__item,
    .choices__list[aria-expanded] .choices__item {
        font-size: 0.875rem;
        padding: 8px 12px;
    }
    .choices__placeholder {
        opacity: 0.5;
    }
    .is-focused .choices__inner,
    .is-open .choices__inner {
        border-color: #1B7A8A;
        box-shadow: 0 0 0 3px rgba(27,122,138,0.15);
        outline: none;
    }
    .choices[data-type*='select-one']::after {
        border-color: #94a3b8 transparent transparent transparent;
        right: 14px;
    }
    .choices[data-type*='select-one'].is-open::after {
        border-color: transparent transparent #1B7A8A transparent;
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-slate-800">Log Aktivitas</h1>
            <p class="text-sm text-slate-400 mt-0.5">Riwayat semua perubahan data (create, update, delete) dan sesi login/logout.</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            {{ number_format($logs->total()) }} entri tercatat
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.activity-logs.index') }}"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            {{-- Aksi --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Aksi</label>
                <select id="filter-action" name="action">
                    <option value="">Semua Aksi</option>
                    @foreach(['created'=>'Buat','updated'=>'Ubah','deleted'=>'Hapus','login'=>'Login','logout'=>'Logout'] as $val => $lbl)
                        <option value="{{ $val }}" @selected(request('action') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Model --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Model</label>
                <select id="filter-model" name="model">
                    <option value="">Semua Model</option>
                    @foreach(['User'=>'User','KandidatProfile'=>'Kandidat'] as $val => $lbl)
                        <option value="{{ $val }}" @selected(request('model') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            {{-- User --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Pengguna</label>
                <select id="filter-user" name="user_id">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-400"
                       style="height:38px;">
            </div>
        </div>

        <div class="flex gap-2 mt-3">
            <button type="submit"
                    class="text-sm font-semibold px-4 py-2 rounded-lg text-white"
                    style="background:var(--teal);">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['action','model','user_id','date']))
                <a href="{{ route('admin.activity-logs.index') }}"
                   class="text-sm font-semibold px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Model</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Target</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Perubahan</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">IP</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    @php $badge = $log->actionBadge(); @endphp
                    <tr class="hover:bg-slate-50 transition">
                        {{-- Waktu --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="font-medium text-slate-700">{{ $log->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $log->created_at->format('H:i:s') }}</p>
                        </td>

                        {{-- Pengguna --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                                         style="background:var(--teal);">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700 leading-tight">{{ $log->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $log->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>

                        {{-- Aksi badge --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $badge['color'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>

                        {{-- Model --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-slate-600 font-medium">{{ $log->modelName() }}</span>
                            @if($log->model_id)
                                <span class="text-slate-300 text-xs"> #{{ $log->model_id }}</span>
                            @endif
                        </td>

                        {{-- Target label --}}
                        <td class="px-4 py-3 max-w-[160px] truncate text-slate-600">
                            {{ $log->model_label ?? '—' }}
                        </td>

                        {{-- Ringkasan perubahan --}}
                        <td class="px-4 py-3 max-w-[220px]">
                            @if($log->action === 'created' && $log->new_data)
                                <span class="text-xs text-slate-500">
                                    {{ count($log->new_data) }} field dicatat
                                </span>
                            @elseif($log->action === 'updated' && $log->new_data)
                                @php $fields = array_keys($log->new_data); @endphp
                                <span class="text-xs text-slate-500">
                                    {{ implode(', ', array_slice($fields, 0, 3)) }}{{ count($fields) > 3 ? ' +'.( count($fields)-3).' lainnya' : '' }}
                                </span>
                            @elseif($log->action === 'deleted' && $log->old_data)
                                <span class="text-xs text-red-400">Data dihapus</span>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- IP --}}
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-400 font-mono">
                            {{ $log->ip_address ?? '—' }}
                        </td>

                        {{-- Detail --}}
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            @if(in_array($log->action, ['created','updated','deleted']))
                                <a href="{{ route('admin.activity-logs.show', $log) }}"
                                   class="text-xs font-semibold hover:underline"
                                   style="color:var(--teal);">
                                    Detail →
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="text-4xl mb-3">📋</div>
                            <p class="text-slate-500 font-medium">Belum ada log aktivitas.</p>
                            <p class="text-sm text-slate-400 mt-1">Aktivitas akan tercatat otomatis saat data diubah.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <x-per-page :paginator="$logs" />
    </div>

</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
(function () {
    const commonOpts = {
        searchEnabled: true,
        searchPlaceholderValue: 'Cari...',
        itemSelectText: '',
        noResultsText: 'Tidak ditemukan',
        noChoicesText: 'Tidak ada pilihan',
        shouldSort: false,
        allowHTML: false,
    };

    // Aksi — sedikit opsi, search tetap aktif tapi bisa dinonaktifkan
    new Choices(document.getElementById('filter-action'), {
        ...commonOpts,
        searchEnabled: false,   // cukup 5 opsi, tidak perlu search
    });

    // Model
    new Choices(document.getElementById('filter-model'), {
        ...commonOpts,
        searchEnabled: false,
    });

    // Pengguna — banyak pilihan, search penting di sini
    new Choices(document.getElementById('filter-user'), {
        ...commonOpts,
        searchEnabled: true,
        searchFields: ['label'],
        searchPlaceholderValue: 'Cari nama pengguna...',
    });
})();
</script>

@endsection
