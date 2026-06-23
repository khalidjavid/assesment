<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice)
    {
        return Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice
        ]);
    }

    public function stream(Invoice $invoice)
    {
        return $this->generate($invoice)
        ->stream("invoice-{$invoice->invoice_number}.pdf");
    }

    public function download(Invoice $invoice)
    {
        return $this->generate($invoice)
        ->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function save(Invoice $invoice): string
    {
        $path = "invoices/invoice-{$invoice->id}.pdf";

        $this->generate($invoice)
        ->save(storage_path("app/public/{$path}"));

        return $path;
    }
}