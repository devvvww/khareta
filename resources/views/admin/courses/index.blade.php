@extends('layouts.app')

@section('title', 'مواد قسم ' . $department->name)

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <a href="{{ route('admin.departments.index') }}"
                    class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                    الأقسام
                </a>
                <h1 class="text-xl font-extrabold text-slate-800">مواد {{ $department->name }}</h1>
            </div>
            <a href="{{ route('admin.departments.courses.create', $department) }}"
                class="bg-[#0b7af1] text-white text-sm font-bold px-4 py-2 rounded-xl">
                + إضافة مادة
            </a>
        </div>

        @if (session('status'))
            <div class="bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3 mb-4">{{ session('status') }}</div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
            <input type="text" id="course-search" placeholder="ابحث عن مادة..."
                class="w-full sm:flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">

            <div
                class="flex items-center bg-white border border-slate-200 rounded-xl overflow-hidden text-sm w-full sm:w-auto">
                <a href="{{ route('admin.departments.courses.index', $department) }}"
                    class="flex-1 sm:flex-none text-center px-3 py-2.5 {{ !request()->filled('elective') ? 'bg-[#0b7af1] text-white font-bold' : 'text-slate-500' }}">الكل</a>
                <a href="{{ route('admin.departments.courses.index', [$department, 'elective' => 1]) }}"
                    class="flex-1 sm:flex-none text-center px-3 py-2.5 border-s border-slate-100 {{ request()->input('elective') === '1' ? 'bg-[#0b7af1] text-white font-bold' : 'text-slate-500' }}">اختيارية</a>
                <a href="{{ route('admin.departments.courses.index', [$department, 'elective' => 0]) }}"
                    class="flex-1 sm:flex-none text-center px-3 py-2.5 border-s border-slate-100 {{ request()->input('elective') === '0' ? 'bg-[#0b7af1] text-white font-bold' : 'text-slate-500' }}">إجبارية</a>
            </div>
        </div>
        
        <div id="course-list" class="flex flex-col gap-2">
            @forelse ($courses as $course)
                <div class="course-row flex items-center justify-between gap-3 bg-white border border-slate-100 rounded-xl px-4 py-3 shadow-sm"
                    data-name="{{ mb_strtolower($course->name) }}" data-code="{{ mb_strtolower($course->code) }}">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0"
                            style="background: {{ $course->color ?: '#64748b' }};"></span>
                        <span class="font-bold text-slate-800 truncate">{{ $course->name }}</span>
                        <span class="shrink-0 text-xs font-mono text-slate-400" dir="ltr">{{ $course->code }}</span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm text-slate-500">تعديل</a>

                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                            id="delete-course-{{ $course->id }}" class="inline">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="delete-trigger text-sm text-rose-500 cursor-pointer"
                            data-form-id="delete-course-{{ $course->id }}" data-name="{{ $course->name }}"
                            data-affected="{{ $course->requiredForCourses->pluck('name')->implode("\n") }}">حذف</button>
                    </div>
                </div>
            @empty
                <p class="text-slate-400 text-sm text-center py-6">لا توجد مواد</p>
            @endforelse
        </div>

        <p id="course-empty-state" class="hidden text-slate-400 text-sm text-center py-6">لا توجد نتائج</p>
    </div>
    <p id="course-empty-state" class="hidden text-slate-400 text-sm text-center py-6">لا توجد نتائج</p>

    {{-- Confirm delete modal --}}
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
            <h2 class="text-lg font-extrabold text-slate-800 mb-2">تأكيد الحذف</h2>
            <p class="text-sm text-slate-500 mb-3">هل أنت متأكد من حذف <span id="delete-modal-name"
                    class="font-bold text-slate-700"></span>؟ لا يمكن التراجع عن هذا الإجراء.</p>

            <div id="delete-modal-warning" class="hidden bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4">
                <p class="text-xs font-bold text-amber-700 mb-1">تنبيه: هذه المادة متطلب سابق لـ:</p>
                <p id="delete-modal-affected" class="text-xs text-amber-700 whitespace-pre-line"></p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" id="delete-modal-cancel"
                    class="flex-1 bg-slate-100 text-slate-600 font-bold py-2.5 rounded-xl cursor-pointer">إلغاء</button>
                <button type="button" id="delete-modal-confirm"
                    class="flex-1 bg-rose-500 text-white font-bold py-2.5 rounded-xl cursor-pointer">حذف</button>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            const courseSearch = document.getElementById('course-search');
            const courseRows = [...document.querySelectorAll('.course-row')];
            const emptyState = document.getElementById('course-empty-state');
            const deleteModal = document.getElementById('delete-modal');
            const deleteModalName = document.getElementById('delete-modal-name');
            const deleteModalWarning = document.getElementById('delete-modal-warning');
            const deleteModalAffected = document.getElementById('delete-modal-affected');
            const deleteModalConfirm = document.getElementById('delete-modal-confirm');
            const deleteModalCancel = document.getElementById('delete-modal-cancel');
            let pendingFormId = null;

            courseSearch.addEventListener('input', () => {
                const q = courseSearch.value.trim().toLowerCase();
                let visibleCount = 0;

                courseRows.forEach(row => {
                    const matches = row.dataset.name.includes(q) || row.dataset.code.includes(q);
                    row.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });

                emptyState.classList.toggle('hidden', visibleCount > 0 || courseRows.length === 0);
            });


            document.querySelectorAll('.delete-trigger').forEach(btn => {
                btn.addEventListener('click', () => {
                    pendingFormId = btn.dataset.formId;
                    deleteModalName.textContent = btn.dataset.name;

                    const affected = btn.dataset.affected;
                    if (affected && affected.trim() !== '') {
                        deleteModalAffected.textContent = affected;
                        deleteModalWarning.classList.remove('hidden');
                    } else {
                        deleteModalWarning.classList.add('hidden');
                    }

                    deleteModal.classList.remove('hidden');
                });
            });

            deleteModalCancel.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
                pendingFormId = null;
            });

            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal) {
                    deleteModal.classList.add('hidden');
                    pendingFormId = null;
                }
            });

            deleteModalConfirm.addEventListener('click', () => {
                if (pendingFormId) {
                    document.getElementById(pendingFormId).submit();
                }
            });
        </script>
    @endpush
@endsection
