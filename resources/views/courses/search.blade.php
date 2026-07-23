@extends('layouts.app')

@section('title', 'البحث عن المواد — مسار المواد الدراسية')

@section('content')
    <div class="flex flex-col items-center min-h-screen p-0 md:p-6">
        <div class="course-flow-page flex flex-col min-h-screen md:min-h-0">

            <div class="pt-10 pb-6 px-6">
                <h1 class="text-xl font-extrabold text-slate-800 mb-4">البحث عن المواد</h1>

                <input type="text" id="search-input" placeholder="ابحث باسم المادة أو الرمز..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0b7af1]"
                    autofocus autocomplete="off">
            </div>

            <div class="flex-grow px-6 pb-10">
                <p id="search-status" class="text-slate-400 text-center mt-10"></p>
                <div id="search-results" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const input = document.getElementById('search-input');
        const results = document.getElementById('search-results');
        const status = document.getElementById('search-status');

        let debounceTimer;

        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);

            const q = input.value.trim();

            if (!q) {
                results.innerHTML = '';
                status.textContent = '';
                return;
            }

            debounceTimer = setTimeout(() => runSearch(q), 300);
        });

        async function runSearch(q) {
            status.textContent = 'جارِ البحث...';
            results.style.opacity = '0';

            try {
                const res = await fetch(`{{ route('courses.search') }}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'Accept': 'application/json'
                    },
                });
                const courses = await res.json();

                if (courses.length === 0) {
                    results.innerHTML = '';
                    results.style.opacity = '1';
                    status.textContent = `لا توجد نتائج لـ "${q}"`;
                    return;
                }

                status.textContent = '';
                results.innerHTML = courses.map((course, i) => `
            <a href="/courses/${course.id}" class="course-card-enter" style="animation-delay: ${i * 40}ms">
                <div class="course-card">
                    <div class="card-header" style="background: ${course.color};">
                        <div class="flex flex-col items-center justify-center gap-1">
                            <span>${escapeHtml(course.name)}</span>
                            ${course.code ? `<span class="text-[10px] font-mono font-normal tracking-widest opacity-75" dir="ltr">${escapeHtml(course.code)}</span>` : ''}
                        </div>
                    </div>
                </div>
            </a>
        `).join('');

                results.style.opacity = '1';
            } catch (e) {
                status.textContent = 'حدث خطأ أثناء البحث';
                results.style.opacity = '1';
            }
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
@endpush
