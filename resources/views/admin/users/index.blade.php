@extends('admin.layouts.app')

@section('title', 'Pengguna')
@section('breadcrumb', 'Pengguna')

@section('content')
<div class="space-y-5">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm font-medium">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm font-medium">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-extrabold text-slate-800">Manajemen Pengguna</h1>
        <p class="text-sm text-slate-500">Total: <strong>{{ $users->total() }}</strong> pengguna terdaftar</p>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        @if($users->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">👥</div>
                <h3 class="font-bold text-slate-600 mb-1">Belum ada pengguna</h3>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left">
                            <th class="px-5 py-3.5 font-semibold text-slate-500 w-10">#</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Nama</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Email</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Role</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Terdaftar</th>
                            <th class="px-5 py-3.5 font-semibold text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($users as $i => $user)
                        @php
                            $roleColors = [
                                'superadmin' => ['bg' => '#7C3AED', 'label' => 'SuperAdmin'],
                                'admin'      => ['bg' => '#1B7A8A', 'label' => 'Admin'],
                                'teacher'    => ['bg' => '#059669', 'label' => 'Guru'],
                                'student'    => ['bg' => '#E6920A', 'label' => 'Murid'],
                                'candidate'  => ['bg' => '#DC2626', 'label' => 'Kandidat'],
                            ];
                            $role     = $user->roles->first();
                            $roleName = $role?->name ?? 'none';
                            $roleConf = $roleColors[$roleName] ?? ['bg' => '#94A3B8', 'label' => ucfirst($roleName)];
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $users->firstItem() + $i }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                                         style="background: {{ $roleConf['bg'] }};">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-700">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $user->email }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-white"
                                      style="background: {{ $roleConf['bg'] }};">
                                    {{ $roleConf['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white transition hover:brightness-110"
                                       style="background: var(--teal);">
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold text-white bg-red-500 transition hover:bg-red-600">
                                            Hapus
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs px-3 py-1.5 rounded-lg font-semibold text-slate-400 bg-slate-100 cursor-not-allowed"
                                          title="Tidak dapat menghapus akun sendiri">
                                        Hapus
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-per-page :paginator="$users" />
        @endif
    </div>

</div>
@endsection
