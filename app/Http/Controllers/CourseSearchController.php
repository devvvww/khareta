<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;

class CourseSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->wantsJson()) {
            $query = $request->input('q');

            $courses = Course::query()
                ->where('department_id', session('department_id'))
                ->when($query, function ($builder) use ($query) {
                    $normalized = $this->normalizeArabic($query);

                    $builder->where(function ($b) use ($normalized, $query) {
                        $b->whereRaw($this->normalizedColumnSql('name') . ' LIKE ?', ["%{$normalized}%"])
                            ->orWhere('code', 'like', "%{$query}%");
                    });
                })
                ->orderBy('name')
                ->limit($query ? 20 : 100)
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'code' => $c->code,
                    'color' => $c->color ?: '#64748b',
                ]);

            return response()->json($courses);
        }

        $idsParam = $request->query('ids');
        $initialSelection = collect();

        if ($idsParam) {
            $ids = array_values(array_unique(array_filter(array_map('intval', explode(',', $idsParam)))));

            $initialSelection = Course::whereIn('id', $ids)
                ->get()
                ->sortBy(fn($c) => array_search($c->id, $ids))
                ->values()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'code' => $c->code,
                    'color' => $c->color ?: '#64748b',
                ]);
        }

        return view('courses.search', [
            'initialSelection' => $initialSelection,
            'currentDepartment' => Department::find(session('department_id')),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    /**
     * Collapse common Arabic letter-form variants down to one canonical
     * form, so 'ادارة' and 'إدارة' match the same course.
     */
    private function normalizeArabic(string $text): string
    {
        $search  = ['أ', 'إ', 'آ', 'ٱ'];
        $replace = ['ا', 'ا', 'ا', 'ا'];

        return str_replace($search, $replace, $text);
    }

    /**
     * Same normalization, expressed as nested SQL REPLACE() calls so it
     * can be applied to the DB column at query time.
     */
    private function normalizedColumnSql(string $column): string
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(
            {$column}, 'أ','ا'), 'إ','ا'), 'آ','ا'), 'ٱ','ا')";
    }
}
