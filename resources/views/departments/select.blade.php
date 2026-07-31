@extends('layouts.app')

@section('title', 'اختر القسم — مسار المواد الدراسية')

@section('content')
<div class="flex flex-col items-center justify-start min-h-screen pt-[25vh] p-6">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-xl font-extrabold text-slate-800">اختر قسمك</h1>
            <p class="text-sm text-slate-400 mt-1">لعرض المواد الدراسية الخاصة بقسمك</p>
        </div>

        <form method="POST" action="{{ route('departments.store') }}" id="department-form">
            @csrf
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            <input type="hidden" name="department_id" id="department-id-input" value="">

            <div class="relative">
                <!-- Big center bar -->
                <button type="button" id="department-bar"
                    class="w-full flex items-center justify-between gap-3 bg-white rounded-2xl px-5 py-5 shadow-sm border border-slate-100 active:scale-[0.98] transition-transform">
                    <span class="flex items-center gap-3">
                        <span id="department-bar-label" class="text-slate-800 font-bold text-base">اختر القسم</span>
                    </span>
                    <svg id="department-bar-chevron" class="w-5 h-5 text-slate-400 transition-transform" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div id="department-dropdown"
                    class="hidden absolute z-10 mt-2 w-full bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden max-h-72 overflow-y-auto">
                    @foreach ($departments as $department)
                        <button type="button"
                            class="department-option w-full flex items-center gap-3 px-5 py-4 text-start hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-b-0"
                            data-id="{{ $department->id }}"
                            data-name="{{ $department->name }}">
                            <span class="text-slate-800 font-bold text-sm">{{ $department->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </form>

    </div>
</div>

<script>
(function () {
    const bar = document.getElementById('department-bar');
    const chevron = document.getElementById('department-bar-chevron');
    const dropdown = document.getElementById('department-dropdown');
    const label = document.getElementById('department-bar-label');
    const hiddenInput = document.getElementById('department-id-input');
    const form = document.getElementById('department-form');

    function toggleDropdown() {
        dropdown.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }

    bar.addEventListener('click', toggleDropdown);

    document.querySelectorAll('.department-option').forEach(function (option) {
        option.addEventListener('click', function () {
            const id = option.dataset.id;
            const name = option.dataset.name;

            hiddenInput.value = id;
            label.textContent = name;

            dropdown.classList.add('hidden');
            chevron.classList.remove('rotate-180');

            form.submit();
        });
    });

    document.addEventListener('click', function (e) {
        if (!bar.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    });
})();
</script>
@endsection