@extends('layouts.app')

@section('title', $department->exists ? 'تعديل قسم' : 'إضافة قسم')

@section('content')
    <div class="max-w-md mx-auto p-6">
        <a href="{{ route('admin.departments.index') }}"
            class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            الأقسام
        </a>
        <h1 class="text-xl font-extrabold text-slate-800 mb-6">{{ $department->exists ? 'تعديل قسم' : 'إضافة قسم' }}</h1>

        <form method="POST"
            action="{{ $department->exists ? route('admin.departments.update', $department) : route('admin.departments.store') }}">
            @csrf
            @if ($department->exists)
                @method('PUT')
            @endif

            <label class="block text-sm font-bold text-slate-700 mb-2">اسم القسم</label>
            <input type="text" name="name" value="{{ old('name', $department->name) }}"
                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm mb-1 focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">
            @error('name')
                <p class="text-rose-500 text-xs mb-3">{{ $message }}</p>
            @enderror

            @if (!$department->exists)
                {{-- CREATE MODE: primary prefix (required) + optional extra prefixes chip builder --}}
                <label class="block text-sm font-bold text-slate-700 mb-2 mt-4">الرمز الأساسي</label>
                <input type="text" name="prefix" value="{{ old('prefix') }}" dir="ltr" maxlength="10"
                    placeholder="ITSE"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm mb-1 uppercase focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">
                @error('prefix')
                    <p class="text-rose-500 text-xs mb-2">{{ $message }}</p>
                @enderror

                <label class="block text-sm font-bold text-slate-700 mb-2 mt-4">رموز القسم الإضافية (اختياري)</label>
                <div id="prefix-chips" class="flex flex-wrap gap-2 mb-3">
                    @foreach (old('prefixes', []) as $existingPrefix)
                        <span
                            class="prefix-chip flex items-center gap-1.5 bg-[#0b7af1]/10 text-[#0b7af1] text-sm font-bold px-3 py-1.5 rounded-full"
                            dir="ltr">
                            {{ $existingPrefix }}
                            <button type="button" class="remove-prefix-chip text-xs">×</button>
                            <input type="hidden" name="prefixes[]" value="{{ $existingPrefix }}">
                        </span>
                    @endforeach
                </div>

                <div class="w-full flex items-center gap-2 mb-1">
                    <input type="text" id="prefix-input" dir="ltr" maxlength="10" placeholder="ITPH"
                        class="flex-1 min-w-0 rounded-xl border border-slate-200 px-4 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">
                    <button type="button" id="add-prefix-btn"
                        class="shrink-0 bg-slate-100 text-slate-600 text-sm font-bold px-4 py-2.5 rounded-xl cursor-pointer">+
                        إضافة</button>
                </div>
                @error('prefixes.*')
                    <p class="text-rose-500 text-xs mb-2">{{ $message }}</p>
                @enderror
            @else
                {{-- EDIT MODE: primary prefix has its own editable input --}}
                <label class="block text-sm font-bold text-slate-700 mb-2 mt-4">الرمز الأساسي</label>
                <input type="text" name="primary_prefix" value="{{ old('primary_prefix', $primaryPrefix?->prefix) }}"
                    dir="ltr" maxlength="10"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm mb-1 uppercase focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">
                @error('primary_prefix')
                    <p class="text-rose-500 text-xs mb-2">{{ $message }}</p>
                @enderror

                {{-- Extra prefixes: added/removed instantly via AJAX, no page reload --}}
                <label class="block text-sm font-bold text-slate-700 mb-2 mt-6">رموز القسم الإضافية</label>
                <div id="extra-prefix-chips" class="flex flex-wrap gap-2 mb-3">
                    @foreach ($additionalPrefixes as $extra)
                        <span class="extra-prefix-chip flex items-center gap-1.5 bg-[#0b7af1]/10 text-[#0b7af1] text-sm font-bold px-3 py-1.5 rounded-full"
                            dir="ltr" data-id="{{ $extra->id }}">
                            {{ $extra->prefix }}
                            <button type="button" class="remove-extra-prefix text-xs" data-id="{{ $extra->id }}">×</button>
                        </span>
                    @endforeach
                    @if ($additionalPrefixes->isEmpty())
                        <p id="no-extra-prefixes" class="text-xs text-slate-400">لا يوجد رموز إضافية</p>
                    @endif
                </div>

                <div class="w-full flex items-center gap-2 mb-1">
                    <input type="text" id="extra-prefix-input" dir="ltr" maxlength="10" placeholder="ITPH"
                        class="flex-1 min-w-0 rounded-xl border border-slate-200 px-4 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">
                    <button type="button" id="add-extra-prefix-btn"
                        class="shrink-0 bg-slate-100 text-slate-600 text-sm font-bold px-4 py-2.5 rounded-xl cursor-pointer">+
                        إضافة</button>
                </div>
                <p id="extra-prefix-error" class="hidden text-rose-500 text-xs mb-2"></p>
            @endif

            @php $allowsElectives = old('allows_electives', $department->exists ? $department->allows_electives : false); @endphp
            <label id="allows-electives-row" class="flex items-center gap-3 mt-6 cursor-pointer">
                <span class="relative w-5 h-5 shrink-0">
                    <input type="checkbox" name="allows_electives" value="1" id="allows-electives-checkbox"
                        class="absolute inset-0 opacity-0 cursor-pointer" {{ $allowsElectives ? 'checked' : '' }}>
                    <span id="allows-electives-indicator"
                        class="check-indicator w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors {{ $allowsElectives ? 'bg-[#0b7af1] border-[#0b7af1]' : 'border-slate-300' }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-3 h-3 text-white {{ $allowsElectives ? '' : 'hidden' }}" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </span>
                <span class="text-sm font-bold text-slate-700">يحتوي على مواد اختيارية</span>
            </label>

            <label class="block text-sm font-bold text-slate-700 mb-2 mt-6">اللون</label>

            @php
                $palette = ['#8b5cf6', '#f97316', '#ec4899', '#6366f1', '#78716c', '#0ea5e9', '#84cc16', '#f43f5e'];
                $currentColor = old('color', $department->color);
                $currentIsCustom =
                    $currentColor && !in_array(strtolower($currentColor), array_map('strtolower', $palette));
            @endphp

            <input type="hidden" name="color" id="color-hidden" value="{{ $currentColor ?: '#8b5cf6' }}">

            <div id="color-palette" class="flex flex-wrap items-center gap-2">
                @if ($currentIsCustom)
                    <button type="button"
                        class="palette-swatch custom-saved-swatch w-8 h-8 rounded-full border-2 border-white shadow ring-1 ring-slate-200"
                        style="background: {{ $currentColor }};" data-color="{{ $currentColor }}"
                        title="{{ $currentColor }}"></button>
                @endif

                @foreach ($palette as $swatch)
                    <button type="button"
                        class="palette-swatch w-8 h-8 rounded-full border-2 border-white shadow ring-1 ring-slate-200 transition-shadow cursor-pointer"
                        style="background: {{ $swatch }};" data-color="{{ $swatch }}"
                        title="{{ $swatch }}"></button>
                @endforeach

                <div class="relative w-8 h-8 shrink-0">
                    <button type="button" id="custom-color-trigger"
                        class="w-8 h-8 rounded-full border-2 border-dashed border-slate-300 flex items-center justify-center text-slate-400 hover:border-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                    <input type="color" id="custom-color-input"
                        class="absolute inset-0 w-8 h-8 opacity-0 cursor-pointer">
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
        const colorHidden = document.getElementById('color-hidden');
        const customTrigger = document.getElementById('custom-color-trigger');
        const customInput = document.getElementById('custom-color-input');

        const allowsElectivesCheckbox = document.getElementById('allows-electives-checkbox');
        const allowsElectivesIndicator = document.getElementById('allows-electives-indicator');
        const allowsElectivesIcon = allowsElectivesIndicator.querySelector('svg');

        allowsElectivesCheckbox.addEventListener('change', () => {
            const isChecked = allowsElectivesCheckbox.checked;
            allowsElectivesIndicator.classList.toggle('bg-[#0b7af1]', isChecked);
            allowsElectivesIndicator.classList.toggle('border-[#0b7af1]', isChecked);
            allowsElectivesIndicator.classList.toggle('border-slate-300', !isChecked);
            allowsElectivesIcon.classList.toggle('hidden', !isChecked);
        });

        // CREATE MODE — chip builder, unsaved until the whole form is submitted
        const prefixChips = document.getElementById('prefix-chips');
        const prefixInput = document.getElementById('prefix-input');
        const addPrefixBtn = document.getElementById('add-prefix-btn');

        if (prefixChips) {
            function addPrefixChip(value) {
                const clean = value.trim().toUpperCase();
                if (!clean) return;

                const existing = [...prefixChips.querySelectorAll('input[name="prefixes[]"]')].map(i => i.value);
                if (existing.includes(clean)) return;

                const chip = document.createElement('span');
                chip.className =
                    'prefix-chip flex items-center gap-1.5 bg-[#0b7af1]/10 text-[#0b7af1] text-sm font-bold px-3 py-1.5 rounded-full';
                chip.dir = 'ltr';
                chip.innerHTML =
                    `${clean} <button type="button" class="remove-prefix-chip text-xs">×</button> <input type="hidden" name="prefixes[]" value="${clean}">`;
                prefixChips.appendChild(chip);

                prefixInput.value = '';
            }

            addPrefixBtn.addEventListener('click', () => addPrefixChip(prefixInput.value));

            prefixInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addPrefixChip(prefixInput.value);
                }
            });

            prefixChips.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-prefix-chip')) {
                    e.target.closest('.prefix-chip').remove();
                }
            });
        }

        // EDIT MODE — extra prefixes saved instantly via AJAX
        const extraPrefixChips = document.getElementById('extra-prefix-chips');
        const extraPrefixInput = document.getElementById('extra-prefix-input');
        const addExtraPrefixBtn = document.getElementById('add-extra-prefix-btn');
        const extraPrefixError = document.getElementById('extra-prefix-error');
        const noExtraPrefixesMsg = document.getElementById('no-extra-prefixes');

        if (addExtraPrefixBtn) {
            addExtraPrefixBtn.addEventListener('click', async () => {
                const value = extraPrefixInput.value.trim().toUpperCase();
                extraPrefixError.classList.add('hidden');
                if (!value) return;

                try {
                    const res = await fetch('{{ $department->exists ? route('admin.departments.prefixes.store', $department) : '#' }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ prefix: value }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        extraPrefixError.textContent = data.message || (data.errors && data.errors.prefix ? data.errors.prefix[0] : 'حدث خطأ ما');
                        extraPrefixError.classList.remove('hidden');
                        return;
                    }

                    if (noExtraPrefixesMsg) noExtraPrefixesMsg.remove();

                    const chip = document.createElement('span');
                    chip.className = 'extra-prefix-chip flex items-center gap-1.5 bg-[#0b7af1]/10 text-[#0b7af1] text-sm font-bold px-3 py-1.5 rounded-full';
                    chip.dir = 'ltr';
                    chip.dataset.id = data.id;
                    chip.innerHTML = `${data.prefix} <button type="button" class="remove-extra-prefix text-xs" data-id="${data.id}">×</button>`;
                    extraPrefixChips.appendChild(chip);

                    extraPrefixInput.value = '';
                } catch (err) {
                    extraPrefixError.textContent = 'حدث خطأ في الاتصال';
                    extraPrefixError.classList.remove('hidden');
                }
            });

            extraPrefixInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addExtraPrefixBtn.click();
                }
            });

            extraPrefixChips.addEventListener('click', async (e) => {
                if (!e.target.classList.contains('remove-extra-prefix')) return;

                const id = e.target.dataset.id;
                if (!confirm('حذف هذا الرمز؟')) return;

                try {
                    const fd = new FormData();
                    fd.append('_method', 'DELETE');

                    await fetch(`/admin/prefixes/${id}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: fd,
                    });

                    e.target.closest('.extra-prefix-chip').remove();
                } catch (err) {
                    alert('تعذر حذف الرمز');
                }
            });
        }

        function bindPaletteButtons() {
            document.querySelectorAll('.palette-swatch').forEach(btn => {
                btn.addEventListener('click', () => {
                    colorHidden.value = btn.dataset.color;
                    highlightActiveSwatch(btn.dataset.color);
                });
            });
        }

        customInput.addEventListener('input', () => {
            colorHidden.value = customInput.value;
            customTrigger.style.background = customInput.value;
            customTrigger.style.borderStyle = 'solid';
            highlightActiveSwatch(customInput.value);
        });

        function highlightActiveSwatch(color) {
            const normalized = color.toLowerCase();
            let matchedPalette = false;

            document.querySelectorAll('.palette-swatch').forEach(btn => {
                const isActive = btn.dataset.color.toLowerCase() === normalized;
                btn.classList.toggle('ring-2', isActive);
                btn.classList.toggle('ring-[#0b7af1]', isActive);
                btn.classList.toggle('ring-1', !isActive);
                btn.classList.toggle('ring-slate-200', !isActive);
                if (isActive) matchedPalette = true;
            });

            if (!matchedPalette) {
                customTrigger.classList.add('ring-2', 'ring-[#0b7af1]');
                customTrigger.classList.remove('ring-1', 'ring-slate-200');
            } else {
                customTrigger.classList.remove('ring-2', 'ring-[#0b7af1]');
                customTrigger.style.background = '';
                customTrigger.style.borderStyle = 'dashed';
            }
        }

        bindPaletteButtons();
        highlightActiveSwatch(colorHidden.value);
    </script>
@endpush