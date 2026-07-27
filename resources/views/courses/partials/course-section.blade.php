@props(['color', 'label', 'empty', 'items'])

<x-section-header color="{{ $color }}">
    {{ $label }}
</x-section-header>

<div class="section-body">
    @if ($items->isEmpty())
        <p class="section-body-empty text-slate-400 text-sm">{{ $empty }}</p>
    @else
        <div class="carousel-container">
            @foreach ($items as $item)
                <a href="{{ route('courses.show', $item['id']) }}">
                    <x-course-card
                        :color="$item['color']"
                        :title="$item['title']"
                        :code="$item['code'] ?? ''"
                    />
                </a>
            @endforeach
        </div>
    @endif
</div>