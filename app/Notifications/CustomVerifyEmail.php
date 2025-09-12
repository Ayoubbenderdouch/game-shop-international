<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    /**
     * The mailer to use for this notification.
     *
     * @var string|null
     */
    protected $mailer;

    /**
     * Create a new notification instance.
     *
     * @param string|null $mailer
     */
    public function __construct($mailer = null)
    {
        $this->mailer = $mailer ?? 'brevo';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        Log::info('Generating verification email', [
            'user_id' => $notifiable->id,
            'email' => $notifiable->email,
            'url' => $verificationUrl,
            'app_url' => config('app.url'),
            'mailer' => $this->mailer
        ]);

        return (new MailMessage)
            ->mailer($this->mailer)
            ->subject('Verify Your Email Address - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Welcome to ' . config('app.name') . '! We\'re excited to have you on board.')
            ->line('Please click the button below to verify your email address and activate your account.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        // Force the URL to use the correct domain from APP_URL
        $appUrl = rtrim(config('app.url'), '/');

        // Generate the signed URL
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            false // Generate relative URL
        );

        // If URL is relative, prepend the app URL
        if (!str_starts_with($url, 'http')) {
            $url = $appUrl . $url;
        }

        Log::info('Generated verification URL', [
            'user_id' => $notifiable->id,
            'generated_url' => $url,
            'app_url' => $appUrl
        ]);

        return $url;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'email_verification',
            'sent_at' => now(),
        ];
    }
}
