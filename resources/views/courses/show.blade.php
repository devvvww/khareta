@extends('layouts.app')

@section('title', $course['title'] . ' — مسار المواد الدراسية')

@section('tg-fallback', route('courses.search'))

@section('content')

    <a href="{{ route('courses.search') }}{{ $selectedParam ? '?ids=' . $selectedParam : '' }}"
        class="fixed top-4 end-4 z-40 w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-lg text-slate-500 hover:text-[#0b7af1]"
        title="بحث">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.6 0 7.5 7.5 0 0010.6 0z" />
        </svg>
    </a>
    {{-- <div class="flex flex-col items-center px-0 md:px-6 pb-0 md:pb-6 pt-0 md:pt-[clamp(0rem,calc((100vw-48rem)*0.094),1.5rem)]">
         --}}
    <div class="flex flex-col items-center px-0 md:px-6 pb-0 md:pb-6 pt-0">
        <div class="course-flow-page flex flex-col h-dvh overflow-y-auto md:h-auto">

            {{-- Section 1: Unlocked courses (top) --}}
            <div id="unlocks-section" class="pt-6 pb-3 bg-slate-50/50 shrink-0">
                @include('courses.partials.course-section', [
                    'color' => 'slate-500',
                    'label' => 'مواد تتطلب هذه المادة :',
                    'empty' => 'لا توجد مواد تتطلب هذه المادة',
                    'items' => $unlocks,
                    'selectedParam' => $selectedParam,
                ])
            </div>

            {{-- Section 2: Current course (middle) --}}
            <div class="grow shrink-0 flex flex-col justify-evenly py-2">
                <div class="flex justify-center">
                    <span class="text-slate-400 text-2xl font-bold leading-none">↑</span>
                </div>

                @if ($carousel->count() > 1)
                    <div id="course-carousel" class="course-carousel">
                        @foreach ($carousel as $item)
                            <div class="course-carousel-slide" data-id="{{ $item['id'] }}"
                                data-url="{{ route('courses.show', $item['id']) }}{{ $idsParam ? '?ids=' . $idsParam : '' }}">
                                <div class="course-carousel-slide-inner current-course-card rounded-3xl text-white text-center px-6 py-6 md:py-6"
                                    style="background: {{ $item['color'] }};">
                                    <span
                                        class="slide-label block text-center text-[12px] uppercase tracking-widest opacity-80">المادة
                                        المختارة</span>
                                    <h1 class="text-xl md:text-2xl font-extrabold mt-1">{{ $item['title'] }}</h1>
                                    @if (!empty($item['code']))
                                        <span class="block text-center text-xs font-mono tracking-widest opacity-75 mt-2"
                                            dir="ltr">{{ $item['code'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-[10%] flex justify-center current-course">
                        <div class="current-course-card w-full rounded-3xl text-white text-center px-6 py-6 md:py-6"
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

                <div class="flex justify-center">
                    <span class="text-slate-400 text-2xl font-bold leading-none">↑</span>
                </div>
            </div>

            {{-- Section 3: Prerequisites (bottom) --}}
            <div id="prerequisites-section" class="pb-6 pt-3 bg-slate-50/50 shrink-0">
                @include('courses.partials.course-section', [
                    'color' => 'slate-500',
                    'label' => 'مواد مطلوبة لهذه المادة :',
                    'empty' => 'لا توجد مواد مطلوبة لهذه المادة',
                    'items' => $prerequisites,
                    'selectedParam' => $selectedParam,
                ])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const selectedParam = @json($selectedParam);
        const prefetchedData = @json($prefetchedData ?? []);
        const currentCourseId = {{ $currentCourseId }};

        const carousel = document.getElementById('course-carousel');
        const idsParam = @json($idsParam);

        if (carousel) {
            const slides = [...carousel.querySelectorAll('.course-carousel-slide')];
            const unlocksSection = document.getElementById('unlocks-section');
            const prerequisitesSection = document.getElementById('prerequisites-section');
            let scrollEndTimer;
            let currentIndex = 0;
            let ticking = false;


            // Jump straight to the slide matching the course actually being viewed
            const currentSlide = slides.find(s => s.dataset.id == currentCourseId);
            if (currentSlide) {
                carousel.scrollLeft = currentSlide.offsetLeft -
                    (carousel.offsetWidth / 2) +
                    (currentSlide.offsetWidth / 2);

                currentIndex = slides.indexOf(currentSlide);
            }

            function equalizeSlideHeights() {
                const inners = slides.map(s => s.querySelector('.course-carousel-slide-inner'));

                // Reset any previously forced height first, so we measure each
                // card's true natural height, not a height from a prior pass.
                inners.forEach(inner => {
                    inner.style.height = 'auto';
                });

                const maxHeight = Math.max(...inners.map(inner => inner.offsetHeight));

                inners.forEach(inner => {
                    inner.style.height = `${maxHeight}px`;
                });
            }


            equalizeSlideHeights();

            function updateCarouselState() {
                const center = carousel.scrollLeft + carousel.offsetWidth / 2;

                slides.forEach(slide => {
                    const inner = slide.querySelector('.course-carousel-slide-inner');
                    const slideCenter = slide.offsetLeft + slide.offsetWidth / 2;
                    const distance = Math.abs(center - slideCenter);
                    const ratio = Math.max(0, 1 - distance / carousel.offsetWidth);

                    inner.style.transform = `scaleX(${0.85 + ratio * 0.15})`;
                    inner.style.opacity = 0.4 + ratio * 0.6;

                    const label = inner.querySelector('.slide-label');
                    label.style.opacity = ratio > 0.9 ? '0.8' : '0';
                });
            }

            updateCarouselState();

            function slideSectionsIn(direction) {
                const offset = direction * 24;
                const bodies = [
                    unlocksSection.querySelector('.section-body'),
                    prerequisitesSection.querySelector('.section-body'),
                ].filter(Boolean);

                bodies.forEach(body => {
                    body.style.transition = 'none';
                    body.style.transform = `translateX(${offset}px)`;
                    body.style.opacity = '0';
                });

                void bodies[0]?.offsetWidth;

                bodies.forEach(body => {
                    body.style.transition = 'transform 0.25s ease, opacity 0.25s ease';
                    body.style.transform = 'translateX(0)';
                    body.style.opacity = '1';
                });
            }

            function loadClosestSlide() {
                const center = carousel.scrollLeft + carousel.offsetWidth / 2;
                let closest = null;
                let closestDistance = Infinity;
                let closestIndex = 0;
                let closestId = null;

                slides.forEach((slide, i) => {
                    const slideCenter = slide.offsetLeft + slide.offsetWidth / 2;
                    const distance = Math.abs(center - slideCenter);
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closest = slide;
                        closestIndex = i;
                        closestId = slide.dataset.id;
                    }
                });

                if (!closest) return;

                const url = closest.dataset.url;
                if (!url || url === window.location.href) return;

                const data = prefetchedData[closestId];
                if (!data) {
                    // Fallback safety net — shouldn't normally happen, but if the
                    // prefetch is ever missing this course's data, fall back to a
                    // real navigation rather than showing nothing.
                    window.location.href = url;
                    return;
                }

                const direction = closestIndex > currentIndex ? 1 : -1;

                unlocksSection.innerHTML =
                    renderSection('مواد تتطلب هذه المادة :', 'لا توجد مواد تتطلب هذه المادة', data.unlocks);
                prerequisitesSection.innerHTML =
                    renderSection('مواد مطلوبة لهذه المادة :', 'لا توجد مواد مطلوبة لهذه المادة', data.prerequisites);

                document.title = `${data.title} — مسار المواد الدراسية`;
                history.replaceState({}, '', url);

                currentIndex = closestIndex;
                slideSectionsIn(direction);
            }

            function renderSection(label, emptyText, items) {
                const header = `
        <h2 class="text-sm font-bold px-6 mb-2 text-slate-500 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
            ${label}
        </h2>`;

                if (items.length === 0) {
                    return header +
                        `<div class="section-body"><p class="section-body-empty text-slate-400 text-sm">${emptyText}</p></div>`;
                }

                const cards = items.map(item => `
        <a href="/courses/${item.id}${selectedParam ? '?selected=' + selectedParam : ''}">
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

                return header + `<div class="section-body"><div class="carousel-container">${cards}</div></div>`;
            }

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            carousel.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        updateCarouselState();
                        ticking = false;
                    });
                    ticking = true;
                }

                clearTimeout(scrollEndTimer);
                scrollEndTimer = setTimeout(loadClosestSlide, 180);
            });

            window.addEventListener('resize', () => {
                equalizeSlideHeights();
                updateCarouselState();
            });
        }
    </script>
@endpush
