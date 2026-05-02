<?php

namespace App\Modules\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    protected int $expire;

    public function __construct(
        protected string $url,
        protected string $name
    ) {
        $this->expire = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');

        return (new MailMessage)
            ->subject(__('auth.reset_password_mail.subject', ['app' => $appName]))
            ->greeting(__('auth.reset_password_mail.greeting', ['name' => $this->name]))
            ->line(__('auth.reset_password_mail.line_request'))
            ->action(__('auth.reset_password_mail.action'), $this->url)
            ->line(__('auth.reset_password_mail.line_expire', ['count' => $this->expire]))
            ->line(__('auth.reset_password_mail.line_no_action'))
            ->salutation(__('auth.reset_password_mail.salutation', ['app' => $appName]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'url' => $this->url,
            'name' => $this->name,
            'expire' => $this->expire,
        ];
    }
}
