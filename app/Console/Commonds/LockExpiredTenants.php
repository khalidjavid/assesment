<?php

namespace App\Console\Commonds;

use App\Models\Tenant;
use Illuminate\Console\Command;

class LockExpiredTenants extends Command
{
    protected $signature   = 'tenants:lock-expired';
    protected $description = 'Lock tenants whose subscription has expired past the grace period';

    public function handle(): void
    {
        $tenants = Tenant::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('is_locked', false)
            ->whereNotNull('subscription_end_date')
            ->get();

        $locked = 0;

        foreach ($tenants as $tenant) {
            if ($tenant->is_hard_locked) {
                $tenant->lock();
                $locked++;
                $this->info("Locked: {$tenant->name} (subdomain: {$tenant->subdomain})");
            }
        }

        $this->info("Done. {$locked} tenant(s) locked.");
    }
}

// ADD TO app/Console/Kernel.php schedule():
// $schedule->command('tenants:lock-expired')->dailyAt('00:01');
