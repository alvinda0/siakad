@php
    // Render nilai dari log aktivitas — handle null, bool, array, string panjang
    if (is_null($value)) {
        $display = '<span class="text-slate-300 italic">null</span>';
    } elseif (is_bool($value)) {
        $display = $value
            ? '<span class="text-emerald-500 font-medium">Ya</span>'
            : '<span class="text-red-400 font-medium">Tidak</span>';
    } elseif (is_array($value)) {
        $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $display = '<pre class="text-xs bg-slate-50 rounded p-2 max-w-sm overflow-auto max-h-32 whitespace-pre-wrap">'
                   . e($json) . '</pre>';
    } else {
        $str = (string) $value;
        // Sembunyikan nilai yang terlihat seperti hash (password hash, token)
        if (strlen($str) > 60 && preg_match('/^[\$a-zA-Z0-9\/\.]+$/', $str)) {
            $display = '<span class="text-slate-400 italic">[terenkripsi]</span>';
        } else {
            $display = '<span class="break-all">' . e($str) . '</span>';
        }
    }
@endphp
{!! $display !!}
