@extends('layouts.app')

@section('title', $course->exists ? 'تعديل مادة' : 'إضافة مادة')

@section('content')
    <div class="max-w-md mx-auto p-6">
        <a href="{{ route('admin.departments.courses.index', $department) }}"
            class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            مواد {{ $department->name }}
        </a>
        <h1 class="text-xl font-extrabold text-slate-800 mb-6">{{ $course->exists ? 'تعديل مادة' : 'إضافة مادة' }}</h1>

        <form method="POST"
            action="{{ $course->exists ? route('admin.courses.update', $course) : route('admin.departments.courses.store', $department) }}">
            @csrf
            @if ($course->exists)
                @method('PUT')
            @endif

            <label class="block text-sm font-bold text-slate-700 mb-1">اسم المادة</label>
            <input type="text" name="name" value="{{ old('name', $course->name) }}"
                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm mb-1 focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">
            @error('name')
                <p class="text-rose-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            <label class="block text-sm font-bold text-slate-700 mb-1 mt-4">رمز المادة</label>
            <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-[#0b7af1] mb-1"
                dir="ltr">
                @if ($department->prefixes->count() === 1)
                    <span class="bg-slate-100 text-slate-500 font-mono font-bold text-sm px-4 py-3 select-none shrink-0">
                        {{ $department->prefixes->first()->prefix }}
                    </span>
                    <input type="hidden" name="department_prefix_id" value="{{ $department->prefixes->first()->id }}">
                @else
                    <div class="relative shrink-0">
                        <select name="department_prefix_id"
                            class="appearance-none bg-slate-100 text-slate-500 font-mono font-bold text-sm pe-8 ps-4 py-3 outline-none cursor-pointer border-none">
                            @foreach ($department->prefixes as $prefix)
                                <option value="{{ $prefix->id }}"
                                    {{ old('department_prefix_id', $course->department_prefix_id) == $prefix->id ? 'selected' : '' }}>
                                    {{ $prefix->prefix }}
                                </option>
                            @endforeach
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-3.5 h-3.5 text-slate-400 absolute top-1/2 -translate-y-1/2 end-3 pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                @endif
                <input type="text" name="code_number" id="code-number-input"
                    value="{{ old('code_number', $codeNumber) }}" inputmode="numeric" maxlength="3" pattern="[0-9]{3}"
                    class="flex-1 px-4 py-3 text-sm outline-none min-w-0" placeholder="401">
            </div>
            @error('code_number')
                <p class="text-rose-500 text-xs mb-2">{{ $message }}</p>
            @enderror
            @error('department_prefix_id')
                <p class="text-rose-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            @php
                $isElective = $department->allows_electives ? old('is_elective', $course->is_elective) : false;
            @endphp

            <label id="elective-row"
                class="flex items-center gap-3 mt-4 {{ !$department->allows_electives ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                <span class="relative w-5 h-5 shrink-0">
                    <input type="checkbox" name="is_elective" value="1" id="elective-checkbox"
                        class="absolute inset-0 opacity-0 {{ !$department->allows_electives ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                        {{ $isElective ? 'checked' : '' }} {{ !$department->allows_electives ? 'disabled' : '' }}>
                    <span id="elective-indicator"
                        class="check-indicator w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors {{ $isElective ? 'bg-[#0b7af1] border-[#0b7af1]' : 'border-slate-300' }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-3 h-3 text-white {{ $isElective ? '' : 'hidden' }}" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </span>
                <span class="text-sm font-bold text-slate-700">مادة اختيارية</span>
                @unless ($department->allows_electives)
                    <span class="text-xs text-slate-400">(لا يوجد مواد اختيارية في هذا القسم)</span>
                @endunless
            </label>

            <label class="block text-sm font-bold text-slate-700 mb-2 mt-6">المتطلبات السابقة</label>

            <div
                class="border border-slate-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-[#0b7af1] focus-within:border-transparent">
                <div class="relative border-b border-slate-100">
                    <input type="text" id="prerequisite-search" placeholder="ابحث عن مادة..."
                        class="w-full px-4 py-2.5 pe-9 text-sm outline-none">
                    <button type="button" id="prerequisite-search-clear"
                        class="hidden absolute top-1/2 -translate-y-1/2 end-2 w-6 h-6 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="prerequisite-list" class="max-h-56 overflow-y-auto">
                    @php
                        $sortedCourses = $allCourses
                            ->sortByDesc(fn($c) => in_array($c->id, $selectedPrerequisiteIds))
                            ->values();
                    @endphp

                    @foreach ($sortedCourses as $c)
                        @php
                            $isSelected = in_array($c->id, $selectedPrerequisiteIds);
                            $isDisabled = in_array($c->id, $disabledPrerequisiteIds);
                        @endphp
                        <label
                            class="prerequisite-row flex items-center gap-3 px-4 py-3 border-b border-slate-50 last:border-0 text-sm transition-colors
                {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}
                {{ $isSelected ? 'bg-[#0b7af1]/10' : ($isDisabled ? '' : 'hover:bg-slate-50') }}"
                            data-name="{{ mb_strtolower($c->name) }}" data-code="{{ mb_strtolower($c->code) }}">
                            <span class="relative w-5 h-5 shrink-0">
                                <input type="checkbox" name="prerequisites[]" value="{{ $c->id }}"
                                    class="prerequisite-checkbox absolute inset-0 opacity-0 {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                                    {{ $isSelected ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                                <span
                                    class="check-indicator w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors {{ $isSelected ? 'bg-[#0b7af1] border-[#0b7af1]' : 'border-slate-300' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-3 h-3 text-white {{ $isSelected ? '' : 'hidden' }}" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            </span>

                            <span
                                class="font-medium {{ $isSelected ? 'text-slate-800' : 'text-slate-600' }}">{{ $c->name }}</span>
                            <span class="text-xs text-slate-400 font-mono" dir="ltr">{{ $c->code }}</span>
                            @if ($isDisabled)
                                <span class="text-xs text-slate-400 ms-auto">(لا يمكن اختيارها لأنها تتطلب هذه المادة بالفعل)</span>
                            @endif
                        </label>
                    @endforeach

                    <p id="prerequisite-empty" class="hidden text-slate-400 text-sm text-center py-4">لا توجد نتائج</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#0b7af1] text-white font-bold py-3 rounded-xl mt-6 cursor-pointer">
                حفظ
            </button>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('.prerequisite-row').forEach(row => {
            const checkbox = row.querySelector('.prerequisite-checkbox');
            const indicator = row.querySelector('.check-indicator');
            const checkIcon = indicator.querySelector('svg');
            const nameSpan = row.querySelector('span.font-medium');

            const codeNumberInput = document.getElementById('code-number-input');

            const prerequisiteSearch = document.getElementById('prerequisite-search');
            const prerequisiteList = document.getElementById('prerequisite-list');
            const prerequisiteEmpty = document.getElementById('prerequisite-empty');
            const prerequisiteRows = [...prerequisiteList.querySelectorAll('.prerequisite-row')];

            const prerequisiteClearBtn = document.getElementById('prerequisite-search-clear');

            prerequisiteSearch.addEventListener('input', () => {
                const q = prerequisiteSearch.value.trim().toLowerCase();
                let visibleCount = 0;

                prerequisiteRows.forEach(row => {
                    const matches = row.dataset.name.includes(q) || row.dataset.code.includes(q);
                    row.classList.toggle('row-collapsed', !matches);
                    if (matches) visibleCount++;
                });

                prerequisiteEmpty.classList.toggle('hidden', visibleCount > 0);
                prerequisiteClearBtn.classList.toggle('hidden', q === '');
                prerequisiteClearBtn.classList.toggle('flex', q !== '');
            });

            prerequisiteClearBtn.addEventListener('click', () => {
                prerequisiteSearch.value = '';
                prerequisiteSearch.dispatchEvent(new Event('input'));
                prerequisiteSearch.focus();
            });

            codeNumberInput.addEventListener('input', () => {
                codeNumberInput.value = codeNumberInput.value.replace(/\D/g, '').slice(0, 3);
            });

            checkbox.addEventListener('change', () => {
                const isChecked = checkbox.checked;

                row.classList.toggle('bg-[#0b7af1]/10', isChecked);
                row.classList.toggle('hover:bg-slate-50', !isChecked);

                indicator.classList.toggle('bg-[#0b7af1]', isChecked);
                indicator.classList.toggle('border-[#0b7af1]', isChecked);
                indicator.classList.toggle('border-slate-300', !isChecked);

                checkIcon.classList.toggle('hidden', !isChecked);

                nameSpan.classList.toggle('text-slate-800', isChecked);
                nameSpan.classList.toggle('text-slate-600', !isChecked);
            });
        });

        const electiveCheckbox = document.getElementById('elective-checkbox');
        const electiveIndicator = document.getElementById('elective-indicator');
        const electiveIcon = electiveIndicator.querySelector('svg');

        electiveCheckbox.addEventListener('change', () => {
            const isChecked = electiveCheckbox.checked;

            electiveIndicator.classList.toggle('bg-[#0b7af1]', isChecked);
            electiveIndicator.classList.toggle('border-[#0b7af1]', isChecked);
            electiveIndicator.classList.toggle('border-slate-300', !isChecked);

            electiveIcon.classList.toggle('hidden', !isChecked);
        });
    </script>
@endpush
