@extends('layouts.app')

@section('title', $course['title'] . ' — مسار المواد الدراسية')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen p-0 md:p-6">

        <div class="course-flow-page flex flex-col min-h-screen md:min-h-0">

            {{-- Section 1: Unlocked courses (top) --}}
            <div class="pt-10 pb-4 bg-slate-50/50">
                <x-section-header color="slate-500">
                    مواد تتطلب هذه المادة :
                </x-section-header>

                @if ($unlocks->isEmpty())
                    <p class="text-slate-400 text-sm text-center px-6 py-4">لا توجد مواد تتطلب هذه المادة</p>
                @else
                    <div class="carousel-container">
                        @foreach ($unlocks as $item)
                            <a href="{{ route('courses.show', $item['id']) }}">
                                <x-course-card :color="$item['color']" :title="$item['title']" :code="$item['code'] ?? ''" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Section 2: Current course (middle) --}}
            <div class="flex-grow flex flex-col py-6 md:p-6 md:min-h-[420px]">
                <div class="flex-1 flex items-center justify-center">
                    <span class="text-slate-400 text-4xl font-bold leading-none">↑</span>
                </div>

                @if ($carousel->count() > 1)
                    <div id="course-carousel" class="course-carousel">
                        @foreach ($carousel as $item)
                            <a href="{{ route('courses.show', $item['id']) }}{{ $idsParam ? '?ids=' . $idsParam : '' }}"
                                class="course-carousel-slide block rounded-3xl text-white text-center px-6 py-10 md:py-12"
                                style="background: {{ $item['color'] }};">
                                <span
                                    class="slide-label block text-center text-[10px] uppercase tracking-widest opacity-80">المادة
                                    المختارة</span>
                                <h1 class="text-xl md:text-2xl font-extrabold mt-1">{{ $item['title'] }}</h1>
                                @if (!empty($item['code']))
                                    <span class="block text-center text-xs font-mono tracking-widest opacity-75 mt-2"
                                        dir="ltr">{{ $item['code'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 md:px-0 flex justify-center">
                        <div class="w-full max-w-md px-6 py-10 md:py-12 rounded-3xl text-white shadow-2xl text-center"
                            style="background: {{ $course['color'] ?? '#0b7af1' }};">
                            <span class="block text-center text-[10px] uppercase tracking-widest opacity-80">المادة
                                المختارة</span>
                            <h1 class="text-xl md:text-2xl font-extrabold mt-1">{{ $course['title'] }}</h1>
                            @if (!empty($course['code']))
                                <span class="block text-center text-xs font-mono tracking-widest opacity-75 mt-2"
                                    dir="ltr">{{ $course['code'] }}</span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="flex-1 flex items-center justify-center">
                    <span class="text-slate-400 text-4xl font-bold leading-none">↑</span>
                </div>
            </div>

            {{-- Section 3: Prerequisites (bottom) --}}
            <div class="pb-10 pt-4 bg-slate-50/50">
                <x-section-header color="slate-500">
                    مواد مطلوبة لهذه المادة :
                </x-section-header>

                @if ($prerequisites->isEmpty())
                    <p class="text-slate-400 text-sm text-center px-6 py-4">لا توجد مواد مطلوبة لهذه المادة</p>
                @else
                    <div class="carousel-container">
                        @foreach ($prerequisites as $item)
                            <a href="{{ route('courses.show', $item['id']) }}">
                                <x-course-card :color="$item['color']" :title="$item['title']" :code="$item['code'] ?? ''" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const carousel = document.getElementById('course-carousel');
        if (carousel) {
            const slides = [...carousel.querySelectorAll('.course-carousel-slide')];
            let scrollEndTimer;

            function updateCarouselState() {
                const center = carousel.scrollLeft + carousel.offsetWidth / 2;

                slides.forEach(slide => {
                    const slideCenter = slide.offsetLeft + slide.offsetWidth / 2;
                    const distance = Math.abs(center - slideCenter);
                    const ratio = Math.max(0, 1 - distance / carousel.offsetWidth);

                    slide.style.transform = `scale(${0.85 + ratio * 0.15})`;
                    slide.style.opacity = 0.4 + ratio * 0.6;

                    const label = slide.querySelector('.slide-label');
                    label.style.opacity = ratio > 0.9 ? '0.8' : '0';
                });
            }

            function navigateToClosestSlide() {
                const center = carousel.scrollLeft + carousel.offsetWidth / 2;
                let closest = null;
                let closestDistance = Infinity;

                slides.forEach(slide => {
                    const slideCenter = slide.offsetLeft + slide.offsetWidth / 2;
                    const distance = Math.abs(center - slideCenter);
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closest = slide;
                    }
                });

                if (closest && closest.href !== window.location.href) {
                    window.location.href = closest.href;
                }
            }

            carousel.addEventListener('scroll', () => {
                updateCarouselState();
                clearTimeout(scrollEndTimer);
                scrollEndTimer = setTimeout(navigateToClosestSlide, 180);
            });

            window.addEventListener('resize', updateCarouselState);
            updateCarouselState();
        }
    </script>
@endpush
