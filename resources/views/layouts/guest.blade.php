<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ISES') }} - Login</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>
    <body class="bg-light d-flex align-items-center" style="min-height: 100vh; margin: 0; font-family: 'Inter', sans-serif;">
        <div class="container-fluid p-0">
            <div class="row g-0" style="min-height: 100vh;">
                <!-- Left Side: Branding / Visual -->
                <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); position: relative; overflow: hidden;">
                    <!-- Decorative background elements -->
                    <div style="position: absolute; top: -10%; left: -10%; width: 50%; height: 50%; background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(0,0,0,0) 70%); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -10%; right: -10%; width: 60%; height: 60%; background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, rgba(0,0,0,0) 70%); border-radius: 50%;"></div>
                    
                    <div style="position: relative; z-index: 1;">
                        <div class="mb-5">
                            <x-application-logo style="height: 50px; width: auto; color: #ffffff;" />
                        </div>
                        <div style="margin-top: 20vh;">
                            <h1 class="fw-bold text-white display-5" style="letter-spacing: -0.02em; line-height: 1.2;">Advanced<br>Survey Estimation<br><span style="color: #38bdf8;">Simplified.</span></h1>
                            <p class="text-white opacity-75 mt-4 fs-5" style="max-width: 80%;">
                                Streamline your hydrographic project planning, cost calculation, and client management in one unified platform.
                            </p>
                        </div>
                    </div>
                    
                    <div style="position: relative; z-index: 1;">
                        <p class="text-white opacity-50 small mb-0">&copy; {{ date('Y') }} ECO HYDROTECH SOLUTIONS. All rights reserved.</p>
                    </div>
                </div>

                <!-- Right Side: Form -->
                <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center p-4 p-sm-5" style="background-color: #f8fafc;">
                    <div style="width: 100%; max-width: 420px;">
                        
                        <!-- Mobile Logo (Hidden on desktop) -->
                        <div class="d-lg-none text-center mb-5">
                            <x-application-logo style="height: 45px; width: auto; color: #0f172a;" />
                        </div>

                        <div class="mb-4">
                            <h2 class="fw-bold" style="color: #0f172a; font-size: 1.75rem; letter-spacing: -0.02em;">Welcome back</h2>
                            <p style="color: #64748b;">Please enter your details to sign in.</p>
                        </div>

                        <!-- Card for the form -->
                        <div class="card shadow-sm border-0" style="border-radius: 16px; background: #ffffff;">
                            <div class="card-body p-4 p-sm-5">
                                {{ $slot }}
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
