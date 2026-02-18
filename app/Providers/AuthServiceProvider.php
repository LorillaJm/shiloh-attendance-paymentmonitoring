<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\AttendanceRecord;
use App\Models\Guardian;
use App\Models\StudentSchedule;
use App\Models\SessionOccurrence;
use App\Policies\UserPolicy;
use App\Policies\StudentPolicy;
use App\Policies\PackagePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\AttendanceRecordPolicy;
use App\Policies\GuardianPolicy;
use App\Policies\StudentSchedulePolicy;
use App\Policies\SessionOccurrencePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Student::class => StudentPolicy::class,
        Package::class => PackagePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        AttendanceRecord::class => AttendanceRecordPolicy::class,
        Guardian::class => GuardianPolicy::class,
        StudentSchedule::class => StudentSchedulePolicy::class,
        SessionOccurrence::class => SessionOccurrencePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
