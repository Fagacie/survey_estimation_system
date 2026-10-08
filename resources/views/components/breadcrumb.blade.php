@props(['items' => null, 'links' => null])

@php
    $breadcrumbItems = $items ?? $links ?? [];
    if (array_is_list($breadcrumbItems)) {
        $breadcrumbItems = collect($breadcrumbItems)->mapWithKeys(function ($item) {
            return [$item['name'] ?? $item['label'] ?? '' => $item['url'] ?? '#'];
        })->all();
    }
@endphp

<nav {{ $attributes->merge(['class' => 'flex', 'aria-label' => 'Breadcrumb']) }}>
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($breadcrumbItems as $label => $url)
            <li class="inline-flex items-center">
                @if(!$loop->first)
                    <svg class="w-3 h-3 text-slate-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                @endif
                
                @if(is_numeric($label))
                    {{-- If no URL provided, it's just text (active page) --}}
                    <span class="text-sm font-medium text-slate-500 {{ $loop->first ? '' : 'ml-1 md:ml-2' }}">{{ $url }}</span>
                @else
                    <a href="{{ $url }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-blue-600 {{ $loop->first ? '' : 'ml-1 md:ml-2' }}">
                        {{ $label }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
