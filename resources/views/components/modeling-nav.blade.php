@php
    $links = [
        ['label' => 'Builder',  'route' => 'projects.modeling.builder'],
        ['label' => 'Items',    'route' => 'projects.modeling.items.index'],
        ['label' => 'Modules',  'route' => 'projects.modeling.modules.index'],
        ['label' => 'Packages', 'route' => 'projects.modeling.packages.index'],
    ];
@endphp

<style>
    .mnav-link:not(.mnav-active):hover { background: rgba(226, 232, 240, 0.6); color: #0f172a !important; }
</style>

<nav style="display:inline-flex; align-items:center; gap:4px; background:rgba(241,245,249,0.8); padding:6px; border-radius:9999px; font-size:0.875rem; font-weight:500;">
    @foreach ($links as $link)
        @php $active = request()->routeIs($link['route']); @endphp
        <a href="{{ route($link['route']) }}"
           class="mnav-link {{ $active ? 'mnav-active' : '' }}"
           style="padding:8px 20px; border-radius:9999px; text-decoration:none; transition:all 0.15s; {{ $active ? 'background:#0f766e; color:#ffffff; font-weight:600; box-shadow:0 1px 2px rgba(0,0,0,0.1);' : 'color:#475569;' }}">
            {{ $link['label'] }}
        </a>
    @endforeach
</nav>