<?php
namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InvoiceService
{
    // initialize disk and pdf expiry day
    public function __construct(
        protected Filesystem $disk,
        protected int $downloadLinkExpiry
    ) {}

    // generate view for pdf and store invoice
    public function generate(Payment $payment): Invoice
    {
        $invoiceId = (string) Str::ulid();

        $pdf = Pdf::loadView('payment.invoices.pdf', [
            'payment' => $payment,
            'invoice' => (object) ['id' => $invoiceId],
        ])->setPaper('a5', 'landscape');

        $path = "invoices/invoice_{$invoiceId}.pdf";
        $this->disk->put($path, $pdf->output());

        $invoice = Invoice::create([
            'id'           => $invoiceId,
            'payment_id'   => $payment->id,
            'invoice_path' => $path,
        ]);

        return $invoice;
    }

    // check pdf invoice file exists or not
    public function fileExists(Invoice $invoice): bool
    {
        return $this->disk->exists($invoice->invoice_path);
    }

    // permanent download link for success page
    public function permanentDownloadLink(Invoice $invoice): string
    {
        return route('payments.invoice.download', ['invoice' => $invoice->id]);
    }
    // time limited download link for email
    public function generateSignedLink(Invoice $invoice): string
    {
        return URL::temporarySignedRoute(
            'invoice.download.signed',
            now()->addDays($this->downloadLinkExpiry),
            ['invoice' => $invoice->id]
        );
    }

    // download pdf from storage path
    public function downloadInvoicePdf(Invoice $invoice)
    {
        // force pdf download
        return $this->disk->download(
            $invoice->invoice_path,     // storage path
            "invoice_{$invoice->id}.pdf"// actual pdf name
        );
    }
}
