<?php

namespace App\Providers;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
    }
}
