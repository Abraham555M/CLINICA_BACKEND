<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorBienvenidaNotification extends Notification
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
        $url = "{$frontendUrl}/activar-cuenta?token={$this->token}&email=" . urlencode($notifiable->ema_usuario);

        return (new MailMessage)
            ->subject('Bienvenido a la Clínica - Activa tu cuenta de Doctor')
            ->greeting("¡Hola, Dr(a). {$notifiable->nom_usuario} {$notifiable->ape_usuario}!")
            ->line('Has sido registrado exitosamente como especialista en nuestra plataforma médica.')
            ->line('Para completar la configuración de tu cuenta y proteger la confidencialidad de tus pacientes, por favor establece tu contraseña personal haciendo clic en el siguiente enlace:')
            ->action('Activar Cuenta y Crear Contraseña', $url)
            ->line('Este enlace es de un solo uso y expirará en 24 horas por motivos de seguridad.')
            ->line('Si tú no solicitaste esta cuenta, puedes ignorar este mensaje.')
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
            'tipo'    => 'bienvenida_doctor',
            'mensaje' => 'Se envió la invitación de activación de cuenta.',
        ];
    }
}
