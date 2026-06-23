@foreach($rows as $label => $value)
    @if(!is_null($value) && $value !== '')
    <div class="flex justify-between gap-4 text-sm py-1.5 border-b border-slate-50 last:border-0">
        <span class="text-slate-400 shrink-0">{{ $label }}</span>
        <span class="text-slate-700 font-medium text-right">{{ $value }}</span>
    </div>
    @endif
@endforeach
