@extends('layouts.app')

@section('title', 'New Prescription')
@section('section', 'Clinical Operations')
@section('page_title', 'New prescription')
@section('page_subtitle', $visit->patient->full_name . ' - Visit #' . $visit->id)

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('prescriptions.store') }}">
        @csrf
        <input type="hidden" name="visit_id" value="{{ $visit->id }}">

        <div class="form-grid">
            @for($i = 0; $i < 3; $i++)
                <div class="detail-item">
                    <label>Medicine</label>
                    <select name="items[{{ $i }}][product_id]" {{ $i === 0 ? 'required' : '' }}>
                        <option value="">Select medicine</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} - USh {{ number_format($product->price, 0) }} ({{ $product->quantity }} in stock)</option>
                        @endforeach
                    </select>
                    <label>Quantity</label>
                    <input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ $i === 0 ? 1 : '' }}" {{ $i === 0 ? 'required' : '' }}>
                    <label>Dosage</label>
                    <input type="text" name="items[{{ $i }}][dosage]" placeholder="e.g. 500mg">
                    <label>Frequency</label>
                    <input type="text" name="items[{{ $i }}][frequency]" placeholder="e.g. twice daily">
                    <label>Duration</label>
                    <input type="text" name="items[{{ $i }}][duration]" placeholder="e.g. 5 days">
                    <label>Instructions</label>
                    <textarea name="items[{{ $i }}][instructions]" rows="2"></textarea>
                </div>
            @endfor
        </div>

        <div class="field" style="margin-top: 1rem;">
            <label>General notes</label>
            <textarea name="notes" rows="3"></textarea>
        </div>

        <div style="margin-top: 1rem;">
            <button class="primary-button" type="submit">Send to pharmacy</button>
            <a class="ghost-button" href="{{ route('visits.show', $visit) }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
