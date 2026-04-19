<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentReminderNotification;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminders for upcoming appointments';

    public function handle()
    {
        $now = Carbon::now();
        $oneHourWindowStart = $now->copy()->addHour();
        $oneHourWindowEnd = $oneHourWindowStart->copy()->addHour();
        $twentyFourHourWindowStart = $now->copy()->addDay();
        $twentyFourHourWindowEnd = $twentyFourHourWindowStart->copy()->addHour();

        $appointments = Appointment::with('patient')
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where(function ($query) use ($oneHourWindowStart, $oneHourWindowEnd, $twentyFourHourWindowStart, $twentyFourHourWindowEnd) {
                $query->whereBetween('scheduled_at', [$oneHourWindowStart, $oneHourWindowEnd])
                    ->orWhereBetween('scheduled_at', [$twentyFourHourWindowStart, $twentyFourHourWindowEnd]);
            })
            ->get();

        $sent = 0;

        foreach ($appointments as $appointment) {
            if (! $appointment->patient || ! $appointment->patient->email) {
                continue;
            }

            $reminderType = $appointment->scheduled_at->between($oneHourWindowStart, $oneHourWindowEnd)
                ? '1h'
                : '24h';

            Notification::route('mail', $appointment->patient->email)
                ->notify(new AppointmentReminderNotification($appointment, $reminderType));

            $sent++;
        }

        $this->info('Reminders sent for ' . $sent . ' appointments.');
    }
}
