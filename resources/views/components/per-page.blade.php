@props([
    'paginator',
    'options'   => [10, 25, 50, 100],
    'paramName' => 'per_page',
])

@php
    $current  = (int) request($paramName, $paginator->perPage());
    $total    = $paginator->total();
    $from     = $paginator->firstItem() ?? 0;
    $to       = $paginator->lastItem()  ?? 0;
    $lastPage = $paginator->lastPage();
    $page     = $paginator->currentPage();

    $prevUrl  = $paginator->previousPageUrl();
    $nextUrl  = $paginator->nextPageUrl();
    $firstUrl = $paginator->url(1);
    $lastUrl  = $paginator->url($lastPage);

    // Pertahankan semua query string kecuali 'page'
    $queryBag = collect(request()->except(['page', $paramName]));
    $buildUrl = function(string $base) use ($queryBag, $paramName, $current): string {
        $parsed = parse_url($base);
        parse_str($parsed['query'] ?? '', $q);
        $q = array_merge($queryBag->toArray(), [$paramName => $current], $q);
        return ($parsed['path'] ?? '') . '?' . http_build_query($q);
    };
@endphp

<div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-5 text-sm text-slate-500 flex-wrap">

    {{-- Rows per page --}}
    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
        @foreach(request()->except([$paramName, 'page']) as $key => $val)
            @if(is_array($val))
                @foreach($val as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach

        <span class="text-slate-400 whitespace-nowrap">Rows per page:</span>
        <div class="relative">
            <select name="{{ $paramName }}"
                    onchange="this.form.submit()"
                    class="appearance-none text-sm font-medium text-slate-700 border border-slate-200 rounded-lg pl-3 pr-7 py-1
                           bg-white focus:outline-none focus:ring-2 focus:ring-teal-300 cursor-pointer">
                @foreach($options as $opt)
                    <option value="{{ $opt }}" @selected($current === $opt)>{{ $opt }}</option>
                @endforeach
                <option value="99999" @selected($current >= 99999)>Semua</option>
            </select>
            {{-- chevron --}}
            <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"
                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </form>

    {{-- Range info --}}
    <span class="text-slate-500 whitespace-nowrap">
        {{ $from }}–{{ $to }} of {{ number_format($total) }}
    </span>

    {{-- Nav buttons --}}
    <div class="flex items-center gap-1">
        {{-- First --}}
        @if($page > 1)
            <a href="{{ $buildUrl($firstUrl) }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition"
               title="Halaman pertama">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
                </svg>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
                </svg>
            </span>
        @endif

        {{-- Prev --}}
        @if($prevUrl)
            <a href="{{ $buildUrl($prevUrl) }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition"
               title="Halaman sebelumnya">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </span>
        @endif

        {{-- Next --}}
        @if($nextUrl)
            <a href="{{ $buildUrl($nextUrl) }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition"
               title="Halaman berikutnya">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif

        {{-- Last --}}
        @if($page < $lastPage)
            <a href="{{ $buildUrl($lastUrl) }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition"
               title="Halaman terakhir">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M6 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-100 text-slate-300 cursor-not-allowed">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M6 5l7 7-7 7"/>
                </svg>
            </span>
        @endif
    </div>

</div>
