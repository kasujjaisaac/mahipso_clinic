<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';
    protected $description = 'Send scheduled notifications and reminders to users';

    public function handle()
    {
        $now = Carbon::now();
        $notifications = Notification::whereNull('read_at')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->get();

        foreach ($notifications as $notification) {
            if ($notification->user_id) {
                $user = User::find($notification->user_id);
                if ($user && $user->email) {
                    // Example: send email (customize as needed)
                    Mail::raw($notification->body, function ($message) use ($user, $notification) {
                        $message->to($user->email)
                            ->subject($notification->title);
                    });
                }
            }
            // Mark as read/sent
            $notification->read_at = $now;
            $notification->save();
        }
        $this->info('Scheduled notifications sent.');
    }
}
