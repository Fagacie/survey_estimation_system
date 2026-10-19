@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold leading-5 text-slate-900 bg-slate-100 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium leading-5 text-slate-600 hover:text-slate-900 hover:bg-slate-50 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
