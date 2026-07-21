@props([
    'href',
    'active' => false,
])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 '
        . ($active
            ? 'bg-[#6b1d14] text-white shadow-md'
            : 'text-[#2b1d17] hover:bg-[#e8ddc8] hover:translate-x-1')
    ]) }}
>

    {{ $slot }}

</a>