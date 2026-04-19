@extends('layouts.app')

@section('title', 'Product Categories')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">🏷️ Product Categories</h1>
                <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;">{{ $pharmacy->branch->name ?? 'Pharmacy' }} • Organize your products</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('pharmacies.categories.create', $pharmacy) }}" style="background: #b8342b; color: white; padding: 8px 16px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block;\">+ Add Category</a>
                <a href="{{ route('pharmacies.show', $pharmacy) }}" style="color: #b8342b; font-size: 12px; text-decoration: none; border: 1px solid #f0d4d1; padding: 8px 16px; display: inline-block; background: #fff;\">← Back</a>
            </div>
        </div>

        @if (session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #2f7d57; padding: 12px 16px; margin-bottom: 16px; border-radius: 0;\">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Categories ({{ $categories->count() }} items)</p>
        </div>

        @if($categories->count() > 0)
            <div class="table-responsive" style="border: none;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                            <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Name</th>
                            <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Description</th>
                            <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Products</th>
                            <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none;\">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr style="border-bottom: 1px solid #f0d4d1;">
                                <td style="padding: 12px 16px; color: #222; font-weight: 500; border: none;\">{{ $category->name }}</td>
                                <td style="padding: 12px 16px; color: #666; border: none;\">{{ $category->description ?? '-' }}</td>
                                <td style="padding: 12px 16px; color: #222; border: none; text-align: center; font-weight: 600;\">
                                    <span style="background: #e7f5ff; color: #2f6fed; padding: 4px 8px; display: inline-block; font-size: 11px; font-weight: 600;\">{{ $category->products()->count() }}</span>
                                </td>
                                <td style="padding: 12px 16px; border: none; text-align: center;">
                                    <a href="{{ route('pharmacies.categories.edit', [$pharmacy, $category]) }}" style="background: #7c3aed; color: white; padding: 6px 12px; font-size: 11px; text-decoration: none; display: inline-block; margin-right: 4px;\">Edit</a>
                                    <form action="{{ route('pharmacies.categories.destroy', [$pharmacy, $category]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #dc3545; color: white; padding: 6px 12px; font-size: 11px; border: none; cursor: pointer; display: inline-block;\">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 32px; text-align: center; color: #888;\">
                <p style="font-size: 12px; margin: 0;\">No categories found. Create your first category.</p>
            </div>
        @endif
    </div>
</div>
@endsection
