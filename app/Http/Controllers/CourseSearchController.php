<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->wantsJson()) {
            $query = $request->input('q');

            $courses = Course::query()
                ->when($query, function ($builder) use ($query) {
                    $normalized = $this->normalizeArabic($query);

                    $builder->where(function ($b) use ($normalized, $query) {
                        $b->whereRaw($this->normalizedColumnSql('name') . ' LIKE ?', ["%{$normalized}%"])
                            ->orWhere('code', 'like', "%{$query}%");
                    });
                })
                ->orderBy('name')
                ->limit(20)
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'code' => $c->code,
                    'color' => $c->color ?: '#64748b',
                ]);

            return response()->json($courses);
        }

        return view('courses.search');
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
