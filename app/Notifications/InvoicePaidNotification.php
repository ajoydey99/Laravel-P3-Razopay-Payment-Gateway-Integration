<?php
namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidNotification extends Notification
{
    //use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Payment $payment,
        public Invoice $invoice
    ) {}

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
        $downloadUrl = app(InvoiceService::class)
            ->generateSignedLink($this->invoice);

        return (new MailMessage)
            ->subject('Your Payment Invoice - Order ' . $this->payment->order_id)
            ->greeting('Payment Successful')
            ->line("Hello {$this->payment->name},")
            ->line('Thank you for your payment of Rs ' . number_format($this->payment->amount, 2))
            ->action('Download Invoice', $downloadUrl)
            ->line('This link expires in ' . config('invoices.link_expiry_days') . ' days')
            ->line('Invoice ID: ' . $this->invoice->id)
            ->line('Order ID: ' . $this->payment->order_id);

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
