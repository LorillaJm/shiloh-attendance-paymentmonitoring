<?php

namespace App\Console\Commands;

use App\Models\PaymentSchedule;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders';
    protected $description = 'Send email reminders for due and overdue payments';

    public function handle(): int
    {
        $this->info('Sending payment reminders...');

        // Get overdue payments
        $overdueSchedules = PaymentSchedule::overdue()->with('enrollment.student.guardians.user')->get();
        
        $overdueCount = 0;
        foreach ($overdueSchedules as $schedule) {
            $guardians = $schedule->enrollment->student->guardians;
            foreach ($guardians as $guardian) {
                if ($guardian->user && $guardian->user->email) {
                    $guardian->user->notify(new PaymentReminderNotification($schedule, 'overdue'));
                    $overdueCount++;
                }
            }
        }

        // Get payments due in next 7 days
        $dueSchedules = PaymentSchedule::where('status', 'UNPAID')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->with('enrollment.student.guardians.user')
            ->get();

        $dueCount = 0;
        foreach ($dueSchedules as $schedule) {
            $guardians = $schedule->enrollment->student->guardians;
            foreach ($guardians as $guardian) {
                if ($guardian->user && $guardian->user->email) {
                    $guardian->user->notify(new PaymentReminderNotification($schedule, 'due'));
                    $dueCount++;
                }
            }
        }

        $this->info("Sent {$overdueCount} overdue reminders and {$dueCount} due reminders.");
        
        return Command::SUCCESS;
    }
}
