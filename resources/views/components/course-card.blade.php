@props([
    'color' => '#64748b',
    'title' => '',
    'code' => '',
    'prerequisiteCount' => null,
])

<div class="course-card relative">
    <div class="card-header" style="background: {{ $color }};">
        <div class="flex flex-col items-center justify-center gap-1">
            <span>{{ $title }}</span>
            @if ($code)
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75"
                    dir="ltr">{{ $code }}</span>
            @endif
        </div>
    </div>
    @if (!is_null($prerequisiteCount))
        <span
            class="text-[9px] font-bold px-2 py-0.5 rounded-full mt-1
        {{ $prerequisiteCount <= 1 ? 'bg-emerald-500 text-white' : 'bg-white/15 text-white/85' }}">
            {{ $prerequisiteCount <= 1 ? '✓ مباشر' : '+ متطلبات أخرى' }}
        </span>
    @endif
</div>
