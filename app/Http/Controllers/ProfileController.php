<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\ViewErrorBag;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProfileController extends Controller
{
    /** Render the migrated React profile settings screen. */
    public function editReact(Request $request): InertiaResponse
    {
        $user = $request->user();
        $oldInput = $request->session()->get('_old_input', []);
        $errorBags = $request->session()->get('errors');
        $messagesFor = fn (string $bag): array => $errorBags instanceof ViewErrorBag
            ? $errorBags->getBag($bag)->getMessages()
            : [];

        return Inertia::render('ProfileSettings', [
            'user' => [
                'id' => $user->getAuthIdentifier(),
                'name' => $user->name,
                'email' => $user->email,
                'createdAt' => $user->created_at?->format('d M Y'),
                'emailVerified' => $user->hasVerifiedEmail(),
            ],
            'csrfToken' => csrf_token(),
            'oldInput' => [
                'name' => $oldInput['name'] ?? null,
                'email' => $oldInput['email'] ?? null,
            ],
            'status' => $request->session()->get('status'),
            'updatePasswordErrors' => $messagesFor('updatePassword'),
            'userDeletionErrors' => $messagesFor('userDeletion'),
            'today' => now()->format('d M Y'),
        ]);
    }

    /** Keep the existing Blade profile settings available during migration. */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
