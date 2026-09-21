<nav class="ises-quotation-nav">
    <a class="ises-quotation-brand" href="{{ route('projects.index') }}">
        <span class="ises-quotation-mark"><i class="fa-solid fa-compass-drafting"></i></span>
        <span><strong>ISES</strong><small>Survey Operations</small></span>
    </a>
    <div class="ises-quotation-links">
        <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') ? 'active' : '' }}"><i class="fa-solid fa-map-location-dot"></i> Survey Projects</a>
        <a href="{{ route('quotations.history') }}" class="{{ request()->is('quotations*') || request()->is('history') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar"></i> Quotation History</a>
    </div>
    @auth
        <div class="ises-quotation-user">
            <a href="{{ route('profile.edit') }}"><span class="ises-quotation-avatar">{{ substr(auth()->user()->name, 0, 1) }}</span>{{ auth()->user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button title="Sign out"><i class="fa-solid fa-arrow-right-from-bracket"></i></button></form>
        </div>
    @endauth
</nav>
