@extends('layouts.app')

@section('title', 'Edit Appointment')
@section('section', 'Scheduling')
@section('kicker', 'Appointment Desk')
@section('page_title', 'Edit appointment #' . $appointment->id)
@section('page_subtitle', 'Update schedule details, assigned doctor, status, and notes for this appointment.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('appointments.index') }}">Back to appointments</a>
@endsection

@section('content')
    <div class="panel">
        @if ($errors->any())
            <div class="alert-box alert-error" style="margin-bottom: 1rem;">
                <strong>There are validation errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('appointments.update', $appointment) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label for="patient_id">Patient</label>
                    <select id="patient_id" name="patient_id" required>
                        <option value="">Select patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>{{ $patient->mrn }} - {{ $patient->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="doctor_id">Doctor</label>
                    <select id="doctor_id" name="doctor_id">
                        <option value="">Any available doctor</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="service_type">Service type</label>
                    <input id="service_type" name="service_type" value="{{ old('service_type', $appointment->service_type) }}">
                </div>
                <div class="field">
                    <label for="scheduled_at">Scheduled at</label>
                    <input id="scheduled_at" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="field">
                    <label for="duration">Duration (minutes)</label>
                    <input id="duration" type="number" name="duration" value="{{ old('duration', $appointment->duration) }}" min="5" max="240" required>
                </div>
                <div id="appointment-availability-message" class="alert" style="display:none; margin-top: 0.5rem; grid-column: span 2;"></div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        @foreach(['scheduled','confirmed','checked_in','completed','canceled','no_show'] as $status)
                            <option value="{{ $status }}" {{ old('status', $appointment->status) == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field field-span-2">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes">{{ old('notes', $appointment->notes) }}</textarea>
                </div>
            </div>
            <div class="action-stack" style="margin-top: 1.25rem;">
                <button class="primary-button" type="submit">Update appointment</button>
                <a class="ghost-button" href="{{ route('appointments.index') }}">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var doctorField = document.getElementById('doctor_id');
            var scheduleField = document.getElementById('scheduled_at');
            var durationField = document.getElementById('duration');
            var statusEl = document.getElementById('appointment-availability-message');
            var timer;

            function setMessage(type, message) {
                statusEl.style.display = 'block';
                statusEl.className = 'alert ' + (type === 'ok' ? 'success' : 'error');
                statusEl.textContent = message;
            }

            function clearMessage() {
                statusEl.style.display = 'none';
                statusEl.textContent = '';
            }

            function checkAvailability() {
                var dateValue = scheduleField.value;
                var durationValue = durationField.value;
                var doctorValue = doctorField.value;

                if (!dateValue || !durationValue) {
                    clearMessage();
                    return;
                }

                var params = new URLSearchParams({
                    date: dateValue.split('T')[0],
                    duration: durationValue
                });

                if (doctorValue) {
                    params.append('doctor_id', doctorValue);
                }

                fetch('{{ route('appointments.availability') }}?' + params.toString(), {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                }).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    if (doctorValue && data.available === false) {
                        setMessage('error', 'This doctor has an appointment on the selected date/time. Adjust the schedule or choose another doctor.');
                    } else if (doctorValue && data.available === true) {
                        setMessage('success', 'This doctor is available for the selected date/time.');
                    } else if (!doctorValue) {
                        setMessage('ok', 'No doctor selected yet. The selected slot is being checked for branch conflicts.');
                    }
                }).catch(function () {
                    setMessage('error', 'Unable to verify availability at this time.');
                });
            }

            [doctorField, scheduleField, durationField].forEach(function (field) {
                if (!field) return;
                field.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(checkAvailability, 400);
                });
            });
        });
    </script>
@endsection
