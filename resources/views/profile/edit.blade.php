@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile-settings.css') }}?v={{ filemtime(public_path('css/profile-settings.css')) }}">
@endpush

<x-app-layout containerClass="px-0">

    <div class="profile-settings-page">
        <section class="profile-settings-header">
            <div class="profile-settings-header-inner">
                <a href="{{ route('dashboard') }}" class="profile-settings-back">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    <span>Back to dashboard</span>
                </a>
                <div class="profile-settings-heading">
                    <span class="profile-settings-eyebrow">ACCOUNT SETTINGS</span>
                    <h1>My profile</h1>
                    <p>Manage your personal information and account security.</p>
                </div>
            </div>
        </section>

        <div class="profile-settings-content">
            <section class="profile-overview-card" aria-label="Account overview">
                <div class="profile-overview-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</div>
                <div class="profile-overview-details">
                    <span class="profile-overview-label">SIGNED-IN ACCOUNT</span>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="profile-overview-meta">
                    <span class="profile-meta-label">Account created</span>
                    <strong>{{ $user->created_at?->format('d M Y') ?? 'Date unavailable' }}</strong>
                </div>
            </section>

            @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
                <div class="profile-global-status" role="status">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    Your changes have been saved.
                </div>
            @endif

            <div class="profile-settings-grid">
                <div class="profile-settings-main">
                    <section class="profile-settings-card profile-settings-information">
                        <div class="profile-section-heading">
                            <span class="profile-section-icon"><i class="fa-regular fa-id-card" aria-hidden="true"></i></span>
                            <div>
                                <h2>Personal information</h2>
                                <p>Update the name and email address associated with your account.</p>
                            </div>
                        </div>
                        @include('profile.partials.update-profile-information-form')
                    </section>

                    <section class="profile-settings-card profile-settings-security">
                        <div class="profile-section-heading">
                            <span class="profile-section-icon"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
                            <div>
                                <h2>Account security</h2>
                                <p>Change your password to help keep your account secure.</p>
                            </div>
                        </div>
                        @include('profile.partials.update-password-form')
                    </section>
                </div>

                <aside class="profile-settings-aside">
                    <section class="profile-help-card">
                        <span class="profile-help-icon"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>
                        <h2>Keep your account secure</h2>
                        <p>Use a unique password that you do not use for other websites. Never share your password with anyone.</p>
                    </section>

                    <section class="profile-danger-card">
                        <div class="profile-danger-heading">
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                            <h2>Delete account</h2>
                        </div>
                        <p>Account deletion is permanent and may remove access to associated information. Only continue if you are certain.</p>
                        @include('profile.partials.delete-user-form')
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
