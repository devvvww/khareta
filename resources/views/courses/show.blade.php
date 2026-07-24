@extends('layouts.app')

@section('title', $course['title'] . ' — مسار المواد الدراسية')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen p-0 md:p-6">

        <div class="course-flow-page flex flex-col min-h-screen md:min-h-0">

            {{-- Section 1: Unlocked courses (top) --}}
            <div id="unlocks-section" class="pt-10 pb-4 bg-slate-50/50">
                @include('courses.partials.course-section', [
                    'color' => 'slate-500',
                    'label' => 'مواد تتطلب هذه المادة :',
                    'empty' => 'لا توجد مواد تتطلب هذه المادة',
                    'items' => $unlocks,
                ])
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
            <div id="prerequisites-section" class="pb-10 pt-4 bg-slate-50/50">
                @include('courses.partials.course-section', [
                    'color' => 'slate-500',
                    'label' => 'مواد مطلوبة لهذه المادة :',
                    'empty' => 'لا توجد مواد مطلوبة لهذه المادة',
                    'items' => $prerequisites,
                ])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const carousel = document.getElementById('course-carousel');
        const idsParam = @json($idsParam);

        if (carousel) {
            const slides = [...carousel.querySelectorAll('.course-carousel-slide')];
            const unlocksSection = document.getElementById('unlocks-section');
            const prerequisitesSection = document.getElementById('prerequisites-section');
            let scrollEndTimer;
            let currentIndex = 0;

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

            function slideSectionsIn(direction) {
                // direction: 1 = new content enters from the end side, -1 = from the start side
                const offset = direction * 24;

                [unlocksSection, prerequisitesSection].forEach(section => {
                    section.style.transition = 'none';
                    section.style.transform = `translateX(${offset}px)`;
                    section.style.opacity = '0';
                });

                // Force a reflow so the browser registers the starting state
                // before we animate to the resting state.
                void unlocksSection.offsetWidth;

                [unlocksSection, prerequisitesSection].forEach(section => {
                    section.style.transition = 'transform 0.25s ease, opacity 0.25s ease';
                    section.style.transform = 'translateX(0)';
                    section.style.opacity = '1';
                });
            }

            async function loadClosestSlide() {
                const center = carousel.scrollLeft + carousel.offsetWidth / 2;
                let closest = null;
                let closestDistance = Infinity;
                let closestIndex = 0;

                slides.forEach((slide, i) => {
                    const slideCenter = slide.offsetLeft + slide.offsetWidth / 2;
                    const distance = Math.abs(center - slideCenter);
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closest = slide;
                        closestIndex = i;
                    }
                });

                if (!closest) return;

                const url = closest.href;
                if (url === window.location.href) return;

                const direction = closestIndex > currentIndex ? 1 : -1;

                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    unlocksSection.innerHTML =
                        renderSection('مواد تتطلب هذه المادة :', 'لا توجد مواد تتطلب هذه المادة', data.unlocks);
                    prerequisitesSection.innerHTML =
                        renderSection('مواد مطلوبة لهذه المادة :', 'لا توجد مواد مطلوبة لهذه المادة', data
                            .prerequisites);

                    document.title = `${data.course.title} — مسار المواد الدراسية`;
                    history.pushState({}, '', url);

                    currentIndex = closestIndex;
                    slideSectionsIn(direction);
                } catch (e) {
                    window.location.href = url;
                }
            }

            function renderSection(label, emptyText, items) {
                const header = `
                <h2 class="text-sm font-bold px-6 mb-2 text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    ${label}
                </h2>`;

                if (items.length === 0) {
                    return header + `<p class="text-slate-400 text-sm text-center px-6 py-4">${emptyText}</p>`;
                }

                const cards = items.map(item => `
                <a href="/courses/${item.id}">
                    <div class="course-card">
                        <div class="card-header" style="background: ${item.color};">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <span>${escapeHtml(item.title)}</span>
                                ${item.code ? `<span class="text-[10px] font-mono font-normal tracking-widest opacity-75" dir="ltr">${escapeHtml(item.code)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </a>
            `).join('');

                return header + `<div class="carousel-container">${cards}</div>`;
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            carousel.addEventListener('scroll', () => {
                updateCarouselState();
                clearTimeout(scrollEndTimer);
                scrollEndTimer = setTimeout(loadClosestSlide, 180);
            });

            window.addEventListener('resize', updateCarouselState);
            updateCarouselState();
        }
    </script>
@endpush
