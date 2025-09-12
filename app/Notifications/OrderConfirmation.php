<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
        $orderUrl = url('/orders/' . $this->order->id);

        $message = (new MailMessage)
            ->subject('Order Confirmation #' . $this->order->order_number . ' - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for your order! We\'re excited to confirm that we\'ve received your order and it\'s being processed.')
            ->line('**Order Details:**')
            ->line('Order Number: #' . $this->order->order_number)
            ->line('Order Date: ' . $this->order->created_at->format('F j, Y g:i A'))
            ->line('Total Amount: $' . number_format($this->order->total_amount, 2));

        // Add order items
        $message->line('**Items Ordered:**');
        foreach ($this->order->orderItems as $item) {
            $message->line('• ' . $item->product_name . ' (Qty: ' . $item->quantity . ') - $' . number_format($item->total_price, 2));
        }

        $message->line('Payment Status: ' . ucfirst($this->order->payment_status))
            ->line('Delivery Status: ' . ucfirst($this->order->status));

        if ($this->order->status === 'completed') {
            $message->line('**Your digital codes have been delivered!**')
                ->line('You can view them in your order details.');
        }

        $message->action('View Order Details', $orderUrl)
            ->line('If you have any questions about your order, please don\'t hesitate to contact our support team.')
            ->line('Thank you for choosing ' . config('app.name') . '!')
            ->salutation('Best regards, ' . config('app.name') . ' Team');

        return $message;
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
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
        ];
    }
}
