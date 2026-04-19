<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\HivRecord;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Visit;
use App\Policies\AppointmentPolicy;
use App\Policies\HivRecordPolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\PatientPolicy;
use App\Policies\VisitPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Visit::class, VisitPolicy::class);
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);
        Gate::policy(HivRecord::class, HivRecordPolicy::class);
    }
}
