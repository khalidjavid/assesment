<?php

namespace App\Services;

class InvoiceCalculator
{
    public function build(array $items): array
    {
        $lines = [];
        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $discountRate = (float) ($item['discount'] ?? 0);
            $taxRate = (float) ($item['tax_rate'] ?? 0);

            $gross = $quantity * $unitPrice;
            $discountAmount = $gross * ($discountRate / 100);
            $taxable = $gross - $discountAmount;
            $taxAmount = $taxable * ($taxRate / 100);

            $subtotal += $gross;
            $discountTotal += $discountAmount;
            $taxTotal += $taxAmount;

            $lines[] = [
                'description' => $item['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discountRate,
                'tax_rate' => $taxRate,
                'line_total' => round($taxable + $taxAmount, 2),
            ];
        }

        return [
            'lines' => $lines,
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($subtotal - $discountTotal + $taxTotal, 2),
        ];
    }
}
