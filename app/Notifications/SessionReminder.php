<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class SessionReminder extends Notification
{
    use Queueable;

    protected $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $sessionDate = Carbon::parse($this->session->date)->format('l, F j, Y');
        $sessionTime = Carbon::parse($this->session->start_time)->format('h:i A') . ' - ' . Carbon::parse($this->session->end_time)->format('h:i A');

        return (new MailMessage)
            ->line('You have a scheduled session.')
            ->line("Student: {$this->session->student->first_name} {$this->session->student->last_name}")
            ->line("Date: {$sessionDate}")
            ->line("Time: {$sessionTime}")
            ->line('Please be prepared for the session.')
            ->action('View Session', url('/student-sessions/' . $this->session->id))
            ->line('Thank you for using our application!');
    }
}