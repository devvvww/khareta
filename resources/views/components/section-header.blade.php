{{--
    Section header (the small dot + label above each carousel), e.g.
    "مواد تتطلب هذه المادة :" / "مواد مطلوبة لهذه المادة:"

    Props:
      color  string  Tailwind color name used for the dot + text, e.g. "rose-500" or "slate-500"
--}}
@props([
    'color' => 'slate-500',
])

<h2 {{ $attributes->merge(['class' => "text-sm font-bold px-6 mb-2 text-$color flex items-center gap-2"]) }}>
    <span class="w-2 h-2 rounded-full bg-{{ $color }}"></span>
    {{ $slot }}
</h2>