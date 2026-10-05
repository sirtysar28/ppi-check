<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')
            ->merge(collect(Setting::DEFAULTS)->map(fn ($v) => (string) $v));

        return view('settings.index', compact('settings'));
    }

    /** Tab Umum: profil fasilitas + ambang batas + batas waktu tindak lanjut. */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => ['required', 'string', 'max:150'],
            'facility_address' => ['nullable', 'string', 'max:300'],
            'threshold_sangat_baik' => ['required', 'numeric', 'min:0', 'max:100'],
            'threshold_baik' => ['required', 'numeric', 'min:0', 'max:100'],
            'threshold_cukup' => ['required', 'numeric', 'min:0', 'max:100'],
            'follow_up_deadline_days' => ['required', 'integer', 'min:1', 'max:90'],
        ], [
            'facility_name.required' => 'Nama fasilitas wajib diisi.',
            'threshold_sangat_baik.required' => 'Ambang batas Sangat Baik wajib diisi.',
        ]);

        if (! ($validated['threshold_sangat_baik'] >= $validated['threshold_baik'] && $validated['threshold_baik'] >= $validated['threshold_cukup'])) {
            return back()->withErrors(['threshold_baik' => 'Urutan ambang batas harus: Sangat Baik ≥ Baik ≥ Cukup.']);
        }

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return back()->with('success', 'Pengaturan umum berhasil disimpan.')->withFragment('#umum');
    }

    /** Tab SMTP: konfigurasi pengiriman email. */
    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => ['required', 'in:smtp,sendmail,log'],
            'mail_host' => ['required_unless:mail_mailer,log,sendmail', 'nullable', 'string', 'max:150'],
            'mail_port' => ['required_unless:mail_mailer,log,sendmail', 'nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', 'in:,tls,ssl'],
            'mail_username' => ['nullable', 'string', 'max:150'],
            'mail_password' => ['nullable', 'string', 'max:200'],
            'mail_from_address' => ['required', 'email', 'max:150'],
            'mail_from_name' => ['required', 'string', 'max:100'],
        ], [
            'mail_host.required_unless' => 'SMTP host wajib diisi.',
            'mail_port.required_unless' => 'SMTP port wajib diisi.',
            'mail_from_address.required' => 'Alamat pengirim wajib diisi.',
        ]);

        // Password kosong = pertahankan password yang tersimpan
        if (empty($validated['mail_password'])) {
            $validated['mail_password'] = Setting::get('mail_password');
        }

        foreach ([
            'mail_mailer', 'mail_host', 'mail_port', 'mail_encryption',
            'mail_username', 'mail_password', 'mail_from_address', 'mail_from_name',
        ] as $key) {
            Setting::set($key, (string) ($validated[$key] ?? ''));
        }

        return back()->with('success', 'Konfigurasi SMTP berhasil disimpan.')->withFragment('#smtp');
    }

    /** Kirim email percobaan menggunakan konfigurasi SMTP tersimpan. */
    public function testSmtp(Request $request)
    {
        $validated = $request->validate([
            'test_email_to' => ['required', 'email'],
        ]);

        // Paksa Laravel membaca ulang konfigurasi dari tabel settings
        $this->applyMailConfig();

        try {
            Mail::raw(
                "Ini adalah email percobaan dari aplikasi " . config('app.name') . ".\n\nJika Anda menerima email ini, konfigurasi SMTP sudah benar.",
                function ($message) use ($validated) {
                    $message->to($validated['test_email_to'])
                        ->subject('[' . config('app.name') . '] Tes Koneksi SMTP');
                }
            );

            return back()->with('success', 'Email percobaan berhasil dikirim ke ' . $validated['test_email_to'] . '.')->withFragment('#smtp');
        } catch (\Throwable $e) {
            return back()->withErrors(['smtp_test' => 'Gagal mengirim email: ' . $e->getMessage()])->withFragment('#smtp');
        }
    }

    /** Tab Logo: ganti logo aplikasi. */
    public function updateLogo(Request $request)
    {
        $validated = $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ], [
            'logo.required' => 'Pilih berkas logo terlebih dahulu.',
            'logo.image' => 'Berkas harus berupa gambar (PNG / JPG / WEBP / SVG).',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
        ]);

        $old = Setting::get('app_logo');
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        $path = $request->file('logo')->store('logos', 'public');
        Setting::set('app_logo', $path);

        return back()->with('success', 'Logo aplikasi berhasil diperbarui.')->withFragment('#logo');
    }

    public function resetLogo(Request $request)
    {
        if ($old = Setting::get('app_logo')) {
            Storage::disk('public')->delete($old);
        }

        Setting::set('app_logo', null);

        return back()->with('success', 'Logo dikembalikan ke bawaan aplikasi.')->withFragment('#logo');
    }

    /** Tab Keamanan: ganti password akun yang sedang login. */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'different:current_password', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini salah.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.different' => 'Password baru harus berbeda dengan password lama.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password berhasil diganti.')->withFragment('#password');
    }

    protected function applyMailConfig(): void
    {
        if (! Setting::get('mail_host')) {
            return;
        }

        config([
            'mail.default' => Setting::get('mail_mailer', 'smtp'),
            'mail.mailers.smtp.host' => Setting::get('mail_host'),
            'mail.mailers.smtp.port' => (int) Setting::get('mail_port', 587),
            'mail.mailers.smtp.encryption' => Setting::get('mail_encryption') ?: null,
            'mail.mailers.smtp.username' => Setting::get('mail_username') ?: null,
            'mail.mailers.smtp.password' => Setting::get('mail_password') ?: null,
            'mail.from.address' => Setting::get('mail_from_address') ?: config('mail.from.address'),
            'mail.from.name' => Setting::get('mail_from_name') ?: config('mail.from.name'),
        ]);
    }
}
