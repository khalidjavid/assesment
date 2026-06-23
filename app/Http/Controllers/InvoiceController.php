<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\InvoiceCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class InvoiceController extends Controller
{
    public function create()
    {
        return view('invoice');
    }

    public function bootstrap(): JsonResponse
    {
        return response()->json([
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'name_ar', 'tax_number']),
            'next_number' => $this->nextNumber(),
            'currency' => 'KWD',
        ]);
    }

    public function store(StoreInvoiceRequest $request, InvoiceCalculator $calculator): JsonResponse
    {
        $data = $request->validated();
        $totals = $calculator->build($data['items']);

        $invoice = DB::transaction(function () use ($data, $totals) {
            $invoice = Invoice::create([
                'customer_id' => $data['customer_id'],
                'number' => $data['number'],
                'issue_date' => $data['issue_date'],
                'currency' => $data['currency'] ?? 'QAR',
                'notes' => $data['notes'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'tax_total' => $totals['tax_total'],
                'grand_total' => $totals['grand_total'],
            ]);

            $invoice->items()->createMany($totals['lines']);

            return $invoice;
        });

        return response()->json([
            'id' => $invoice->id,
            'number' => $invoice->number,
            'grand_total' => $invoice->grand_total,
        ], 201);
    }

    public function pdf(int $id)
    {
       $invoice = Invoice::with(['customer', 'items'])->find($id);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML(view('pdf.invoice', ['invoice' => $invoice])->render());

        return response(
            $mpdf->Output($invoice->number . '.pdf', Destination::STRING_RETURN),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    private function nextNumber(): string
    {
        $year = now()->year;
        $count = Invoice::whereYear('created_at', $year)->count() + 1;

        return sprintf('INV-%d-%04d', $year, $count);
    }
}
