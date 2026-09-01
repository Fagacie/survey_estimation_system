<x-app-layout containerClass="px-0">
    <style>
        .dashboard-wrapper {
            background-color: #f8fafc;
            min-height: calc(100vh - 64px);
        }

        /* Top Dark Section */
        .dashboard-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 3rem 1.5rem 6rem;
            position: relative;
        }

        .dashboard-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .page-title {
            color: #ffffff;
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }
        .page-subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        /* Main Content Area */
        .content-area {
            padding: 0 1.5rem 5rem;
            margin-top: -4rem;
            position: relative;
            z-index: 10;
        }

        /* Profile Cards */
        .profile-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 4px 10px -5px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 2.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .profile-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            border-color: rgba(226, 232, 240, 1);
        }

        /* Override Laravel Breeze Form Elements inside the partials */
        .profile-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.25rem;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.75rem;
        }
        .profile-card p {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            margin-top: 0.5rem;
        }

        .profile-card label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .profile-card input[type="text"],
        .profile-card input[type="email"],
        .profile-card input[type="password"] {
            width: 100%;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155;
            font-size: 0.95rem;
            transition: all 0.2s;
            margin-bottom: 1.25rem;
        }
        .profile-card input[type="text"]:focus,
        .profile-card input[type="email"]:focus,
        .profile-card input[type="password"]:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .profile-card button[type="submit"],
        .profile-card .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
            transition: all 0.2s;
            cursor: pointer;
            text-transform: none;
            letter-spacing: normal;
        }
        .profile-card button[type="submit"]:hover,
        .profile-card .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
        }

        .profile-card button[type="submit"].bg-red-600 {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }
        .profile-card button[type="submit"].bg-red-600:hover {
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
        }

        .profile-card .text-red-600 {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: -1rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .profile-card header {
            margin-bottom: 1.5rem;
        }

        /* Fix gap between save and success message */
        .profile-card .flex.items-center.gap-4 {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        .profile-card .text-gray-600 {
            color: #10b981;
            font-weight: 600;
        }
    </style>

    <div class="dashboard-wrapper">
        <div class="dashboard-hero">
            <div class="dashboard-container">
                <div>
                    <h1 class="page-title"><i class="fa-solid fa-user-circle me-2 opacity-75"></i> Profile Settings</h1>
                    <div class="page-subtitle">Manage your account information, security, and preferences</div>
                </div>
            </div>
        </div>

        <div class="content-area dashboard-container">
            <div class="profile-card">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="profile-card">
                @include('profile.partials.update-password-form')
            </div>

            <div class="profile-card" style="border-top: 4px solid #ef4444;">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
