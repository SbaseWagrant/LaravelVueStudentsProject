<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\StudentSession;
use Carbon\Carbon;

class SendReminderEmails extends Command
{
    protected $signature = 'emails:send-reminders';
    protected $description = 'Send reminder emails 5 minutes before the session';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $time = Carbon::now()->addMinutes(5);

        $sessions = StudentSession::where('start_time', '>', $time->toDateTimeString())->get();

        foreach ($sessions as $session) {
            $user = $session->user_id;

            Mail::raw("notification", function ($message) use ($user) {
                $message->to('Wagrant1992@gmail.com')
                        ->subject('notification');
            });
        }
    }
}
