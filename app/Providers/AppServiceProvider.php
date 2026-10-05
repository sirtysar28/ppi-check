<?php

namespace App\Providers;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('manage-masters', fn (User $user) => $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI]));
        Gate::define('manage-users', fn (User $user) => $user->hasRole(User::ROLE_SUPER_ADMIN));
        Gate::define('manage-instruments', fn (User $user) => $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI]));
        Gate::define('view-all-reports', fn (User $user) => $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI]));
        Gate::define('conduct-audit', fn (User $user) => $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI, User::ROLE_AUDITOR]));
        Gate::define('verify-followup', fn (User $user) => $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI, User::ROLE_AUDITOR]));

        Gate::define('view-audit', fn (User $user, Audit $audit) => $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI, User::ROLE_AUDITOR]) || $audit->unit_id === $user->unit_id);
        Gate::define('view-finding', fn (User $user, Finding $finding) => $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI, User::ROLE_AUDITOR]) || $finding->unit_id === $user->unit_id);

        $this->configureMailFromSettings();
    }

    /**
     * Terapkan konfigurasi SMTP dari menu Pengaturan (tabel settings)
     * secara runtime, tanpa perlu mengubah file .env.
     */
    protected function configureMailFromSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        if (! Setting::get('mail_host')) {
            return; // belum dikonfigurasi, pakai default .env
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
