<?php

namespace App\Console\Commands;

use App\Services\RecurringExpenseService;
use Illuminate\Console\Command;

class GenerateRecurringExpenses extends Command
{
    protected $signature = 'expenses:generate-recurring';
    protected $description = 'Generate recurring expense entries for active trips';

    public function handle(): int
    {
        $count = RecurringExpenseService::generateForActiveTrips();

        $this->info("Generated {$count} recurring expense(s).");

        return self::SUCCESS;
    }
}
