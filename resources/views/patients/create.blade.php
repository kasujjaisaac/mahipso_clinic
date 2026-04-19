@extends('layouts.app')

@section('title', 'Create Patient')
@section('section', 'Patient Registry')
@section('kicker', 'Registration')
@section('page_title', 'Register a patient')
@section('page_subtitle', 'Capture demographic, insurance, and branch details in one clean registration form.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('patients.index') }}">Back to patients</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('patients.store') }}">
            @csrf

            <!-- Basic Information Section -->
            <div class="form-section" style="margin-bottom: 2rem;">
                <div class="section-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: #1f2937; display: flex; align-items: center;">
                        <span style="font-size: 1.5rem; margin-right: 0.5rem;">👤</span>
                        Basic Information
                    </h2>
                    <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Patient's personal and identification details</p>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="branch_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Branch <span style="color: #ef4444;">*</span></label>
                        <select id="branch_id" name="branch_id" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                            <option value="">Select a branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->code }})</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="mrn" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Medical Record Number (MRN) <span style="color: #ef4444;">*</span></label>
                        <input id="mrn" name="mrn" value="{{ old('mrn') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('mrn')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="first_name" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">First Name <span style="color: #ef4444;">*</span></label>
                        <input id="first_name" name="first_name" value="{{ old('first_name') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('first_name')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="last_name" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Last Name <span style="color: #ef4444;">*</span></label>
                        <input id="last_name" name="last_name" value="{{ old('last_name') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('last_name')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="dob" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Date of Birth</label>
                        <input id="dob" type="date" name="dob" value="{{ old('dob') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('dob')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="gender" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Gender</label>
                        <select id="gender" name="gender" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section" style="margin-bottom: 2rem;">
                <div class="section-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: #1f2937; display: flex; align-items: center;">
                        <span style="font-size: 1.5rem; margin-right: 0.5rem;">📞</span>
                        Contact Information
                    </h2>
                    <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Patient's contact details and address</p>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="phone" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Phone Number</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('phone')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('email')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field field-span-2">
                        <label for="address" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Address</label>
                        <textarea id="address" name="address" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;">{{ old('address') }}</textarea>
                        @error('address')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Insurance & Identification Section -->
            <div class="form-section" style="margin-bottom: 2rem;">
                <div class="section-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: #1f2937; display: flex; align-items: center;">
                        <span style="font-size: 1.5rem; margin-right: 0.5rem;">🆔</span>
                        Insurance & Identification
                    </h2>
                    <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Insurance details and national identification</p>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="national_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">National ID</label>
                        <input id="national_id" name="national_id" value="{{ old('national_id') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('national_id')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="insurance_provider" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Insurance Provider</label>
                        <input id="insurance_provider" name="insurance_provider" value="{{ old('insurance_provider') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('insurance_provider')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="insurance_number" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Insurance Number</label>
                        <input id="insurance_number" name="insurance_number" value="{{ old('insurance_number') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                        @error('insurance_number')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="status" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Status <span style="color: #ef4444;">*</span></label>
                        <select id="status" name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Visit Information Section -->
            <div class="form-section" style="margin-bottom: 2rem;">
                <div class="section-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; margin: 0; color: #1f2937; display: flex; align-items: center;">
                        <span style="font-size: 1.5rem; margin-right: 0.5rem;">🏥</span>
                        Initial Visit (Optional)
                    </h2>
                    <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Start a visit immediately after registration</p>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="provider_id" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Assign Doctor</label>
                        <select id="provider_id" name="provider_id" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                            <option value="">Select a doctor (optional)</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('provider_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ optional($doctor->branch)->name ? $doctor->branch->name . ' - ' : '' }}{{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('provider_id')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="visit_type" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Visit Type</label>
                        <select id="visit_type" name="visit_type" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                            @foreach(['general','hiv','counseling','lab','pharmacy','other'] as $type)
                                <option value="{{ $type }}" {{ old('visit_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                        @error('visit_type')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field field-span-2">
                        <label for="chief_complaint" style="font-weight: 500; color: #374151; margin-bottom: 0.5rem; display: block;">Reason for Visit</label>
                        <textarea id="chief_complaint" name="chief_complaint" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; resize: vertical;">{{ old('chief_complaint') }}</textarea>
                        @error('chief_complaint')
                            <p style="color: #ef4444; font-size: 0.8rem; margin: 0.25rem 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="action-stack" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; display: flex; gap: 1rem; justify-content: flex-end;">
                <a class="ghost-button" href="{{ route('patients.index') }}" style="padding: 0.75rem 1.5rem;">Cancel</a>
                <button class="primary-button" type="submit" style="padding: 0.75rem 1.5rem;">Register Patient</button>
            </div>
        </form>
    </div>

    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .field-span-2 {
            grid-column: span 2;
        }
        .panel, .form-section, .section-header {
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
        }
        .panel *, .form-section *, .section-header * {
            font-size: 11px !important;
        }
        @media (max-width: 768px) {
            .field-span-2 {
                grid-column: span 1;
            }
        }
    </style>
@endsection
