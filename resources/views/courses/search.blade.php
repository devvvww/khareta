@extends('layouts.app')

@section('title', 'البحث عن المواد — مسار المواد الدراسية')

@section('content')
    <div class="flex flex-col items-center min-h-screen p-0 md:p-6">
        <div class="course-flow-page flex flex-col min-h-screen md:min-h-0" style="overflow: visible;">

            <div class="pt-10 pb-6 px-6">
                <h1 class="text-xl font-extrabold text-slate-800 mb-4">البحث عن المواد</h1>

                <div class="relative">
                    {{-- Tag-input style field: chips + text input live inside the same box --}}
                    <div id="search-input-wrapper"
                        class="w-full flex flex-wrap items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 pe-9 focus-within:ring-2 focus-within:ring-[#0b7af1] cursor-text max-h-24 overflow-y-auto">
                        <div id="chips-container" class="flex flex-wrap gap-1.5"></div>
                        <input type="text" id="search-input" placeholder="ابحث باسم المادة أو الرمز..."
                            class="flex-1 min-w-[100px] border-none outline-none focus:ring-0 text-sm py-1 bg-transparent"
                            autofocus autocomplete="off">
                    </div>

                    <button id="clear-all-btn" type="button" title="مسح الكل"
                        class="hidden absolute top-1/2 -translate-y-1/2 end-2 items-center gap-1 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 px-2 py-1">
                        <span id="selection-count-badge" class="text-[10px] font-bold"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    {{-- Live results dropdown --}}
                    <div id="search-menu"
                        class="hidden absolute inset-x-0 top-full mt-1 bg-white rounded-xl border border-slate-100 shadow-xl max-h-80 overflow-y-auto z-20">
                        <p id="search-status" class="text-slate-400 text-sm text-center py-4"></p>
                        <div id="search-results" class="flex flex-col"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Slim view bar --}}
    <div id="view-bar"
        class="hidden fixed inset-x-4 md:inset-x-auto md:left-1/2 md:-translate-x-1/2 md:w-full md:max-w-md z-30">
        <a id="view-selection-btn" href="#"
            class="block text-center bg-[#0b7af1] text-white text-sm font-bold px-5 py-3 rounded-2xl shadow-2xl">
            عرض
        </a>
    </div>
@endsection

@push('scripts')
    <script>
        const inputWrapper = document.getElementById('search-input-wrapper');
        const input = document.getElementById('search-input');
        const searchMenu = document.getElementById('search-menu');
        const results = document.getElementById('search-results');
        const status = document.getElementById('search-status');
        const chipsContainer = document.getElementById('chips-container');
        const clearAllBtn = document.getElementById('clear-all-btn');
        const viewBar = document.getElementById('view-bar');
        const viewSelectionBtn = document.getElementById('view-selection-btn');
        const selectionCountBadge = document.getElementById('selection-count-badge');

        const selectedCourses = new Map();
        let debounceTimer;

        // Clicking anywhere in the wrapper (empty space, around chips) focuses the text input,
        // just like a real tag-input component.
        inputWrapper.addEventListener('click', (e) => {
            if (e.target === inputWrapper || e.target === chipsContainer) {
                input.focus();
            }
        });

        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const q = input.value.trim();

            if (!q) {
                searchMenu.classList.add('hidden');
                results.innerHTML = '';
                status.textContent = '';
                return;
            }

            searchMenu.classList.remove('hidden');
            debounceTimer = setTimeout(() => runSearch(q), 300);
        });

        document.addEventListener('click', (e) => {
            if (!searchMenu.contains(e.target) && e.target !== input) {
                searchMenu.classList.add('hidden');
            }
        });
        input.addEventListener('focus', () => {
            if (input.value.trim()) searchMenu.classList.remove('hidden');
        });

        // Backspace on an empty input removes the last chip — standard tag-input behavior.
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value === '' && selectedCourses.size > 0) {
                const lastId = [...selectedCourses.keys()].pop();
                removeCourse(lastId);
            }
        });

        async function runSearch(q) {
            status.textContent = 'جارِ البحث...';
            results.innerHTML = '';

            try {
                const res = await fetch(`{{ route('courses.search') }}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'Accept': 'application/json'
                    },
                });
                const courses = await res.json();

                if (courses.length === 0) {
                    status.textContent = `لا توجد نتائج لـ "${q}"`;
                    return;
                }

                status.textContent = '';
                results.innerHTML = courses.map(course => `
                <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 border-b border-slate-50 last:border-0">
                    <label class="shrink-0 cursor-pointer">
                        <input type="checkbox" class="course-checkbox w-4 h-4 accent-[#0b7af1]"
                               data-id="${course.id}"
                               data-name="${escapeHtml(course.name)}"
                               data-code="${escapeHtml(course.code || '')}"
                               data-color="${course.color}"
                               ${selectedCourses.has(course.id) ? 'checked' : ''}>
                    </label>
                    <a href="/courses/${course.id}" class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: ${course.color};"></span>
                        <span class="text-sm text-slate-700 truncate">${escapeHtml(course.name)}</span>
                        ${course.code ? `<span class="text-[10px] font-mono text-slate-400 shrink-0" dir="ltr">${escapeHtml(course.code)}</span>` : ''}
                    </a>
                </div>
            `).join('');
            } catch (e) {
                status.textContent = 'حدث خطأ أثناء البحث';
            }
        }

        results.addEventListener('change', (e) => {
            if (!e.target.classList.contains('course-checkbox')) return;

            const {
                id,
                name,
                code,
                color
            } = e.target.dataset;
            const numId = parseInt(id, 10);

            if (e.target.checked) {
                selectedCourses.set(numId, {
                    name,
                    code,
                    color
                });
            } else {
                selectedCourses.delete(numId);
            }

            renderChips();
            updateViewBar();
            // input.focus();
        });

        clearAllBtn.addEventListener('click', () => {
            selectedCourses.clear();
            results.querySelectorAll('.course-checkbox').forEach(cb => cb.checked = false);
            renderChips();
            updateViewBar();
        });

        function removeCourse(id) {
            selectedCourses.delete(id);

            const cb = results.querySelector(`.course-checkbox[data-id="${id}"]`);
            if (cb) cb.checked = false;

            renderChips();
            updateViewBar();
            // input.focus();
        }

        function renderChips() {
            chipsContainer.innerHTML = [...selectedCourses.entries()].map(([id, c]) => `
            <span class="inline-flex items-center gap-1 bg-[#0b7af1]/10 text-[#0b7af1] rounded-md ps-2 pe-1 py-0.5 text-xs font-medium">
                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: ${c.color};"></span>
                <span class="max-w-[90px] truncate">${c.name}</span>
                <button type="button" class="remove-chip w-4 h-4 flex items-center justify-center rounded-full hover:bg-[#0b7af1]/20 text-sm leading-none" data-id="${id}">×</button>
            </span>
        `).join('');

            clearAllBtn.classList.toggle('hidden', selectedCourses.size === 0);
            clearAllBtn.classList.toggle('flex', selectedCourses.size > 0);
        }

        chipsContainer.addEventListener('click', (e) => {
            if (!e.target.classList.contains('remove-chip')) return;
            removeCourse(parseInt(e.target.dataset.id, 10));
        });

        function updateViewBar() {
            if (selectedCourses.size === 0) {
                viewBar.classList.add('hidden');
                return;
            }

            viewBar.classList.remove('hidden');
            const ids = [...selectedCourses.keys()];
            viewSelectionBtn.href = `/courses/${ids[0]}?ids=${ids.join(',')}`;
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function renderChips() {
            chipsContainer.innerHTML = [...selectedCourses.entries()].map(([id, c]) => `
        <span class="inline-flex items-center gap-1 bg-[#0b7af1]/10 text-[#0b7af1] rounded-md ps-2 pe-1 py-0.5 text-xs font-medium">
            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: ${c.color};"></span>
            <span class="max-w-[90px] truncate">${c.name}</span>
            <button type="button" class="remove-chip w-4 h-4 flex items-center justify-center rounded-full hover:bg-[#0b7af1]/20 text-sm leading-none" data-id="${id}">×</button>
        </span>
    `).join('');

            selectionCountBadge.textContent = selectedCourses.size;
            clearAllBtn.classList.toggle('hidden', selectedCourses.size === 0);
            clearAllBtn.classList.toggle('flex', selectedCourses.size > 0);
        }


        function positionViewBar() {
            if (!window.visualViewport) return;

            const keyboardHeight = window.innerHeight - window.visualViewport.height - window.visualViewport.offsetTop;
            const offset = Math.max(keyboardHeight, 0) + 16;

            viewBar.style.bottom = `${offset}px`;
        }

        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', positionViewBar);
            window.visualViewport.addEventListener('scroll', positionViewBar);
            positionViewBar();
        }
    </script>
@endpush
