@extends('admin.layouts.app')

@section('title', 'Edit Pengguna')
@section('breadcrumb', 'Edit Pengguna')

@section('content')
<div class="max-w-xl space-y-5">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-extrabold text-slate-800">Edit Pengguna</h1>
        <p class="text-sm text-slate-500">Ubah data akun pengguna.</p>
    </div>

    {{-- Alert error --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm space-y-1">
        @foreach($errors->all() as $err)
            <p>• {{ $err }}</p>
        @endforeach
    </div>
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       required
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                       placeholder="Nama lengkap">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       required
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                       placeholder="email@contoh.com">
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Role</label>
                <select name="role"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition bg-white">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                            {{ $role->label ?? ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                    Password Baru
                    <span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                       placeholder="••••••••" autocomplete="new-password">
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                       placeholder="••••••••" autocomplete="new-password">
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:brightness-110"
                        style="background: var(--teal);">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
