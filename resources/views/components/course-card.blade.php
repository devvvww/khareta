@props([
    'color' => '#64748b',
    'title' => '',
    'code' => '',
    'prerequisiteCount' => null,
])

<div class="course-card relative">
    @if (!is_null($prerequisiteCount))
        <span class="absolute top-1.5 end-1.5 z-10 text-[9px] font-bold px-1.5 py-0.5 rounded-full
            {{ $prerequisiteCount <= 1 ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-600' }}">
            {{ $prerequisiteCount <= 1 ? '✓ مباشر' : '1 من ' . $prerequisiteCount }}
        </span>
    @endif
    <div class="card-header" style="background: {{ $color }};">
        <div class="flex flex-col items-center justify-center gap-1">
            <span>{{ $title }}</span>
            @if ($code)
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75" dir="ltr">{{ $code }}</span>
            @endif
        </div>
    </div>
</div>