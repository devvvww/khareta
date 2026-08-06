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
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75" dir="ltr">{{ $code }}</span>
            @endif
        </div>
    </div>
    @if (!is_null($prerequisiteCount))
        <span class="absolute bottom-1.5 inset-x-0 flex justify-center text-[9px] font-bold px-1.5 py-0.5
            {{ $prerequisiteCount <= 1 ? 'text-emerald-100' : 'text-white/80' }}">
            {{ $prerequisiteCount <= 1 ? '✓ مباشر' : '+ متطلبات أخرى' }}
        </span>
    @endif
</div>