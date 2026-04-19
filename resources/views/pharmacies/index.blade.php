@extends('layouts.app')

@section('title', 'Pharmacies')
@section('content')
<div class="container-fluid ps-5 pe-5 pt-4">
    <div class="mb-4">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; color: #222; margin: 0; font-family: 'Poppins';">💊 Pharmacies</h1>
            <p style="color: #888; font-size: 12px; margin: 4px 0 0 0;\">Manage your pharmacy branches</p>
        </div>
    </div>

    <!-- Pharmacies Table -->
    <div style="background: #fff; border: 1px solid #f0d4d1;">
        <div style="padding: 16px; border-bottom: 1px solid #f0d4d1; background: #fafafa;">
            <p style="color: #222; font-size: 13px; font-weight: 600; margin: 0;\">Branches ({{ $pharmacies->total() }} items)</p>
        </div>
        <div class="table-responsive" style="border: none;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f9f9f9; border-bottom: 1px solid #f0d4d1;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none; width: 50px;\">#</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #666; border: none;\">Branch Name</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #666; border: none; width: 100px;\">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pharmacies as $pharmacy)
                        <tr style="border-bottom: 1px solid #f0d4d1;">
                            <td style="padding: 12px 16px; color: #666; font-weight: 500; border: none; font-family: monospace;\">{{ $pharmacy->id }}</td>
                            <td style="padding: 12px 16px; color: #222; font-weight: 500; border: none;\">{{ $pharmacy->branch->name ?? '-' }}</td>
                            <td style="padding: 12px 16px; border: none; text-align: center;">
                                <a href="{{ route('pharmacies.show', $pharmacy) }}" style="background: #b8342b; color: white; padding: 6px 12px; font-size: 11px; text-decoration: none; display: inline-block;\">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 16px; border-top: 1px solid #f0d4d1; background: #fafafa; display: flex; justify-content: center;">
            {{ $pharmacies->links() }}
        </div>
    </div>
</div>
@endsection
