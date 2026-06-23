<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ParkingAllocationService;

class CompleteExpiredParkingAllocations extends Command
{
    protected $signature = 'parking:release-expired';
    protected $description = 'Complete expired parking allocations and free slots';

    public function handle(ParkingAllocationService $service)
    {
        $service->completeExpired();
        $this->info('Expired allocations processed.');
    }
}