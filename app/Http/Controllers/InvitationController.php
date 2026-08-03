<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invitation;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with('inviter')->latest()->paginate(20);
        return view('invitations.index', compact('invitations'));
    }

    public function create()
    {
        $roles   = Role::all();
        $clients = Client::orderBy('company')->get();
        return view('invitations.create', compact('roles', 'clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email'       => 'required|email',
            'name'        => 'nullable|string|max:255',
            'role_ids'    => 'required|array|min:1',
            'role_ids.*'  => 'exists:roles,name',
            'client_id'   => 'nullable|exists:clients,id',
        ]);

        if (in_array('client', $data['role_ids'], true) && empty($data['client_id'])) {
            return back()->withErrors(['client_id' => 'Select a client for the Client role.'])->withInput();
        }

        // Check if user already exists
        if (User::where('email', $data['email'])->exists()) {
            return back()->withErrors(['email' => 'A user with this email already exists.'])->withInput();
        }

        // Invalidate any previous pending invitations for same email
        Invitation::where('email', $data['email'])->whereNull('accepted_at')->delete();

        $invitation = Invitation::create([
            'email'       => $data['email'],
            'name'        => $data['name'] ?? null,
            'token'       => Str::random(64),
            'role_ids'    => $data['role_ids'],
            'client_id'   => $data['client_id'] ?? null,
            'invited_by'  => Auth::id(),
            'expires_at'  => Carbon::now()->addDays(7),
        ]);

        $link = route('invitations.accept', $invitation->token);

        return redirect()->route('invitations.index')
            ->with('success', 'Invitation created!')
            ->with('invite_link', $link);
    }

    public function accept(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return redirect()->route('login')->with('error', 'This invitation has already been used.');
        }
        if ($invitation->isExpired()) {
            return back()->with('error', 'This invitation has expired.');
        }

        $roles = $invitation->roles();

        return view('invitations.accept', compact('invitation', 'roles'));
    }

    public function register(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted() || $invitation->isExpired()) {
            return redirect()->route('login')->with('error', 'This invitation is no longer valid.');
        }

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $invitation->email,
            'password'  => Hash::make($data['password']),
            'client_id' => $invitation->client_id,
        ]);

        // Assign roles
        $user->syncRoles($invitation->role_ids ?? []);

        // Mark invitation accepted
        $invitation->update(['accepted_at' => Carbon::now()]);

        Auth::login($user);

        $home = match (true) {
            $user->isAdmin()         => route('dashboard'),
            $user->hasRole('client') => route('client.dashboard'),
            default                  => route('tasks.index'),
        };
        return redirect($home)->with('success', 'Welcome to ' . config('app.name') . ', ' . $user->name . '!');
    }

    public function destroy(Invitation $invitation)
    {
        if ($invitation->isAccepted()) {
            return back()->with('error', 'Cannot delete an accepted invitation.');
        }
        $invitation->delete();
        return redirect()->route('invitations.index')->with('success', 'Invitation revoked.');
    }
}
