{{--
    Global Confirm Modal
    Dipanggil via JS: openConfirmModal({ type, name, formId })
    type  : 'terima' | 'tolak'
    name  : nama kandidat
    formId: id form yang akan di-submit
--}}

<div id="confirm-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="modal-title">

    {{-- Backdrop --}}
    <div id="modal-backdrop"
         class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-200 opacity-0"
         onclick="closeConfirmModal()"></div>

    {{-- Panel --}}
    <div id="modal-panel"
         class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md
                transition-all duration-200 scale-95 opacity-0 overflow-hidden">

        {{-- Top accent bar --}}
        <div id="modal-accent" class="h-1.5 w-full"></div>

        <div class="px-6 pt-6 pb-7">

            {{-- Icon --}}
            <div class="flex justify-center mb-4">
                <div id="modal-icon-wrap"
                     class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                    <span id="modal-icon"></span>
                </div>
            </div>

            {{-- Title & desc --}}
            <div class="text-center mb-6">
                <h2 id="modal-title" class="text-xl font-extrabold text-slate-800 mb-1"></h2>
                <p id="modal-desc" class="text-sm text-slate-500 leading-relaxed"></p>
            </div>

            {{-- Candidate name chip --}}
            <div class="flex justify-center mb-6">
                <span id="modal-name-chip"
                      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                             bg-slate-100 text-slate-700 border border-slate-200">
                    👤 <span id="modal-name"></span>
                </span>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button type="button"
                        onclick="closeConfirmModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold
                               text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button id="modal-confirm-btn"
                        type="button"
                        onclick="submitConfirmModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition
                               hover:brightness-110 active:scale-95">
                    <span id="modal-confirm-label"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /* ─── State ─── */
    let _confirmFormId = null;

    const CONFIG = {
        terima: {
            accent:      'bg-emerald-500',
            iconBg:      'bg-emerald-50',
            iconColor:   'text-emerald-600',
            icon:        '✓',
            title:       'Terima Kandidat?',
            desc:        'Kandidat ini akan diterima sebagai peserta didik baru. Tindakan ini dapat diubah kembali.',
            btnBg:       'bg-emerald-500',
            btnLabel:    'Ya, Terima',
        },
        tolak: {
            accent:      'bg-red-500',
            iconBg:      'bg-red-50',
            iconColor:   'text-red-500',
            icon:        '✕',
            title:       'Tolak Kandidat?',
            desc:        'Kandidat ini akan ditolak dari proses penerimaan. Tindakan ini dapat diubah kembali.',
            btnBg:       'bg-red-500',
            btnLabel:    'Ya, Tolak',
        },
        hapus: {
            accent:      'bg-red-500',
            iconBg:      'bg-red-50',
            iconColor:   'text-red-500',
            icon:        '🗑️',
            title:       'Hapus Data?',
            desc:        'Data ini akan dihapus secara permanen dan tidak dapat dikembalikan.',
            btnBg:       'bg-red-500',
            btnLabel:    'Ya, Hapus',
        },
    };

    /* ─── Open ─── */
    function openConfirmModal({ type, name, formId }) {
        const cfg   = CONFIG[type];
        if (!cfg) return;

        _confirmFormId = formId;

        // Apply config
        document.getElementById('modal-accent').className      = `h-1.5 w-full ${cfg.accent}`;
        document.getElementById('modal-icon-wrap').className   =
            `w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shadow-inner ${cfg.iconBg}`;
        document.getElementById('modal-icon').textContent      = cfg.icon;
        document.getElementById('modal-icon').className        = `font-black text-2xl ${cfg.iconColor}`;
        document.getElementById('modal-title').textContent     = cfg.title;
        document.getElementById('modal-desc').textContent      = cfg.desc;
        document.getElementById('modal-name').textContent      = name;
        document.getElementById('modal-confirm-btn').className =
            `flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition hover:brightness-110 active:scale-95 ${cfg.btnBg}`;
        document.getElementById('modal-confirm-label').textContent = cfg.btnLabel;

        // Show
        const modal   = document.getElementById('confirm-modal');
        const backdrop = document.getElementById('modal-backdrop');
        const panel    = document.getElementById('modal-panel');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
    }

    /* ─── Close ─── */
    function closeConfirmModal() {
        const modal    = document.getElementById('confirm-modal');
        const backdrop = document.getElementById('modal-backdrop');
        const panel    = document.getElementById('modal-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            _confirmFormId = null;
        }, 200);
    }

    /* ─── Submit ─── */
    function submitConfirmModal() {
        if (!_confirmFormId) return;
        const form = document.getElementById(_confirmFormId);
        if (form) form.submit();
        closeConfirmModal();
    }

    /* ─── Close on Escape ─── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeConfirmModal();
    });

    /* ─── Helper: hapus via URL (buat form DELETE on-the-fly) ─── */
    let _deleteUrl = null;

    function confirmDeleteModal(url, name) {
        _deleteUrl = url;

        // Inject dynamic form id
        _confirmFormId = '__delete_form__';
        let existing = document.getElementById('__delete_form__');
        if (existing) existing.remove();

        const form = document.createElement('form');
        form.id     = '__delete_form__';
        form.method = 'POST';
        form.action = url;
        form.style.display = 'none';
        form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                          <input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(form);

        openConfirmModal({ type: 'hapus', name: name, formId: '__delete_form__' });
    }
</script>
