<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
class MailResetPassword extends Notification
{
    use Queueable;
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The usernme.
     *
     * @var string
     */
    public $username;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token,$username)
    {
        $this->token = $token;
        $this->username = $username;
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
        return (new MailMessage)
        ->from('recovery@medicalarm.com', 'MedicAlarm')
        ->greeting("Hola {$this->username}!")
        ->subject(Lang::get('Recuperación de contraseña MedicAlarm'))
        ->line(Lang::get('Recibimos una solicitud para cambiar la contraseña de tu cuenta MedicAlarm'))
        ->action(Lang::get('Cambiar ahora'), url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false)))
        ->line(Lang::get('El enlace para cambiar la contraseña solo estará disponible por :count minutos.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
        ->line(Lang::get('Si no solicitaste el cambio de contraseña, ignora este mensaje.'));
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
            //
        ];
    }
}
