@extends('layouts.app')

@section('title', 'Patient Details')
@section('section', 'Patient Registry')
@section('kicker', 'Patient Profile')
@section('page_title', $patient->full_name)
@section('page_subtitle', 'Reference profile for this patient, including registration, contact, and insurance information.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('visits.create', ['patient_id' => $patient->id]) }}">Check-in / Start Visit</a>
    <a class="ghost-button" href="{{ route('medical-records.create', ['patient_id' => $patient->id]) }}">New medical record</a>
    <a class="ghost-button" href="{{ route('laboratory.create', ['patient_id' => $patient->id]) }}">Order lab test</a>
    @if(isset($pharmacyForPatient))
        <a class="ghost-button" href="{{ route('pharmacies.sales.create', [$pharmacyForPatient, 'patient_id' => $patient->id]) }}">Send prescription to pharmacy</a>
    @endif
    <a class="ghost-button" href="{{ route('patients.index') }}">Back</a>
    <a class="ghost-button" href="{{ route('patients.edit', $patient) }}">Edit patient</a>
@endsection

@section('content')
    <div class="patient-profile-container" style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Patient Summary Card -->
        <div class="patient-summary-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); height: fit-content;">
            <div class="patient-avatar-large" style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 2rem; margin-bottom: 1rem;">
                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                </div>
                <h2 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 600; color: #1f2937;">{{ $patient->full_name }}</h2>
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">MRN: {{ $patient->mrn }}</p>
            </div>

            <div class="patient-quick-info">
                <div class="info-item" style="margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.2rem; margin-right: 0.5rem;">🏥</span>
                        <span style="font-weight: 500; color: #374151;">Branch</span>
                    </div>
                    <p style="margin: 0; color: #6b7280;">{{ optional($patient->branch)->name ?: 'Unassigned' }}</p>
                </div>

                <div class="info-item" style="margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.2rem; margin-right: 0.5rem;">📊</span>
                        <span style="font-weight: 500; color: #374151;">Status</span>
                    </div>
                    <span class="status-pill {{ $patient->status }}" style="font-size: 0.85rem; padding: 0.3rem 0.6rem; border-radius: 20px; font-weight: 500;">{{ ucfirst($patient->status) }}</span>
                </div>

                @if($patient->dob)
                    <div class="info-item" style="margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">🎂</span>
                            <span style="font-weight: 500; color: #374151;">Date of Birth</span>
                        </div>
                        <p style="margin: 0; color: #6b7280;">{{ $patient->dob->format('M d, Y') }} ({{ $patient->dob->age }} years old)</p>
                    </div>
                @endif

                @if($patient->gender)
                    <div class="info-item" style="margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">👤</span>
                            <span style="font-weight: 500; color: #374151;">Gender</span>
                        </div>
                        <p style="margin: 0; color: #6b7280;">{{ ucfirst($patient->gender) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="patient-main-content">
            <!-- Contact & Insurance Information -->
            <div class="panel" style="margin-bottom: 2rem;">
                <div class="panel-header" style="margin-bottom: 1.5rem;">
                    <h2 class="section-title" style="font-size: 1.25rem; font-weight: 600; margin: 0;">Contact & Insurance Information</h2>
                </div>

                <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="info-section">
                        <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; display: flex; align-items: center;">
                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">📞</span>
                            Contact Details
                        </h3>
                        <div class="info-rows">
                            <div class="info-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                                <span style="color: #6b7280;">Phone:</span>
                                <span style="font-weight: 500;">{{ $patient->phone ?: 'Not provided' }}</span>
                            </div>
                            <div class="info-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                                <span style="color: #6b7280;">Email:</span>
                                <span style="font-weight: 500;">{{ $patient->email ?: 'Not provided' }}</span>
                            </div>
                            <div class="info-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                <span style="color: #6b7280;">Address:</span>
                                <span style="font-weight: 500;">{{ $patient->address ?: 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; display: flex; align-items: center;">
                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">🆔</span>
                            Identification
                        </h3>
                        <div class="info-rows">
                            <div class="info-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                                <span style="color: #6b7280;">National ID:</span>
                                <span style="font-weight: 500;">{{ $patient->national_id ?: 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin-bottom: 1rem; display: flex; align-items: center;">
                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">🏥</span>
                            Insurance
                        </h3>
                        <div class="info-rows">
                            <div class="info-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;">
                                <span style="color: #6b7280;">Provider:</span>
                                <span style="font-weight: 500;">{{ $patient->insurance_provider ?: 'Not provided' }}</span>
                            </div>
                            <div class="info-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                                <span style="color: #6b7280;">Number:</span>
                                <span style="font-weight: 500;">{{ $patient->insurance_number ?: 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="panel" style="margin-bottom: 2rem;">
                <div class="panel-header" style="margin-bottom: 1rem;">
                    <h2 class="section-title" style="font-size: 1.25rem; font-weight: 600; margin: 0;">Allergies</h2>
                </div>
                @if($patient->allergies->isNotEmpty())
                    <div class="table-wrap" style="margin-bottom: 1rem;">
                        <table>
                            <thead><tr><th>Substance</th><th>Reaction</th><th>Severity</th><th></th></tr></thead>
                            <tbody>
                                @foreach($patient->allergies as $allergy)
                                    <tr>
                                        <td>{{ $allergy->substance }}</td>
                                        <td>{{ $allergy->reaction ?: '-' }}</td>
                                        <td>{{ ucfirst($allergy->severity) }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('patients.allergies.destroy', $allergy) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="danger-button" type="submit">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="subtle">No known allergies recorded.</p>
                @endif
                <form method="POST" action="{{ route('patients.allergies.store', $patient) }}" class="form-grid">
                    @csrf
                    <div><label>Substance</label><input name="substance" placeholder="e.g. Penicillin" required></div>
                    <div><label>Reaction</label><input name="reaction" placeholder="e.g. rash"></div>
                    <div><label>Severity</label><select name="severity"><option value="unknown">Unknown</option><option value="mild">Mild</option><option value="moderate">Moderate</option><option value="severe">Severe</option></select></div>
                    <div><button class="primary-button" type="submit">Add allergy</button></div>
                </form>
            </div>

            <div class="stats-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div class="stat-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📋</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #1f2937;">{{ $patient->visits->where('status', 'open')->count() }}</div>
                    <div style="color: #6b7280; font-size: 0.9rem;">Open Visits</div>
                </div>
                <div class="stat-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📄</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #1f2937;">{{ $patient->medicalRecords->count() }}</div>
                    <div style="color: #6b7280; font-size: 0.9rem;">Medical Records</div>
                </div>
                <div class="stat-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🧪</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #1f2937;">{{ $patient->labTests->count() }}</div>
                    <div style="color: #6b7280; font-size: 0.9rem;">Lab Tests</div>
                </div>
                <div class="stat-card" style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">💊</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #1f2937;">{{ $patient->visits->count() }}</div>
                    <div style="color: #6b7280; font-size: 0.9rem;">Total Visits</div>
                </div>
            </div>

            <!-- Visit History -->
            <div class="panel">
                <div class="panel-header" style="margin-bottom: 1.5rem;">
                    <h2 class="section-title" style="font-size: 1.25rem; font-weight: 600; margin: 0;">Visit History</h2>
                </div>

                @if($patient->visits->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" style="background:#fff;">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Provider</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->visits as $visit)
                                    <tr>
                                        <td>{{ optional($visit->visit_date)->format('M d, Y H:i') ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($visit->visit_type ?? 'general') }}</td>
                                        <td><span class="status-pill {{ $visit->status }}" style="font-size: 0.8rem; padding: 0.25rem 0.5rem; border-radius: 12px; font-weight: 500;">{{ ucfirst($visit->status) }}</span></td>
                                        <td>{{ optional($visit->provider)->name ?: 'Unassigned' }}</td>
                                        <td><a class="ghost-button" href="{{ route('visits.show', $visit) }}" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state" style="text-align: center; padding: 2rem; color: #6b7280;">
                        <div style="font-size: 2rem; margin-bottom: 1rem;">📋</div>
                        <h3 style="margin-bottom: 0.5rem; color: #374151;">No visits yet</h3>
                        <p>No visit history available for this patient.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .status-pill.active { background: #d1fae5; color: #065f46; }
        .status-pill.inactive { background: #fee2e2; color: #991b1b; }
        .status-pill.open { background: #dbeafe; color: #1e40af; }
        .status-pill.closed { background: #f3f4f6; color: #374151; }
        .status-pill.completed { background: #d1fae5; color: #065f46; }

        .patient-profile-container, .patient-summary-card, .patient-main-content, .stat-card {
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
        }
        .patient-profile-container *, .patient-summary-card *, .patient-main-content *, .stat-card * {
            font-size: 11px !important;
        }

        @media (max-width: 1024px) {
            .patient-profile-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
