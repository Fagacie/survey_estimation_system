@php
    $links = [
        ['label' => 'Builder',  'route' => 'projects.modeling.builder'],
        ['label' => 'Items',    'route' => 'projects.modeling.items.index'],
        ['label' => 'Modules',  'route' => 'projects.modeling.modules.index'],
        ['label' => 'Packages', 'route' => 'projects.modeling.packages.index'],
    ];
@endphp

<nav class="mnav" aria-label="Modelling management">
    @foreach ($links as $link)
        @php $active = request()->routeIs($link['route']); @endphp
        <a href="{{ route($link['route']) }}"
           class="mnav-link {{ $active ? 'mnav-active' : '' }}" {{ $active ? 'aria-current=page' : '' }}>
            {{ $link['label'] }}
        </a>
    @endforeach
</nav>