@extends('layouts.app')

@section('title', 'Edit Category')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">✎ Edit Category</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $category->name }} • {{ $pharmacy->branch->name ?? 'Pharmacy' }}</p>
            </div>
            <a href="{{ route('pharmacies.categories.index', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back to Categories</a>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #f0d4d1; max-width: 600px; margin: 0 auto;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Category Information</p>
        </div>
        <div style="padding: 24px;">
            @if ($errors->any())
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 16px; margin-bottom: 16px; border-radius: 0;">
                    <strong>Validation Error:</strong>
                    <ul style="margin: 4px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pharmacies.categories.update', [$pharmacy, $category]) }}">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Category Name <span style="color: #b8342b;">*</span></label>
                    <input type="text" name="name" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px;" value="{{ old('name', $category->name) }}" required>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222; margin-bottom: 6px;">Description (Optional)</label>
                    <textarea name="description" style="border: 1px solid #f0d4d1; border-radius: 0; padding: 8px 10px; width: 100%; font-size: 12px; font-family: inherit;" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; border-top: 1px solid #f0d4d1; padding-top: 16px;">
                    <button type="submit" style="background: #b8342b; color: white; padding: 10px 20px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;\">✓ Update Category</button>
                    <a href="{{ route('pharmacies.categories.index', $pharmacy) }}" style="background: #f5f5f5; color: #222; padding: 10px 20px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #f0d4d1; display: inline-block;\">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
