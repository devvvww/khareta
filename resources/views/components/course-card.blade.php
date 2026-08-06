@props([
    'color' => '#64748b',
    'title' => '',
    'code' => '',
    'prerequisiteCount' => null,
    'reserveBadgeSlot' => false,
])

<div class="course-card">
    <div class="card-header" style="background: {{ $color }};">
        <div class="course-info">
            <span>{{ $title }}</span>

            @if ($code)
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75" dir="ltr">
                    {{ $code }}
                </span>
            @endif
        </div>

        @if ($reserveBadgeSlot || !is_null($prerequisiteCount))
            <div class="course-badge">
                @if (!is_null($prerequisiteCount) && $prerequisiteCount > 1)
                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-white/15 text-white/85 leading-none">
                        + متطلبات أخرى
                    </span>
                @endif
            </div>
        @endif
    </div>

</div>
