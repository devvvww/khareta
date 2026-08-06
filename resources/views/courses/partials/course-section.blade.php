@props(['color', 'label', 'empty', 'items', 'selectedParam' => null])

<x-section-header color="{{ $color }}">
    {{ $label }}
</x-section-header>

<div class="section-body">
    @if ($items->isEmpty())
        <p class="section-body-empty text-slate-400 text-sm">{{ $empty }}</p>
    @else
        <div class="carousel-container">
            @foreach ($items as $item)
                <a
                    href="{{ route('courses.show', $item['id']) }}{{ $selectedParam ? '?selected=' . $selectedParam : '' }}">
                    <x-course-card :color="$item['color']" :title="$item['title']" :code="$item['code'] ?? ''" :prerequisiteCount="$item['prerequisite_count'] ?? null" />
                </a>
            @endforeach
        </div>
    @endif
</div>
