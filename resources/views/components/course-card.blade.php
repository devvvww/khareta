{{--
    Course card component.

    Props:
      variant  string  one of: red | green | gray | blue  (controls background color)
      title    string  course title
      code     string  course code, e.g. "ITGS101" (optional)

    Usage:
      <x-course-card variant="red" title="برمجة ويب متقدمة" code="ITGS101" />
--}}
@props([
    'variant' => 'gray',
    'title' => '',
    'code' => '',
])

<div class="course-card">
    <div class="card-header {{ $variant }}-header">
        <div class="flex flex-col items-center justify-center gap-1">
            <span>{{ $title }}</span>
            @if ($code)
                <span class="text-[10px] font-mono font-normal tracking-widest opacity-75"
                    dir="ltr">{{ $code }}</span>
            @endif
        </div>
    </div>
</div>
