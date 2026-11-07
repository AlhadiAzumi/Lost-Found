<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\CampusApplication;
use App\Models\User;
use App\Support\CampusContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampusApplicationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ], [
            'name.required' => 'Nama kampus wajib diisi.',
            'email.required' => 'Email pemohon wajib diisi.',
            'email.email' => 'Format email pemohon tidak valid.',
        ]);

        $email = Str::lower(trim($validated['email']));

        if (User::withoutGlobalScopes()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah terhubung ke akun kampus lain. Gunakan email yang belum terdaftar untuk admin portal baru.',
            ]);
        }

        if (CampusApplication::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('status', 'pending')
            ->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah memiliki pengajuan yang menunggu persetujuan.',
            ]);
        }

        $baseSlug = Str::substr(Str::slug(trim($validated['name'])) ?: 'kampus', 0, 70);
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Campus::query()->where('slug', $slug)->exists()
            || CampusApplication::query()->where('slug', $slug)->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        CampusApplication::create([
            'name' => trim($validated['name']),
            'email' => $email,
            'slug' => $slug,
        ]);

        return redirect()->route('campuses.index')
            ->with('application_submitted', 'Pengajuan berhasil dikirim. Admin platform akan meninjaunya; jika disetujui, tautan pembuatan akun admin akan dikirim ke email tersebut.');
    }

    public function setup(CampusApplication $application, string $token): View|RedirectResponse
    {
        if (! $this->invitationIsValid($application, $token)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Tautan pengaturan admin tidak valid atau sudah kedaluwarsa.');
        }

        return view('campuses.setup-admin', compact('application', 'token'));
    }

    public function completeSetup(Request $request, CampusApplication $application, string $token): RedirectResponse
    {
        if (! $this->invitationIsValid($application, $token)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Tautan pengaturan admin tidak valid atau sudah kedaluwarsa.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($application, $token, $validated): User {
            $application = CampusApplication::query()->lockForUpdate()->findOrFail($application->id);

            abort_unless($this->invitationIsValid($application, $token), 410);

            $emailExists = User::withoutGlobalScopes()
                ->whereRaw('LOWER(email) = ?', [Str::lower($application->email)])
                ->exists();

            if ($emailExists) {
                abort(409, 'Email pemohon sudah digunakan oleh akun lain.');
            }

            $campus = Campus::query()->findOrFail($application->campus_id);
            app(CampusContext::class)->set($campus);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $application->email,
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
            ]);

            $application->update([
                'admin_created_at' => now(),
                'invitation_token_hash' => null,
                'invitation_expires_at' => null,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        $campus = Campus::query()->findOrFail($user->campus_id);

        return redirect()->route('home', ['campus' => $campus->slug])
            ->with('success', 'Akun admin kampus berhasil dibuat.');
    }

    private function invitationIsValid(CampusApplication $application, string $token): bool
    {
        return $application->status === 'approved'
            && $application->campus_id !== null
            && $application->admin_created_at === null
            && $application->invitation_expires_at?->isFuture()
            && $application->invitation_token_hash !== null
            && Hash::check($token, $application->invitation_token_hash);
    }
}
