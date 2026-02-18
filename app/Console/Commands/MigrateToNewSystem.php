<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Guardian;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Models\PaymentTransaction;
use App\Enums\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MigrateToNewSystem extends Command
{
    protected $signature = 'shiloh:migrate-data';
    protected $description = 'Migrate existing data to new Shiloh system structure';

    public function handle(): int
    {
        $this->info('🔄 Migrating data to new system...');

        DB::transaction(function () {
            // 1. Update student ages
            $this->info('📊 Calculating student ages...');
            $this->updateStudentAges();

            // 2. Create guardian accounts
            $this->info('👨‍👩‍👧 Creating guardian accounts...');
            $this->createGuardianAccounts();

            // 3. Update enrollment package dates
            $this->info('📦 Setting package dates...');
            $this->updateEnrollmentDates();

            // 4. Migrate payment history
            $this->info('💰 Migrating payment transactions...');
            $this->migratePaymentTransactions();
        });

        $this->newLine();
        $this->info('✅ Data migration completed!');
        $this->newLine();
        $this->info('Next steps:');
        $this->line('  1. Create student schedules');
        $this->line('  2. Generate session occurrences');
        $this->line('  3. Test parent portal access');

        return Command::SUCCESS;
    }

    private function updateStudentAges(): void
    {
        $count = 0;
        Student::whereNotNull('birthdate')->each(function ($student) use (&$count) {
            $age = now()->diffInYears($student->birthdate);
            $student->update([
                'age' => $age,
                'requires_monitoring' => $age <= 10,
            ]);
            $count++;
        });
        $this->line("  ✓ Updated {$count} students");
    }

    private function createGuardianAccounts(): void
    {
        $count = 0;
        Student::whereDoesntHave('guardians')->each(function ($student) use (&$count) {
            // Create user account
            $user = User::create([
                'name' => $student->guardian_name ?? "Guardian of {$student->full_name}",
                'email' => "parent{$student->id}@shiloh.test",
                'password' => Hash::make('password'),
                'role' => UserRole::PARENT,
            ]);

            // Create guardian profile
            $names = explode(' ', $student->guardian_name ?? 'Guardian Name');
            $guardian = Guardian::create([
                'user_id' => $user->id,
                'first_name' => $names[0] ?? 'Guardian',
                'last_name' => $student->last_name,
                'middle_name' => $names[1] ?? null,
                'contact_number' => $student->guardian_contact ?? 'N/A',
                'email' => $user->email,
                'address' => $student->address,
                'relationship' => 'Guardian',
            ]);

            // Link to student
            $guardian->students()->attach($student->id, ['is_primary' => true]);
            $count++;
        });
        $this->line("  ✓ Created {$count} guardian accounts");
    }

    private function updateEnrollmentDates(): void
    {
        $count = 0;
        Enrollment::whereNull('package_start_date')->each(function ($enrollment) use (&$count) {
            $enrollment->update([
                'package_start_date' => $enrollment->enrollment_date,
                'package_end_date' => $enrollment->enrollment_date->copy()->addMonths(3),
                'monthly_installments' => 3,
                'is_non_refundable' => true,
            ]);
            $count++;
        });
        $this->line("  ✓ Updated {$count} enrollments");
    }

    private function migratePaymentTransactions(): void
    {
        $count = 0;
        PaymentSchedule::where('status', 'PAID')
            ->whereDoesntHave('transactions')
            ->each(function ($schedule) use (&$count) {
                PaymentTransaction::create([
                    'enrollment_id' => $schedule->enrollment_id,
                    'payment_schedule_id' => $schedule->id,
                    'amount' => $schedule->amount_due,
                    'type' => 'PAYMENT',
                    'transaction_date' => $schedule->paid_at ?? $schedule->updated_at,
                    'payment_method' => $schedule->payment_method ?? 'Cash',
                    'reference_no' => $schedule->receipt_no,
                    'remarks' => $schedule->remarks,
                    'processed_by_user_id' => 1, // Default to admin
                ]);
                $count++;
            });
        $this->line("  ✓ Migrated {$count} payment transactions");
    }
}
