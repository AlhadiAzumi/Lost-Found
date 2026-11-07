<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Mail\CampusAdminInvitation;
use App\Models\Campus;
use App\Models\CampusApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class CampusManagementController extends Controller
{
    public function index(): View
    {
        return view('platform.campuses.index', [
            'campuses' => Campus::query()->orderBy('name')->get(),
            'pendingApplications' => CampusApplication::query()
                ->where('status', 'pending')
                ->oldest()
                ->get(),
            'awaitingAdminSetup' => CampusApplication::query()
                ->where('status', 'approved')
                ->whereNull('admin_created_at')
                ->latest()
                ->get(),
        ]);
    }

    public function approve(CampusApplication $application): RedirectResponse
    {
        [$application, $token] = DB::transaction(function () use ($application): array {
            $application = CampusApplication::query()->lockForUpdate()->findOrFail($application->id);

            if ($application->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Pengajuan ini sudah pernah ditinjau.',
                ]);
            }

            $emailExists = User::withoutGlobalScopes()
                ->whereRaw('LOWER(email) = ?', [Str::lower($application->email)])
                ->exists();

            if ($emailExists) {
                throw ValidationException::withMessages([
                    'application' => 'Email pemohon sudah digunakan akun lain. Minta pemohon mengajukan dengan email yang belum terdaftar.',
                ]);
            }

            $baseSlug = $application->slug;
            $slug = $baseSlug;
            $suffix = 2;

            while (Campus::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$suffix++;
            }

            $campus = Campus::create([
                'name' => $application->name,
                'slug' => $slug,
                'is_active' => true,
            ]);
            $token = Str::random(64);

            $application->update([
                'slug' => $slug,
                'status' => 'approved',
                'campus_id' => $campus->id,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'invitation_token_hash' => Hash::make($token),
                'invitation_expires_at' => now()->addDays(7),
            ]);

            return [$application->fresh(), $token];
        });

        $emailSent = $this->sendInvitation($application, $token);

        $redirect = redirect()->route('platform.campuses.index', ['campus' => null]);

        if (! $emailSent) {
            return $redirect->with('error', 'Pengajuan disetujui dan portal sudah aktif, tetapi email undangan gagal dikirim. Coba kirim ulang tautan setelah layanan email tersedia.');
        }

        return $redirect->with('success', $this->invitationMessage('Pengajuan disetujui. Portal aktif dan tautan pembuatan akun admin telah dikirim ke email pemohon.'));
    }

    public function reject(CampusApplication $application): RedirectResponse
    {
        DB::transaction(function () use ($application): void {
            $application = CampusApplication::query()->lockForUpdate()->findOrFail($application->id);

            if ($application->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Pengajuan ini sudah pernah ditinjau.',
                ]);
            }

            $application->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('platform.campuses.index', ['campus' => null])
            ->with('success', 'Pengajuan ditolak.');
    }

    public function resendInvitation(CampusApplication $application): RedirectResponse
    {
        $token = Str::random(64);
        $application = DB::transaction(function () use ($application, $token): CampusApplication {
            $application = CampusApplication::query()->lockForUpdate()->findOrFail($application->id);

            if ($application->status !== 'approved' || $application->admin_created_at !== null) {
                throw ValidationException::withMessages([
                    'application' => 'Undangan admin tidak tersedia untuk pengajuan ini.',
                ]);
            }

            $application->update([
                'invitation_token_hash' => Hash::make($token),
                'invitation_expires_at' => now()->addDays(7),
            ]);

            return $application;
        });

        $emailSent = $this->sendInvitation($application, $token);

        $redirect = redirect()->route('platform.campuses.index', ['campus' => null]);

        if (! $emailSent) {
            return $redirect->with('error', 'Email undangan gagal dikirim. Tautan baru tersimpan; coba kirim ulang setelah layanan email tersedia.');
        }

        return $redirect->with('success', $this->invitationMessage('Tautan pembuatan akun admin dikirim ulang.'));
    }

    private function sendInvitation(CampusApplication $application, string $token): bool
    {
        try {
            Mail::to($application->email)->send(new CampusAdminInvitation($application, $token));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return false;
        }

        return true;
    }

    private function invitationMessage(string $sentMessage): string
    {
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return $sentMessage.' Namun MAIL_MAILER saat ini hanya menyimpan email di log; atur mailer SMTP agar pemohon menerima undangan.';
        }

        return $sentMessage;
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('campuses', 'slug'),
            ],
        ], [
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Alamat portal ini sudah digunakan.',
        ]);

        Campus::create([
            'name' => $validated['name'],
            'slug' => Str::lower($validated['slug']),
            'is_active' => true,
        ]);

        return redirect()->route('platform.campuses.index', ['campus' => null])
            ->with('success', 'Portal kampus berhasil dibuat.');
    }

    public function updateStatus(Request $request, Campus $managedCampus): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');

        if ($managedCampus->slug === config('campus.default_slug') && ! $isActive) {
            return back()->with('error', 'Portal kampus utama tidak dapat dinonaktifkan.');
        }

        $managedCampus->update(['is_active' => $isActive]);

        return redirect()->route('platform.campuses.index', ['campus' => null])
            ->with('success', $isActive ? 'Portal kampus diaktifkan.' : 'Portal kampus dinonaktifkan.');
    }
}
