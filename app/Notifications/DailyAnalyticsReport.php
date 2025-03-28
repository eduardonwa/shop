<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyAnalyticsReport extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public array $reportData)
    {
        //
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
            ->subject('Reporte diario de analíticas - '.now()->format('d/m/Y'))
            ->line('Resumen de actividades del día:');
        
        // productos más vendidos
        $mail->line('Productos más vendidos');
        foreach ($this->reportData['top_products'] as $product) {
            $mail->line("- {$product['name']}: {$product['sold']} unidades");
        }

        // carritos abandonados
        $mail->line("\nCarritos abandonados: {$this->reportData['abandoned_carts']}");
        
        return $mail;
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
