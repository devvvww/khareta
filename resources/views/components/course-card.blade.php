@props([
    'color' => '#64748b',
    'title' => '',
    'code' => '',
    'prerequisiteCount' => null,
    'reserveBadgeSlot' => false,
])

<div class="course-card">
    <div class="card-header flex flex-col items-center justify-center p-3 min-h-[90px]" style="background: {{ $color }};">
        <div class="flex flex-col items-center justify-center text-center">
            <span>{{ $title }}</span>
            @if ($code)
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75" dir="ltr">{{ $code }}</span>
            @endif
        </div>
        @if (!is_null($prerequisiteCount) && $prerequisiteCount > 1)
            <div class="flex items-center justify-center mt-1">
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-white/15 text-white/85 leading-none">
                    + متطلبات أخرى
                </span>
            </div>
        @endif
    </div>
</div>