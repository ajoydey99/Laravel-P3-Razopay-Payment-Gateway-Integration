<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $service
    ) {}

    public function download(Invoice $invoice)
    {
        if (! $this->service->fileExists($invoice)) {
            abort(404, 'Invoice not found');
        }

        return $this->service->downloadInvoicePdf($invoice);
    }

    public function downloadSigned(Invoice $invoice)
    {
        return $this->download($invoice);
    }
}
