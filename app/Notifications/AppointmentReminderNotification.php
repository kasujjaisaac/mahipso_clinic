<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    public $appointment;
    public $reminderType;

    public function __construct(Appointment $appointment, string $reminderType = '24h')
    {
        $this->appointment = $appointment;
        $this->reminderType = $reminderType;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $when = $this->reminderType === '1h'
            ? 'in one hour'
            : 'tomorrow';

        return (new MailMessage)
            ->subject('Appointment Reminder')
            ->greeting('Hello ' . ($notifiable->full_name ?? 'Patient') . ',')
            ->line('This is a reminder for your upcoming appointment at Mahipso Clinic ' . $when . '.')
            ->line('Date & Time: ' . $this->appointment->scheduled_at->format('Y-m-d H:i'))
            ->line('Doctor: ' . optional($this->appointment->doctor)->name)
            ->line('Service: ' . ($this->appointment->service_type ?: 'General'))
            ->action('View Appointment', url('/appointments/' . $this->appointment->id))
            ->line('If you have any questions or need to reschedule, please contact us.');
    }
}
