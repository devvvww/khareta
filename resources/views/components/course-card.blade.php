@props([
    'color' => '#64748b',
    'title' => '',
    'code' => '',
    'prerequisiteCount' => null,
    'reserveBadgeSlot' => false,
])

<div class="course-card">
    <div class="card-header" style="background: {{ $color }};">
        <div class="flex flex-col items-center justify-center gap-1">
            <span>{{ $title }}</span>
            @if ($code)
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75" dir="ltr">{{ $code }}</span>
            @endif
            @if ($reserveBadgeSlot || !is_null($prerequisiteCount))
                <span class="h-4 mt-1 flex items-center justify-center">
                    @if (!is_null($prerequisiteCount) && $prerequisiteCount > 1)
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-white/15 text-white/85 leading-none">
                            + متطلبات أخرى
                        </span>
                    @endif
                </span>
            @endif
        </div>
    </div>
</div>