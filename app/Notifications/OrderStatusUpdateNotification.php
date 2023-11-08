<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

     public $order;

     public $user;
    public function __construct($order, $user)
    {
        $this->order = $order;
        $this->user = $user;
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
                    ->success()
                    ->subject('Order #' .$this->order->order_id .' Status Update')
                    ->line('We hope this message finds you well. We wanted to update you on the status of your recent order '. $this->order->order_id. ', placed on ' .$this->order->date. 'Your satisfaction is our top priority, and we are committed to keeping you informed about your order progress.' )
                    ->line('Order Status: '. $this->order->status)
                    ->line('Estimated Delivery Date: '. 'Order usually takes less than 4days')
                    ->line('If you have any questions or require further assistance, please do not hesitate to reach out to our customer support team at 05446355353 or lara-play@gmail.com. We are here to help and ensure your order experience is as smooth as possible.')
                    ->line('You can log into your account to also check the status of your order. Than you for choosing larave-play. We appreciate your business and look forward to serving you in the future.');
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
