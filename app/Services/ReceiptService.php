<?php

namespace App\Services;

use App\Models\Sale;

class ReceiptService
{
    public function generateReceiptData(Sale $sale)
    {
        return [
            'receipt_number' => 'RCP-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT),
            'sale_id' => $sale->id,
            'pharmacy_name' => $sale->pharmacy->branch->name ?? 'Pharmacy',
            'pharmacy_location' => $sale->pharmacy->branch->location ?? '',
            'sale_date' => $sale->sale_date,
            'product_name' => $sale->product->name,
            'product_price' => $sale->product->price,
            'quantity' => $sale->quantity,
            'total_price' => $sale->total_price,
            'sold_by' => $sale->soldBy->name ?? 'N/A',
            'payment_method' => 'Cash', // Can be extended
            'status' => $sale->status,
        ];
    }

    public function generateReceiptHTML(Sale $sale)
    {
        $data = $this->generateReceiptData($sale);

        return view('receipts.html', $data)->render();
    }

    public function generateReceiptPDF(Sale $sale)
    {
        // Requires PDF library like dompdf
        // Example implementation
        $html = $this->generateReceiptHTML($sale);
        // PDF generation logic here
        return $html;
    }

    public function formatCurrency($amount)
    {
        return number_format($amount, 2);
    }
}
