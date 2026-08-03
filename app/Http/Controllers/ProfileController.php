<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
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
     * Upload or replace the user's avatar.
     */
    public function uploadAvatar(Request $request): RedirectResponse
    {
        $request->validateWithBag('avatar', [
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $request->user()
            ->addMediaFromRequest('avatar')
            ->toMediaCollection('avatar');

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Remove the user's avatar.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        $request->user()->clearMediaCollection('avatar');
        return Redirect::route('profile.edit')->with('status', 'avatar-removed');
    }

    /**
     * Update the user's notification preferences.
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $preferences = [];
        foreach (NotificationType::staffTypes() as $type) {
            foreach (['database', 'mail'] as $channel) {
                $preferences[$type->value][$channel] = $request->boolean("preferences.{$type->value}.{$channel}");
            }
        }

        $request->user()->update(['notification_preferences' => $preferences]);

        return Redirect::route('profile.edit')->with('status', 'notifications-updated');
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
