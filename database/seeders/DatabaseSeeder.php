<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\ApdType;
use App\Models\Audit;
use App\Models\AuditAnswer;
use App\Models\AuditCategory;
use App\Models\AuditQuestion;
use App\Models\Finding;
use App\Models\FollowUp;
use App\Models\Profession;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use App\Models\Verification;
use App\Models\WasteType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MasterSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}
