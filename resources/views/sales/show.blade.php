@extends('layouts.app')

@section('title', 'Sale Details')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">Receipt #{{ $receiptData['receipt_number'] }}</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $receiptData['pharmacy_name'] }} • {{ $sale->sale_date->format('M d, Y H:i:s') }}</p>
            </div>
            <a href="{{ route('pharmacies.sales.index', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Sales</a>
        </div>
    </div>

    @if (session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #2f7d57; padding: 12px 16px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Sale Details -->
        <div>
            <div style="background: #fff; border: 1px solid #f0d4d1;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Sale Details</p>
                </div>
                <div style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Product</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;\">{{ $sale->product->name ?? 'Deleted Product' }}</p>
                        </div>
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Status</p>
                            <span style="background: @if($sale->status === 'completed') #d4edda @elseif($sale->status === 'voided') #f8d7da @else #fff3cd @endif; color: @if($sale->status === 'completed') #2f7d57 @elseif($sale->status === 'voided') #b8342b @else #c87b16 @endif; padding: 4px 8px; display: inline-block; font-size: 11px; font-weight: 600;\">{{ ucfirst($sale->status) }}</span>
                        </div>
                    </div>
                    @if($sale->patient || $sale->visit || $sale->provider)
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                            @if($sale->patient)
                                <div>
                                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Patient</p>
                                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $sale->patient->full_name }}</p>
                                </div>
                            @endif
                            @if($sale->visit)
                                <div>
                                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Visit</p>
                                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">#{{ $sale->visit->id }}</p>
                                </div>
                            @endif
                            @if($sale->provider)
                                <div>
                                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Provider</p>
                                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $sale->provider->name }}</p>
                                </div>
                            @endif
                        </div>                    @if($sale->patient || $sale->visit || $sale->provider)
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                            @if($sale->patient)
                                <div>
                                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Patient</p>
                                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $sale->patient->full_name }}</p>
                                </div>
                            @endif
                            @if($sale->visit)
                                <div>
                                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Visit</p>
                                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">#{{ $sale->visit->id }}</p>
                                </div>
                            @endif
                            @if($sale->provider)
                                <div>
                                    <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;">Provider</p>
                                    <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">{{ $sale->provider->name }}</p>
                                </div>
                            @endif
                        </div>
                    @endif                    @endif                    <div style="border-top: 1px solid #f0d4d1; padding-top: 24px; margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Unit Price</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;">USh {{ number_format($sale->product->price, 0) }}</p>
                        </div>
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Quantity</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;\">{{ $sale->quantity }}</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Sold By</p>
                            <p style="color: #222; font-size: 13px; font-weight: 500; margin: 0;\">{{ $sale->soldBy->name ?? 'N/A' }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 6px 0;\">Total Amount</p>
                            <p style="color: #b8342b; font-size: 20px; font-weight: 700; margin: 0;">USh {{ number_format($sale->total_price, 0) }}</p>
                        </div>
                    </div>
                    @if($sale->prescription_note)
                        <div style="background: #f4f5f7; border: 1px solid #e2e8f0; color: #1f2937; padding: 12px; border-radius: 0; margin-bottom: 24px;">
                            <p style="font-weight: 600; margin: 0 0 4px 0;">Prescription note</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px;">{{ $sale->prescription_note }}</p>
                        </div>                    @if($sale->prescription_note)
                        <div style="background: #f4f5f7; border: 1px solid #e2e8f0; color: #1f2937; padding: 12px; border-radius: 0; margin-bottom: 24px;">
                            <p style="font-weight: 600; margin: 0 0 4px 0;">Prescription note</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px;">{{ $sale->prescription_note }}</p>
                        </div>
                    @endif                    @endif                    @if($sale->status !== 'completed')
                        <div style="background: #d1ecf1; border: 1px solid #bee5eb; color: #2f6fed; padding: 12px; border-radius: 0;">
                            <p style="font-weight: 600; margin: 0 0 4px 0;\">{{ ucfirst($sale->status) }} Information</p>
                            <p style="margin: 4px 0 0 0; font-size: 11px;\">{{ $sale->void_reason }}</p>
                            <p style="margin: 4px 0 0 0; font-size: 11px;\">{{ $sale->status === 'voided' ? 'Voided' : 'Refunded' }} by {{ $sale->voidedBy->name ?? 'N/A' }} on {{ $sale->voided_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    @endif

                    <div style="display: flex; gap: 12px; border-top: 1px solid #f0d4d1; padding-top: 16px; margin-top: 24px; flex-wrap: wrap;">
                        <a href="{{ route('pharmacies.sales.printReceipt', [$pharmacy, $sale]) }}" target="_blank" style="background: #2f6fed; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer;\">🖨 Print Receipt</a>
                        
                        @if($sale->status === 'completed')
                            <button onclick="document.getElementById('voidModal').style.display='block'" style="background: #c87b16; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;\">✗ Void Sale</button>
                            <button onclick="document.getElementById('refundModal').style.display='block'" style="background: #2f6fed; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;\">↩ Refund</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div style="background: #fff; border: 1px solid #f0d4d1;">
                <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
                    <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Product Details</p>
                </div>
                <div style="padding: 16px;">
                    @if($sale->product->image_path)
                        <img src="{{ asset('storage/' . $sale->product->image_path) }}" alt="{{ $sale->product->name }}" style="width: 100%; margin-bottom: 16px; border: 1px solid #f0d4d1;">
                    @endif
                    
                    <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f0d4d1;">
                        <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 4px 0;\">Category</p>
                        <p style="color: #222; font-size: 12px; margin: 0;\">{{ $sale->product->category->name ?? 'Uncategorized' }}</p>
                    </div>
                    
                    <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f0d4d1;">
                        <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 4px 0;\">Expiry Date</p>
                        <p style="color: #222; font-size: 12px; margin: 0;\">
                            @if($sale->product->expiry_date)
                                <span style="background: @if($sale->product->is_expired) #f8d7da @else #d1ecf1 @endif; color: @if($sale->product->is_expired) #b8342b @else #2f6fed @endif; padding: 2px 6px; font-size: 11px; font-weight: 600;\">
                                    {{ $sale->product->expiry_date->format('M d, Y') }}
                                </span>
                            @else
                                <span style="color: #888;\">No expiry date</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p style="color: #888; font-size: 11px; text-transform: uppercase; font-weight: 600; margin: 0 0 4px 0;\">Current Stock</p>
                        <span style="background: @if($sale->product->is_low_stock) #f8d7da @else #d4edda @endif; color: @if($sale->product->is_low_stock) #b8342b @else #2f7d57 @endif; padding: 4px 8px; display: inline-block; font-weight: 700;\">{{ $sale->product->quantity }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Void Modal -->
    <div id="voidModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border: 1px solid #f0d4d1; width: 90%; max-width: 600px; max-height: 90vh; overflow: auto;">
            <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa; display: flex; justify-content: space-between; align-items: center;">
                <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Void Sale</p>
                <button onclick="document.getElementById('voidModal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer;">×</button>
            </div>
            <form method="POST" action="{{ route('pharmacies.sales.void', [$pharmacy, $sale]) }}" style="padding: 24px;">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Reason for Voiding <span style="color: #b8342b;">*</span></label>
                    <textarea name="void_reason" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; font-family: inherit;" rows="4" required></textarea>
                </div>
                <div style="display: flex; gap: 12px; border-top: 1px solid #f0d4d1; padding-top: 16px;">
                    <button type="submit" style="background: #b8342b; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;\">Void Sale</button>
                    <button type="button" onclick="document.getElementById('voidModal').style.display='none'" style="background: #f5f5f5; color: #222; padding: 10px 20px; font-size: 12px; font-weight: 600; border: 1px solid #f0d4d1; cursor: pointer;\">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Refund Modal -->
    <div id="refundModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border: 1px solid #f0d4d1; width: 90%; max-width: 600px; max-height: 90vh; overflow: auto;">
            <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa; display: flex; justify-content: space-between; align-items: center;">
                <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Refund Sale</p>
                <button onclick="document.getElementById('refundModal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer;">×</button>
            </div>
            <form method="POST" action="{{ route('pharmacies.sales.refund', [$pharmacy, $sale]) }}" style="padding: 24px;">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Refund Reason <span style="color: #b8342b;">*</span></label>
                    <textarea name="refund_reason" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; font-family: inherit;" rows="4" required></textarea>
                </div>
                <div style="display: flex; gap: 12px; border-top: 1px solid #f0d4d1; padding-top: 16px;">
                    <button type="submit" style="background: #b8342b; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;\">Process Refund</button>
                    <button type="button" onclick="document.getElementById('refundModal').style.display='none'" style="background: #f5f5f5; color: #222; padding: 10px 20px; font-size: 12px; font-weight: 600; border: 1px solid #f0d4d1; cursor: pointer;\">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.onclick = function(event) {
            const voidModal = document.getElementById('voidModal');
            const refundModal = document.getElementById('refundModal');
            if (event.target === voidModal) voidModal.style.display = 'none';
            if (event.target === refundModal) refundModal.style.display = 'none';
        }
    </script>
</div>
@endsection
