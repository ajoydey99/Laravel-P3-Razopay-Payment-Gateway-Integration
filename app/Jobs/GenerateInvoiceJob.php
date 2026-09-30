<?php
namespace App\Jobs;

use App\Models\Payment;
use App\Notifications\InvoicePaidNotification;
use App\Services\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 20; // Wait 20 seconds between every retry

    public function __construct(protected Payment $payment)
    {}

    public function handle(InvoiceService $invoice): void
    {
        // Guard for duplicate invoice generation
        if ($this->payment->invoice) {
            Log::info('Invoice already exists for payment, skipping generation', [
                'payment_id' => $this->payment->id,
            ]);
            return;
        }

        // Generate Invoice and save it in local storage
        try {
            $invoiceData = $invoice->generate($this->payment);
        } catch (\Throwable $e) {
            Log::error('Invoice PDF generation failed', [
                'payment_id' => $this->payment->id,
                'error'      => $e->getMessage(),
            ]);

            throw $e; // job retries
        }

        // Send generated Invoice via mail notification
        try {
            Notification::route('mail', $this->payment->email)
                ->notify(new InvoicePaidNotification($this->payment, $invoiceData));

            $invoiceData->markAsMailed();
        } catch (\Throwable $e) {
            Log::error('Invoice email notification failed', [
                'payment_id' => $this->payment->id,
                'invoice_id' => $invoiceData->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('GenerateInvoiceJob failed after all retries', [
            'payment_id' => $this->payment->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
