@props([
    'title' => '',
    'code' => '',
    'color' => '#64748b',
])

<div class="course-card">
    <div class="card-header" style="background: {{ $color }};">
        <div class="flex flex-col items-center justify-center gap-1">
            <span>{{ $title }}</span>
            @if ($code)
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75"
                    dir="ltr">{{ $code }}</span>
            @endif
        </div>
    </div>
</div>