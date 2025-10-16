<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $name;
    protected $email;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($name,$email,$password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New ViverClinic Account'))
            ->greeting('¡' . __('Hello') . ' ' . $this->name . '!')
            ->line(__('You already have a ViverClinic account'))
            ->line(__('These are your login credentials'.':'))
            ->line(__('email'.': '.$this->email))
            ->line(__('password'.': '.$this->password))
            ->action(__('Access my account'), url('/login'))
            ->line(__('Thank you for being part of ViverClinic').'!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
