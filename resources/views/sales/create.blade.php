@extends('layouts.app')

@section('title', 'Register Sale')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">+ Register Sale</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Create new transaction</p>
            </div>
            <a href="{{ route('pharmacies.sales.index', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Sales</a>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #f0d4d1; max-width: 800px; margin: 0 auto;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Sale Information</p>
        </div>
        <div style="padding: 24px;">
            @if ($errors->any())
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 16px; margin-bottom: 16px; border-radius: 0;">
                    <strong>Error:</strong>
                    <ul style="margin: 4px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pharmacies.sales.store', $pharmacy) }}">
                @csrf
                @if(isset($patient))
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                @elseif(request('patient_id'))
                    <input type="hidden" name="patient_id" value="{{ request('patient_id') }}">
                @endif
                @if(isset($visit))
                    <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                @elseif(request('visit_id'))
                    <input type="hidden" name="visit_id" value="{{ request('visit_id') }}">
                @endif

                @if(isset($patient) || isset($visit))
                    <div style="margin-bottom: 16px; padding: 16px; border: 1px solid #f0d4d1; background:#f9fafb;">
                        @if(isset($patient))
                            <p style="margin:0 0 6px 0; font-weight:600;">Patient</p>
                            <p style="margin:0 0 8px 0;">#{{ $patient->id }} — {{ $patient->full_name }}</p>
                        @endif
                        @if(isset($visit))
                            <p style="margin:0 0 6px 0; font-weight:600;">Visit</p>
                            <p style="margin:0;">#{{ $visit->id }} — {{ ucfirst($visit->visit_type) }} on {{ optional($visit->visit_date)->format('Y-m-d H:i') }}</p>
                        @endif
                    </div>
                @endif

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Product <span style="color: #b8342b;">*</span></label>
                    <select name="product_id" id="productSelect" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #fff;" required onchange="updateProductDetails()">
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->quantity }}" data-expiry="{{ $product->expiry_date }}">
                                {{ $product->name }} (Stock: {{ $product->quantity }}, Price: USh {{ number_format($product->price, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Unit Price (USh)</label>
                        <input type="text" id="unitPrice" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #f9f9f9;" readonly>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Available Stock</label>
                        <input type="text" id="availableStock" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #f9f9f9;" readonly>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Quantity <span style="color: #b8342b;">*</span></label>
                        <input type="number" name="quantity" id="quantity" min="1" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" required onchange="calculateTotal()">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Total Amount (USh)</label>
                        <input type="text" id="totalAmount" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; background: #f9f9f9;" readonly>
                    </div>
                </div>

                <div id="expiryWarning" style="background: #fff3cd; border: 1px solid #ffeeba; color: #c87b16; padding: 12px; margin-bottom: 16px; display: none;">
                    <p style="margin: 0; font-weight: 600; font-size: 12px;">⚠️ This product is expiring soon or has expired</p>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Prescription / Note</label>
                    <textarea name="prescription_note" rows="3" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" placeholder="Optional note for pharmacy staff">{{ old('prescription_note') }}</textarea>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Prescription / Note</label>
                    <textarea name="prescription_note" rows="3" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" placeholder="Optional note for pharmacy staff">{{ old('prescription_note') }}</textarea>
                </div>
                <div style="display: flex; gap: 12px; border-top: 1px solid #f0d4d1; padding-top: 16px;">
                    <button type="submit" style="background: #b8342b; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;">✓ Register Sale</button>
                    <a href="{{ route('pharmacies.sales.index', $pharmacy) }}" style="background: #f5f5f5; color: #222; padding: 10px 20px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #f0d4d1; display: inline-block;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateProductDetails() {
    const select = document.getElementById('productSelect');
    const selected = select.options[select.selectedIndex];
    const price = selected.dataset.price;
    const stock = selected.dataset.stock;
    const expiry = selected.dataset.expiry;
    
    document.getElementById('unitPrice').value = price ? 'USh ' + Math.round(parseFloat(price)).toLocaleString('en-US') : '';
    document.getElementById('availableStock').value = stock || '';
    
    if (expiry && new Date(expiry) < new Date()) {
        document.getElementById('expiryWarning').style.display = 'block';
    } else {
        document.getElementById('expiryWarning').style.display = 'none';
    }
    
    calculateTotal();
}

function calculateTotal() {
    const select = document.getElementById('productSelect');
    const selected = select.options[select.selectedIndex];
    const price = parseFloat(selected.dataset.price) || 0;
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const total = price * quantity;
    
    document.getElementById('totalAmount').value = 'USh ' + Math.round(total).toLocaleString('en-US');
}
</script>
@endsection
