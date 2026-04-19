@extends('layouts.app')

@section('title', 'Print Receipt')
@section('content')
<div class="container" style="max-width: 600px; margin-top: 20px;">
    <div style="border: 2px dashed #333; padding: 30px; font-family: monospace; background: white;">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px;">{{ $receiptData['pharmacy_name'] }}</h3>
            <p style="margin: 5px 0; font-size: 12px;">{{ $receiptData['pharmacy_location'] }}</p>
            <p style="margin: 5px 0; font-size: 12px; border-bottom: 2px dashed #333; padding-bottom: 10px;">Receipt #{{ $receiptData['receipt_number'] }}</p>
        </div>

        <!-- Date & Time -->
        <div style="text-align: center; margin-bottom: 15px; font-size: 12px;">
            <p style="margin: 3px 0;">{{ $receiptData['sale_date']->format('M d, Y H:i:s') }}</p>
        </div>

        <!-- Product Details -->
        <div style="margin-bottom: 15px; border-bottom: 2px dashed #333; padding-bottom: 10px;">
            <table style="width: 100%; font-size: 12px;">
                <tr>
                    <td style="text-align: left;"><strong>Product:</strong></td>
                    <td style="text-align: right;">{{ $receiptData['product_name'] }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;"><strong>Unit Price:</strong></td>
                    <td style="text-align: right;">USh {{ number_format($receiptData['product_price'], 0) }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;"><strong>Quantity:</strong></td>
                    <td style="text-align: right;">{{ $receiptData['quantity'] }}</td>
                </tr>
            </table>
        </div>

        <!-- Total -->
        <div style="margin-bottom: 15px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc;">
            <table style="width: 100%; font-size: 14px;">
                <tr style="font-weight: bold;">
                    <td style="text-align: left;">TOTAL AMOUNT:</td>
                    <td style="text-align: right;">USh {{ number_format($receiptData['total_price'], 0) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Info -->
        <div style="margin-bottom: 15px; font-size: 12px; border-bottom: 2px dashed #333; padding-bottom: 10px;">
            <p style="margin: 3px 0;"><strong>Sold By:</strong> {{ $receiptData['sold_by'] }}</p>
            <p style="margin: 3px 0;"><strong>Payment Method:</strong> {{ $receiptData['payment_method'] }}</p>
            <p style="margin: 3px 0;"><strong>Status:</strong> {{ ucfirst($receiptData['status']) }}</p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px; font-size: 11px;">
            <p style="margin: 5px 0;">Thank you for your purchase!</p>
            <p style="margin: 5px 0;">Please keep this receipt for your records.</p>
            <p style="margin: 5px 0; border-top: 2px dashed #333; padding-top: 10px; margin-top: 15px;">
                Printed on {{ now()->format('M d, Y H:i:s') }}
            </p>
        </div>
    </div>

    <!-- Print Button -->
    <div style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary" style="margin-right: 10px;">
            <i class="bi bi-printer"></i> Print Receipt
        </button>
        <a href="{{ route('pharmacies.sales.show', [$sale->pharmacy_id, $sale->id]) }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        .btn {
            display: none;
        }
        .container {
            max-width: 100%;
        }
    }
</style>
@endsection
