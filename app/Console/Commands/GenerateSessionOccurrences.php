<?php

namespace App\Console\Commands;

use App\Services\SessionOccurrenceGenerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSessionOccurrences extends Command
{
    protected $signature = 'sessions:generate {--days=30 : Number of days to generate}';
    protected $description = 'Generate session occurrences from active schedules';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays($days);

        $this->info("Generating sessions from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}...");

        $count = SessionOccurrenceGenerator::generateForAllSchedules($startDate, $endDate);

        $this->info("Generated {$count} session occurrences.");

        return Command::SUCCESS;
    }
}
