@extends('layouts.app')

@php
    $isEdit = $requisition->exists;
    $items = old('items', $isEdit ? $requisition->items->map(fn ($item) => $item->only(['item', 'unit_cost', 'quantity', 'frequency']))->toArray() : [
        ['item' => '', 'unit_cost' => '', 'quantity' => 1, 'frequency' => '']
    ]);
@endphp

@section('title', $isEdit ? 'Edit Requisition' : 'New Requisition')
@section('page_title', $isEdit ? 'Edit requisition' : 'New requisition')
@section('page_subtitle', 'Fill the MAHIPSO requisition form and submit it to your line supervisor.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('requisitions.index') }}">Back to requisitions</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ $isEdit ? route('requisitions.update', $requisition) : route('requisitions.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="field">
                    <label>From</label>
                    <input type="text" value="{{ auth()->user()->name }}" disabled>
                </div>

                <div class="field">
                    <label>Department</label>
                    <input type="text" name="department" value="{{ old('department', $requisition->department ?? auth()->user()->department) }}">
                    @error('department')<p class="subtle">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label>Date</label>
                    <input type="date" name="requested_at" value="{{ old('requested_at', optional($requisition->requested_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    @error('requested_at')<p class="subtle">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label>Line supervisor</label>
                    <input type="text" value="{{ optional(auth()->user()->lineSupervisor)->name ?? 'Not assigned' }}" disabled>
                </div>

                <div class="field field-span-2">
                    <label>Purpose / notes</label>
                    <textarea name="purpose">{{ old('purpose', $requisition->purpose) }}</textarea>
                    @error('purpose')<p class="subtle">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="panel-header" style="margin-top: 1rem;">
                <h2 class="section-title">Items</h2>
                <button class="ghost-button" type="button" onclick="addRequisitionRow()">Add item</button>
            </div>

            <div class="table-wrap">
                <table id="requisition-items">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Unit cost</th>
                            <th>Quantity</th>
                            <th>Freq</th>
                            <th>Total cost</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td><input name="items[{{ $index }}][item]" value="{{ $item['item'] ?? '' }}" required></td>
                                <td><input class="unit-cost" type="number" step="0.01" min="0" name="items[{{ $index }}][unit_cost]" value="{{ $item['unit_cost'] ?? '' }}" required oninput="calculateRequisitionTotals()"></td>
                                <td><input class="quantity" type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" required oninput="calculateRequisitionTotals()"></td>
                                <td><input name="items[{{ $index }}][frequency]" value="{{ $item['frequency'] ?? '' }}"></td>
                                <td class="line-total">0.00</td>
                                <td><button class="danger-button" type="button" onclick="removeRequisitionRow(this)">Remove</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total</th>
                            <th id="grand-total">0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @error('items')<p class="subtle">{{ $message }}</p>@enderror

            <div class="form-grid" style="margin-top: 1rem;">
                <div class="field field-span-2">
                    <label>Total amount in words</label>
                    <input type="text" name="amount_in_words" value="{{ old('amount_in_words', $requisition->amount_in_words) }}">
                    @error('amount_in_words')<p class="subtle">{{ $message }}</p>@enderror
                </div>

                <div></div>
                <div class="inline-actions">
                    <button class="ghost-button" type="submit" name="action" value="draft">Save draft</button>
                    <button class="primary-button" type="submit" name="action" value="submit">Submit to supervisor</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function calculateRequisitionTotals() {
            let total = 0;
            document.querySelectorAll('#requisition-items tbody tr').forEach((row) => {
                const unitCost = parseFloat(row.querySelector('.unit-cost').value || 0);
                const quantity = parseFloat(row.querySelector('.quantity').value || 0);
                const lineTotal = unitCost * quantity;
                row.querySelector('.line-total').textContent = lineTotal.toFixed(2);
                total += lineTotal;
            });
            document.getElementById('grand-total').textContent = total.toFixed(2);
        }

        function addRequisitionRow() {
            const tbody = document.querySelector('#requisition-items tbody');
            const index = tbody.querySelectorAll('tr').length;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input name="items[${index}][item]" required></td>
                <td><input class="unit-cost" type="number" step="0.01" min="0" name="items[${index}][unit_cost]" required oninput="calculateRequisitionTotals()"></td>
                <td><input class="quantity" type="number" step="0.01" min="0.01" name="items[${index}][quantity]" value="1" required oninput="calculateRequisitionTotals()"></td>
                <td><input name="items[${index}][frequency]"></td>
                <td class="line-total">0.00</td>
                <td><button class="danger-button" type="button" onclick="removeRequisitionRow(this)">Remove</button></td>
            `;
            tbody.appendChild(row);
        }

        function removeRequisitionRow(button) {
            const tbody = document.querySelector('#requisition-items tbody');
            if (tbody.querySelectorAll('tr').length > 1) {
                button.closest('tr').remove();
                calculateRequisitionTotals();
            }
        }

        calculateRequisitionTotals();
    </script>
@endsection
