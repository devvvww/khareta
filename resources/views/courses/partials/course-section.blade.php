@props(['color', 'label', 'empty', 'items'])

<x-section-header color="{{ $color }}">
    {{ $label }}
</x-section-header>

@if ($items->isEmpty())
    <p class="text-slate-400 text-sm text-center px-6 py-4">{{ $empty }}</p>
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