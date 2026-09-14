<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecuperarPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');
        $url = "{$frontendUrl}/restablecer-password?token={$this->token}&email=" . urlencode($notifiable->ema_usuario);

        return (new MailMessage)
            ->subject('Recuperación de Contraseña - Clínica')
            ->greeting("¡Hola, {$notifiable->nom_usuario} {$notifiable->ape_usuario}!")
            ->line('Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.')
            ->line('Puedes restablecer tu contraseña haciendo clic en el siguiente enlace:')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace de recuperación es de un solo uso y expirará en 2 horas por motivos de seguridad.')
            ->line('Si tú no solicitaste restablecer tu contraseña, puedes ignorar este correo de forma segura. Tu contraseña actual no cambiará.')
            ->salutation('Atentamente, el equipo de la Clínica');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo'    => 'recuperar_password',
            'mensaje' => 'Se envió el correo para restablecer contraseña.',
        ];
    }
}

