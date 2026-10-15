<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sign in — ISES</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"><link rel="stylesheet" href="{{ asset('css/ises-auth.css') }}?v={{ filemtime(public_path('css/ises-auth.css')) }}">
</head><body><div class="auth-shell">
<aside class="auth-aside"><a class="auth-brand" href="{{ url('/') }}"><span class="auth-brand-mark"><i class="fa-solid fa-water"></i></span><span><strong>ISES</strong><small>Survey Estimation System</small></span></a>
<div class="auth-aside-copy"><span class="auth-overline">INOS · ECO HYDROTECH</span><h1>Plan with clarity.<br>Survey with confidence.</h1><p>One workspace for survey planning, time estimation, and project cost workflows.</p>
<div class="auth-feature-list"><div><i class="fa-solid fa-map-location-dot"></i><span>Survey planning workspace</span></div><div><i class="fa-solid fa-calculator"></i><span>Time and cost estimation</span></div><div><i class="fa-solid fa-file-invoice"></i><span>Project quotations and records</span></div></div></div><div class="auth-aside-foot">INSTITUTE OF OCEANOGRAPHY AND ENVIRONMENT</div></aside>
<main class="auth-main"><section class="auth-card" aria-labelledby="auth-title"><div class="auth-mobile-brand"><span class="auth-brand-mark"><i class="fa-solid fa-water"></i></span><strong>ISES</strong></div>
<div class="auth-card-heading"><span class="auth-step">ACCOUNT ACCESS</span><h2 id="auth-title">Welcome back</h2><p>Sign in with your registered account to continue.</p></div>
<form method="POST" action="{{ route('signin') }}">@csrf
@if(session('success'))<div class="auth-alert auth-alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>@endif
@if($errors->any())<div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<label for="email">Email address</label><div class="auth-input-wrap"><i class="fa-regular fa-envelope"></i><input id="email" type="email" name="email" value="{{ old('email', session('registered_email')) }}" placeholder="name@organisation.com" autocomplete="email" required></div>
<div class="auth-label-row"><label for="password">Password</label>@if(\Illuminate\Support\Facades\Route::has('password.request'))<a href="{{ route('password.request') }}">Forgot password?</a>@endif</div>
<div class="auth-input-wrap"><i class="fa-solid fa-lock"></i><input id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required><button class="auth-password-toggle" type="button" id="togglePassword" aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div>
<button type="submit" class="auth-submit">Sign in <i class="fa-solid fa-arrow-right"></i></button><div class="auth-switch"><span>New to ISES?</span><a href="{{ route('signup') }}">Create an account</a></div></form>
<p class="auth-legal">Access is limited to authorised users. Keep your account credentials secure.</p></section></main></div><script src="{{ asset('js/signin.js') }}"></script></body></html>