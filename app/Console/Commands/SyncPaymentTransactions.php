<?php

namespace App\Console\Commands;

use App\Models\PaymentSchedule;
use App\Models\PaymentTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPaymentTransactions extends Command
{
    protected $signature = 'payments:sync-transactions';
    protected $description = 'Create PaymentTransaction records for paid PaymentSchedules that are missing transactions';

    public function handle()
    {
        $this->info('Syncing payment transactions from payment schedules...');

        // Find all PAID payment schedules that don't have a corresponding transaction
        $paidSchedules = PaymentSchedule::where('status', 'PAID')
            ->whereNotNull('paid_at')
            ->whereDoesntHave('transactions', function ($query) {
                $query->where('type', 'PAYMENT');
            })
            ->with('enrollment')
            ->get();

        $this->info("Found {$paidSchedules->count()} paid schedules without transactions.");

        if ($paidSchedules->isEmpty()) {
            $this->info('All payment schedules are already synced!');
            return 0;
        }

        $created = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($paidSchedules as $schedule) {
                try {
                    PaymentTransaction::create([
                        'enrollment_id' => $schedule->enrollment_id,
                        'payment_schedule_id' => $schedule->id,
                        'transaction_date' => $schedule->paid_at,
                        'type' => 'PAYMENT',
                        'amount' => $schedule->amount_due,
                        'payment_method' => $schedule->payment_method ?? 'CASH',
                        'reference_no' => $schedule->receipt_no,
                        'remarks' => $schedule->remarks ?? 'Synced from payment schedule',
                        'processed_by_user_id' => 1, // Default to admin user
                    ]);

                    $created++;
                    $this->line("  Created transaction for Schedule #{$schedule->id} (Enrollment #{$schedule->enrollment_id}, Installment #{$schedule->installment_no})");
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("  Failed for Schedule #{$schedule->id}: {$e->getMessage()}");
                }
            }

            DB::commit();
            $this->info("Successfully created {$created} payment transactions.");
            if ($errors > 0) {
                $this->warn("{$errors} errors occurred.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Transaction failed: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
