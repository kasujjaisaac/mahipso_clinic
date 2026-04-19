@extends('layouts.app')

@section('title', 'Patients')
@section('section', 'Patient Registry')
@section('kicker', 'Clinic Operations')
@section('page_title', 'Patients')
@section('page_subtitle', 'Search, register, and maintain patient profiles across the clinic network.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('patients.create') }}">New patient</a>
@endsection

@section('content')
    <div class="panel" style="margin-top:1.5rem;">
        <div class="table-toolbar" style="margin-bottom:1.2rem;">
            <div>
                <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Patient Register</h2>
                <p class="table-meta" style="font-size:0.95rem; color:#888;">Find and filter patients by various criteria.</p>
            </div>
            <form class="toolbar-form d-flex align-items-center gap-2" method="GET" action="{{ route('patients.index') }}" style="margin-bottom:0;">
                @if (request('branch'))
                    <input type="hidden" name="branch" value="{{ request('branch') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patients" style="min-width:200px;" class="form-control">
                <button class="ghost-button" type="submit">Search</button>
            </form>
        </div>

        <!-- Filters Section -->
        <div class="filters-section" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('patients.index') }}" class="filters-form">
                @if (request('branch'))
                    <input type="hidden" name="branch" value="{{ request('branch') }}">
                @endif
                
                <div class="filters-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                    <!-- Status Filter -->
                    <div class="filter-group">
                        <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Status</label>
                        <select id="status" name="status" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; background: white;">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div class="filter-group">
                        <label for="gender" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Gender</label>
                        <select id="gender" name="gender" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; background: white;">
                            <option value="">All Genders</option>
                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ request('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Date From Filter -->
                    <div class="filter-group">
                        <label for="date_from" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Registered From</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    </div>

                    <!-- Date To Filter -->
                    <div class="filter-group">
                        <label for="date_to" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Registered To</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    </div>

                    @if(isset($availableBranches) && $availableBranches->count() > 0)
                    <!-- Branch Filter (Super Admin Only) -->
                    <div class="filter-group">
                        <label for="branch_filter" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Branch</label>
                        <select id="branch_filter" name="branch_filter" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; background: white;">
                            <option value="">All Branches</option>
                            @foreach($availableBranches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_filter') == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="filter-group" style="display: flex; gap: 0.5rem; align-items: end;">
                        <button type="submit" class="primary-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Apply Filters</button>
                        <a href="{{ route('patients.index') }}" class="ghost-button" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        @if($patients->count() > 0)
            <div class="patient-cards-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                @foreach($patients as $patient)
                    <div class="patient-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s ease;">
                        <div class="patient-card-header" style="display: flex; align-items: center; margin-bottom: 1rem;">
                            <div class="patient-avatar" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem; margin-right: 1rem;">
                                {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                            </div>
                            <div class="patient-info">
                                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">{{ $patient->full_name }}</h3>
                                <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">MRN: {{ $patient->mrn }}</p>
                            </div>
                        </div>

                        <div class="patient-details" style="margin-bottom: 1rem;">
                            <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: #6b7280; font-size: 0.9rem;">Branch:</span>
                                <span style="font-weight: 500;">{{ optional($patient->branch)->name ?: 'Unassigned' }}</span>
                            </div>
                            <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: #6b7280; font-size: 0.9rem;">Status:</span>
                                <span class="status-pill {{ $patient->status }}" style="font-size: 0.8rem; padding: 0.25rem 0.5rem; border-radius: 12px; font-weight: 500;">{{ ucfirst($patient->status) }}</span>
                            </div>
                            @if($patient->phone)
                                <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span style="color: #6b7280; font-size: 0.9rem;">Phone:</span>
                                    <span style="font-weight: 500;">{{ $patient->phone }}</span>
                                </div>
                            @endif
                            @if($patient->email)
                                <div class="detail-row" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span style="color: #6b7280; font-size: 0.9rem;">Email:</span>
                                    <span style="font-weight: 500; word-break: break-all;">{{ $patient->email }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="patient-actions" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a class="ghost-button" href="{{ route('patients.show', $patient) }}" style="flex: 1; text-align: center; padding: 0.5rem; font-size: 0.9rem;">View</a>
                            <a class="primary-button" href="{{ route('patients.edit', $patient) }}" style="flex: 1; text-align: center; padding: 0.5rem; font-size: 0.9rem;">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state" style="text-align: center; padding: 3rem; color: #6b7280;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                <h3 style="margin-bottom: 0.5rem; color: #374151;">No patients found</h3>
                <p style="margin-bottom: 2rem;">No patients match your current search criteria.</p>
                <a class="primary-button" href="{{ route('patients.create') }}">Register First Patient</a>
            </div>
        @endif

        <div class="pagination-wrap" style="margin-top:1.2rem;">{{ $patients->links() }}</div>
    </div>

    <style>
        .status-pill.active {
            background: #d1fae5;
            color: #065f46;
        }
        .status-pill.inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        .patient-card:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .filters-section, .patient-card, .empty-state {
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
        }
        .filters-section *, .patient-card *, .empty-state * {
            font-size: 11px !important;
        }
        @media (max-width: 768px) {
            .patient-cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
