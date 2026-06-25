<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — SIAKAD SMK Muhammadiyah Sempor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --teal:        #1B7A8A;
            --teal-dark:   #145F6E;
            --teal-deeper: #0D4A57;
            --teal-light:  #E0F4F7;
            --navy:        #112D3E;
            --gold:        #E6920A;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--teal-deeper) 0%, var(--teal) 55%, #2BA8BF 100%);
            min-height: 100vh;
        }

        /* Floating orbs */
        .orb {
            position: fixed; border-radius: 50%; pointer-events: none;
            background: radial-gradient(circle, rgba(255,255,255,0.10), transparent 70%);
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0) scale(1); }
            50%      { transform: translateY(-20px) scale(1.04); }
        }

        /* Card */
        .login-card {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 32px 80px rgba(13,74,87,0.30), 0 0 0 1px rgba(255,255,255,0.2);
        }

        /* Input */
        .form-input {
            width: 100%; border: 1.5px solid #CBD5E1;
            border-radius: .625rem; padding: .65rem 1rem .65rem 2.75rem;
            font-size: .9rem; color: #1E293B;
            transition: border-color .2s, box-shadow .2s;
            outline: none; background: #F8FAFC;
        }
        .form-input:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(27,122,138,0.15);
            background: #fff;
        }
        .form-input.error { border-color: #EF4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.12); }

        /* Button */
        .btn-login {
            width: 100%; background: linear-gradient(135deg, var(--teal), var(--teal-dark));
            color: #fff; font-weight: 700; font-size: .95rem;
            padding: .75rem 1.5rem; border-radius: .625rem; border: none;
            cursor: pointer; transition: all .2s; letter-spacing: .01em;
        }
        .btn-login:hover  { filter: brightness(1.08); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(27,122,138,0.35); }
        .btn-login:active { transform: translateY(0); filter: brightness(.96); }
        .btn-login:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Alert error */
        .alert-error {
            background: #FEF2F2; border: 1px solid #FECACA;
            border-radius: .625rem; padding: .75rem 1rem;
            color: #B91C1C; font-size: .85rem;
            display: flex; align-items: flex-start; gap: .5rem;
        }

        /* Fade-up animation */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation: fadeUp .6s ease both; }
        .fade-up-2 { animation: fadeUp .6s .12s ease both; }

        /* Password toggle */
        .toggle-pwd { position:absolute; right:.75rem; top:50%; transform:translateY(-50%); cursor:pointer; color:#94A3B8; }
        .toggle-pwd:hover { color: var(--teal); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen px-4 py-12">

    <!-- Decorative orbs -->
    <div class="orb" style="width:480px;height:480px;right:-120px;top:-80px;animation-delay:0s;"></div>
    <div class="orb" style="width:300px;height:300px;left:-80px;bottom:-60px;animation-delay:3.5s;"></div>
    <div class="orb" style="width:180px;height:180px;right:120px;bottom:80px;animation-delay:6s;"></div>

    <div class="w-full max-w-md relative z-10">

        <!-- Logo area -->
        <div class="text-center mb-8 fade-up">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4 shadow-xl"
                 style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3);">
                <img src="{{ asset('image/smk.png') }}" alt="Logo SMK" class="w-14 h-14 object-contain drop-shadow">
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-tight leading-snug">
                SMK Muhammadiyah Sempor
            </h1>
            <p class="text-white/70 text-sm mt-1 italic">Sistem Informasi Akademik</p>
        </div>

        <!-- Card -->
        <div class="login-card rounded-2xl p-8 fade-up-2">

            <!-- Header card -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-lg">🔐</span>
                    <h2 class="text-xl font-extrabold" style="color:var(--navy);">Login SIAKAD</h2>
                </div>
                <p class="text-sm text-slate-500">Masuk sebagai Admin atau Guru</p>
            </div>

            <!-- Alert validasi Laravel -->
            @if ($errors->any())
            <div class="alert-error mb-5">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Alert session error (e.g. dari redirect) -->
            @if (session('error'))
            <div class="alert-error mb-5">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm" novalidate>
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold mb-1.5" style="color:var(--navy);">
                        Email
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4.5 h-4.5 w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@smkmuhsempor.sch.id"
                            autocomplete="email"
                            class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                            required
                        >
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold" style="color:var(--navy);">
                            Password
                        </label>
                        <a href="#" class="text-xs font-medium hover:underline" style="color:var(--teal);">
                            Lupa password?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                            required
                        >
                        <!-- Toggle show/hide password -->
                        <button type="button" class="toggle-pwd" id="togglePwd" aria-label="Tampilkan password">
                            <svg id="eyeIcon" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeOffIcon" class="w-[18px] h-[18px] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me -->
                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" id="remember" name="remember" value="1"
                           class="w-4 h-4 rounded accent-teal-600" style="accent-color:var(--teal);">
                    <label for="remember" class="text-sm text-slate-600 select-none cursor-pointer">
                        Ingat sesi login saya
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="submitBtn">
                    <span id="btnText" class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk ke Dashboard
                    </span>
                    <span id="btnLoading" class="hidden flex items-center justify-center gap-2">
                        <img src="{{ asset('image/smk.png') }}" alt="Loading" class="w-5 h-5 object-contain animate-spin">
                        Memproses…
                    </span>
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-slate-200"></div>
                <span class="text-xs text-slate-400 font-medium">Admin & Guru</span>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            <!-- Back to home -->
            <a href="{{ route('beranda') }}"
               class="flex items-center justify-center gap-2 w-full text-sm font-semibold py-2.5 rounded-xl border-2 transition hover:-translate-y-0.5"
               style="border-color:var(--teal); color:var(--teal);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Footer note -->
        <p class="text-center text-white/50 text-xs mt-6 fade-up">
            &copy; {{ date('Y') }} SMK Muhammadiyah Sempor &mdash; SIAKAD v1.0
        </p>
    </div>

    <script>
        // Toggle show/hide password
        const togglePwd  = document.getElementById('togglePwd');
        const pwdInput   = document.getElementById('password');
        const eyeIcon    = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        togglePwd.addEventListener('click', () => {
            const isHidden = pwdInput.type === 'password';
            pwdInput.type  = isHidden ? 'text' : 'password';
            eyeIcon.classList.toggle('hidden', isHidden);
            eyeOffIcon.classList.toggle('hidden', !isHidden);
        });

        // Loading state on submit
        const form      = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText   = document.getElementById('btnText');
        const btnLoad   = document.getElementById('btnLoading');

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnLoad.classList.remove('hidden');
        });
    </script>
</body>
</html>
