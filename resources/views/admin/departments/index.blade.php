@extends('layouts.app')

@section('title', 'إدارة الأقسام')

@section('content')
    <div class="max-w-3xl mx-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-extrabold text-slate-800">إدارة الأقسام</h1>
            <a href="{{ route('admin.departments.create') }}"
                class="bg-[#0b7af1] text-white text-sm font-bold px-4 py-2 rounded-xl">
                + إضافة قسم
            </a>
        </div>

        @if (session('status'))
            <div class="bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3 mb-4">{{ session('status') }}</div>
        @endif

        <input type="text" id="department-search" placeholder="ابحث عن قسم..."
            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-[#0b7af1] focus:border-transparent">

        <div id="department-list" class="flex flex-col gap-2">
            @foreach ($departments as $department)
                <div
                    class="flex items-center justify-between gap-3 bg-white border border-slate-100 rounded-xl px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <span class="font-bold text-slate-800 truncate">{{ $department->name }}</span>
                        <span class="shrink-0 bg-slate-100 text-slate-500 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $department->courses_count }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.departments.courses.index', $department) }}"
                            class="text-sm text-[#0b7af1] font-bold">المواد</a>
                        <a href="{{ route('admin.departments.edit', $department) }}"
                            class="text-sm text-slate-500">تعديل</a>

                        <form method="POST" action="{{ route('admin.departments.destroy', $department) }}"
                            id="delete-department-{{ $department->id }}" class="inline">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="delete-trigger text-sm text-rose-500 cursor-pointer"
                            data-form-id="delete-department-{{ $department->id }}"
                            data-name="{{ $department->name }}">حذف</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Confirm delete modal --}}
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
            <h2 class="text-lg font-extrabold text-slate-800 mb-2">تأكيد الحذف</h2>
            <p class="text-sm text-slate-500 mb-6">هل أنت متأكد من حذف <span id="delete-modal-name"
                    class="font-bold text-slate-700"></span>؟ لا يمكن التراجع عن هذا الإجراء.</p>
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
            const departmentSearch = document.getElementById('department-search');
            const departmentRows = [...document.querySelectorAll('.department-row')];

            departmentSearch.addEventListener('input', () => {
                const q = departmentSearch.value.trim().toLowerCase();
                departmentRows.forEach(row => {
                    row.classList.toggle('hidden', !row.dataset.name.includes(q));
                });
            });

            const deleteModal = document.getElementById('delete-modal');
            const deleteModalName = document.getElementById('delete-modal-name');
            const deleteModalConfirm = document.getElementById('delete-modal-confirm');
            const deleteModalCancel = document.getElementById('delete-modal-cancel');
            let pendingFormId = null;

            document.querySelectorAll('.delete-trigger').forEach(btn => {
                btn.addEventListener('click', () => {
                    pendingFormId = btn.dataset.formId;
                    deleteModalName.textContent = btn.dataset.name;
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
