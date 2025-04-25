<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginReminderMail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::call(function () {
//     $oneMonthAgo = Carbon::now()->subMonth();

//     // Get users who haven't logged in for a month
//     $users = User::where('last_activity', '<', $oneMonthAgo)
//         ->orWhereNull('last_activity')
//         ->get();

//     // Queue an email for each user
//     foreach ($users as $user) {
//         Mail::to($user->email)->queue(new LoginReminderMail($user));
//     }

//     // Log success message in console
//     \Illuminate\Support\Facades\Log::info('Login reminder emails queued successfully.');
// })->daily()->description('Send login reminder to users who haven’t logged in for a month');



// Schedule::call(function () {
//     $oneMonthAgo = now()->subMonth();

//     $users = User::where(function ($query) use ($oneMonthAgo) {
//         $query->where('last_activity', '<', $oneMonthAgo)
//             ->orWhereNull('last_activity');
//     })
//         ->whereHas('roles', function ($q) {
//             $q->where('name', 'client'); // Only send to clients
//         })
//         ->get();

//     foreach ($users as $user) {
//         Mail::to($user->email)->send(new LoginReminderMail($user));
//     }

//     \Illuminate\Support\Facades\Log::info("Sent login reminders to {$users->count()} users");
// })->dailyAt('9:00')->description('Send login reminder to inactive clients');