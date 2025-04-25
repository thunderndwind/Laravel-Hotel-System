<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginReminderMail;
use Carbon\Carbon;

class SendInactiveUsersReminder extends Command
{
    // This is the command you'll run manually or via cron
    protected $signature = 'users:remind-inactive';

    protected $description = 'Send email reminders to users who haven’t logged in for a month';

    public function handle(): void
    {
        $oneMonthAgo = Carbon::now()->subMonth();

        $users = User::where('last_activity', '<', $oneMonthAgo)
            ->orWhereNull('last_activity')
            ->get();

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new LoginReminderMail($user));
        }

        $this->info('Reminder emails queued successfully.');
    }
}