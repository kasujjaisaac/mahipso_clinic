@extends('layouts.app')

@section('title', 'Inpatient Chart')
@section('section', 'Inpatient Management')
@section('kicker', 'Inpatient Chart')
@section('page_title', $admission->admission_no . ' - ' . $admission->patient->full_name)
@section('page_subtitle', 'Ward care, vitals, medication administration, transfer history, clearance, and discharge summary.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('admissions.index') }}">Admissions</a>
    <a class="ghost-button" href="{{ route('wards.index') }}">Bed board</a>
    @if($admission->visit)
        <a class="ghost-button" href="{{ route('visits.show', $admission->visit) }}">Visit</a>
    @endif
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ $admission->patient->full_name }} - {{ $admission->patient->mrn }}</div></div>
            <div class="detail-item"><span class="detail-label">Ward / Bed</span><div class="detail-value">{{ $admission->ward->name }} / {{ $admission->bed->bed_number }}</div></div>
            <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value"><span class="status-pill {{ $admission->status }}">{{ $admission->status_label }}</span></div></div>
            <div class="detail-item"><span class="detail-label">Admitted</span><div class="detail-value">{{ $admission->admitted_at->format('Y-m-d H:i') }} ({{ $admission->length_of_stay }} days)</div></div>
            <div class="detail-item"><span class="detail-label">Doctor</span><div class="detail-value">{{ optional($admission->currentDoctor)->name ?: optional($admission->admittingDoctor)->name ?: 'Unassigned' }}</div></div>
            <div class="detail-item"><span class="detail-label">Clearance</span><div class="detail-value">Nursing {{ $admission->nursing_cleared ? 'Cleared' : 'Pending' }} - Pharmacy {{ $admission->pharmacy_cleared ? 'Cleared' : 'Pending' }} - Billing {{ $admission->billing_cleared ? 'Cleared' : 'Pending' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Diagnosis</span><div class="detail-value">{{ $admission->current_diagnosis ?: $admission->provisional_diagnosis ?: 'Not recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Care plan</span><div class="detail-value">{{ $admission->care_plan ?: 'No care plan recorded.' }}</div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="section-title">Update care plan</h2></div>
        <form method="POST" action="{{ route('admissions.update', $admission) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div class="field-span-2"><label>Current diagnosis</label><textarea name="current_diagnosis" rows="3">{{ old('current_diagnosis', $admission->current_diagnosis) }}</textarea></div>
            <div class="field-span-2"><label>Care plan</label><textarea name="care_plan" rows="3">{{ old('care_plan', $admission->care_plan) }}</textarea></div>
            <div><label>Expected discharge</label><input type="datetime-local" name="expected_discharge_at" value="{{ optional($admission->expected_discharge_at)->format('Y-m-d\\TH:i') }}"></div>
            <div><label>Payment type</label><input name="payment_type" value="{{ old('payment_type', $admission->payment_type) }}"></div>
            <div><button class="primary-button" type="submit">Save plan</button></div>
        </form>
    </div>

    <div class="panel" id="vitals">
        <div class="panel-header"><h2 class="section-title">Record vitals</h2></div>
        <form method="POST" action="{{ route('admissions.vitals.store', $admission) }}" class="form-grid">
            @csrf
            <div><label>Temperature</label><input type="number" step="0.1" name="temperature"></div>
            <div><label>BP systolic</label><input type="number" name="blood_pressure_systolic"></div>
            <div><label>BP diastolic</label><input type="number" name="blood_pressure_diastolic"></div>
            <div><label>Pulse</label><input type="number" name="pulse"></div>
            <div><label>Respiratory rate</label><input type="number" name="respiratory_rate"></div>
            <div><label>SpO2</label><input type="number" name="oxygen_saturation"></div>
            <div><label>Weight</label><input type="number" step="0.01" name="weight"></div>
            <div><label>Pain score</label><input type="number" min="0" max="10" name="pain_score"></div>
            <div><label>Intake ml</label><input type="number" step="0.01" name="intake_ml"></div>
            <div><label>Output ml</label><input type="number" step="0.01" name="output_ml"></div>
            <div><label>Recorded at</label><input type="datetime-local" name="recorded_at" value="{{ now()->format('Y-m-d\\TH:i') }}" required></div>
            <div class="field-span-2"><label>Notes</label><textarea name="notes" rows="2"></textarea></div>
            <div><button class="primary-button" type="submit">Record vitals</button></div>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="section-title">Vitals chart</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Time</th><th>Temp</th><th>BP</th><th>Pulse</th><th>RR</th><th>SpO2</th><th>I/O</th><th>By</th></tr></thead>
                <tbody>
                    @forelse($admission->vitals->sortByDesc('recorded_at') as $vital)
                        <tr>
                            <td>{{ $vital->recorded_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $vital->temperature ?: '-' }}</td>
                            <td>{{ $vital->blood_pressure_systolic && $vital->blood_pressure_diastolic ? $vital->blood_pressure_systolic . '/' . $vital->blood_pressure_diastolic : '-' }}</td>
                            <td>{{ $vital->pulse ?: '-' }}</td>
                            <td>{{ $vital->respiratory_rate ?: '-' }}</td>
                            <td>{{ $vital->oxygen_saturation ?: '-' }}</td>
                            <td>{{ $vital->intake_ml ?: 0 }} / {{ $vital->output_ml ?: 0 }}</td>
                            <td>{{ optional($vital->recorder)->name ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">No inpatient vitals recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel" id="notes">
        <div class="panel-header"><h2 class="section-title">Add clinical or nursing note</h2></div>
        <form method="POST" action="{{ route('admissions.notes.store', $admission) }}" class="form-grid">
            @csrf
            <div><label>Type</label><select name="note_type">@foreach(['doctor_round','nursing','care_plan','handover','procedure','other'] as $type)<option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>@endforeach</select></div>
            <div><label>Recorded at</label><input type="datetime-local" name="recorded_at" value="{{ now()->format('Y-m-d\\TH:i') }}" required></div>
            <div><label>Subjective</label><textarea name="subjective" rows="2"></textarea></div>
            <div><label>Objective</label><textarea name="objective" rows="2"></textarea></div>
            <div><label>Assessment</label><textarea name="assessment" rows="2"></textarea></div>
            <div><label>Plan</label><textarea name="plan" rows="2"></textarea></div>
            <div class="field-span-2"><label>General note</label><textarea name="note" rows="3"></textarea></div>
            <div><button class="primary-button" type="submit">Add note</button></div>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="section-title">Progress notes</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Time</th><th>Type</th><th>Author</th><th>Note</th></tr></thead>
                <tbody>
                    @forelse($admission->notes->sortByDesc('recorded_at') as $note)
                        <tr>
                            <td>{{ $note->recorded_at->format('Y-m-d H:i') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $note->note_type)) }}</td>
                            <td>{{ optional($note->author)->name ?: '-' }}</td>
                            <td>
                                @if($note->subjective)<strong>S:</strong> {{ $note->subjective }}<br>@endif
                                @if($note->objective)<strong>O:</strong> {{ $note->objective }}<br>@endif
                                @if($note->assessment)<strong>A:</strong> {{ $note->assessment }}<br>@endif
                                @if($note->plan)<strong>P:</strong> {{ $note->plan }}<br>@endif
                                {{ $note->note }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No notes recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel" id="medications">
        <div class="panel-header"><h2 class="section-title">Medication chart</h2></div>
        <form method="POST" action="{{ route('admissions.medications.store', $admission) }}" class="form-grid">
            @csrf
            <div><label>Medicine</label><input name="medicine_name" required></div>
            <div><label>Dose</label><input name="dose" required></div>
            <div><label>Route</label><input name="route" placeholder="Oral, IV, IM"></div>
            <div><label>Frequency</label><input name="frequency" placeholder="BD, TDS, QID"></div>
            <div><label>Start</label><input type="datetime-local" name="start_at" value="{{ now()->format('Y-m-d\\TH:i') }}"></div>
            <div><label>Stop</label><input type="datetime-local" name="stop_at"></div>
            <div class="field-span-2"><label>Instructions</label><textarea name="instructions" rows="2"></textarea></div>
            <div><button class="primary-button" type="submit">Add order</button></div>
        </form>

        <div class="table-wrap" style="margin-top:1rem;">
            <table>
                <thead><tr><th>Order</th><th>Schedule</th><th>Administer</th><th>History</th></tr></thead>
                <tbody>
                    @forelse($admission->medicationOrders as $order)
                        <tr>
                            <td><strong>{{ $order->medicine_name }}</strong><br>{{ $order->dose }} {{ $order->route }} {{ $order->frequency }}<br><span class="status-pill {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ optional($order->start_at)->format('Y-m-d H:i') ?: '-' }} to {{ optional($order->stop_at)->format('Y-m-d H:i') ?: 'ongoing' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admissions.medication-administrations.store', $admission) }}" class="toolbar-form">
                                    @csrf
                                    <input type="hidden" name="medication_order_id" value="{{ $order->id }}">
                                    <input type="datetime-local" name="scheduled_at">
                                    <select name="status"><option value="given">Given</option><option value="missed">Missed</option><option value="refused">Refused</option><option value="held">Held</option></select>
                                    <input name="notes" placeholder="Notes">
                                    <button class="ghost-button" type="submit">Record</button>
                                </form>
                            </td>
                            <td>
                                @forelse($order->administrations->sortByDesc('created_at') as $admin)
                                    <div>{{ $admin->administered_at?->format('Y-m-d H:i') }} - {{ ucfirst($admin->status) }} - {{ optional($admin->administeredBy)->name }}</div>
                                @empty
                                    <span class="subtle">No administrations.</span>
                                @endforelse
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No medication orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="section-title">Transfer patient</h2></div>
        <form method="POST" action="{{ route('admissions.transfer', $admission) }}" class="form-grid">
            @csrf
            <div><label>Ward</label><select name="to_ward_id">@foreach($wards as $ward)<option value="{{ $ward->id }}">{{ $ward->name }}</option>@endforeach</select></div>
            <div><label>Available bed</label><select name="to_bed_id">@foreach($wards as $ward)@foreach($ward->beds as $bed)<option value="{{ $bed->id }}">{{ $ward->name }} - Bed {{ $bed->bed_number }}</option>@endforeach @endforeach</select></div>
            <div><label>Reason</label><input name="reason" required></div>
            <div><label>Transferred at</label><input type="datetime-local" name="transferred_at" value="{{ now()->format('Y-m-d\\TH:i') }}" required></div>
            <div class="field-span-2"><label>Notes</label><textarea name="notes" rows="2"></textarea></div>
            <div><button class="primary-button" type="submit">Transfer</button></div>
        </form>
    </div>

    <div class="panel" id="discharge">
        <div class="panel-header"><h2 class="section-title">Discharge clearance</h2></div>
        <div class="toolbar-form">
            <form method="POST" action="{{ route('admissions.ready', $admission) }}">@csrf<button class="ghost-button" type="submit">Mark ready</button></form>
            @foreach(['nursing_cleared' => 'Nursing clear', 'pharmacy_cleared' => 'Pharmacy clear', 'billing_cleared' => 'Billing clear'] as $field => $label)
                <form method="POST" action="{{ route('admissions.clearance', $admission) }}">
                    @csrf
                    <input type="hidden" name="clearance" value="{{ $field }}">
                    <button class="ghost-button" type="submit">{{ $admission->{$field} ? $label . 'ed' : $label }}</button>
                </form>
            @endforeach
        </div>
        @if($bills->isNotEmpty())
            <p class="subtle">Bills: @foreach($bills as $bill)<a href="{{ route('billing.show', $bill) }}">#{{ $bill->id }} {{ ucfirst($bill->status) }} {{ number_format($bill->balance, 2) }} balance</a>{{ $loop->last ? '' : ' - ' }}@endforeach</p>
        @endif
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="section-title">Discharge summary</h2></div>
        @if($admission->dischargeSummary)
            <div class="detail-grid">
                <div class="detail-item field-span-2"><span class="detail-label">Final diagnosis</span><div class="detail-value">{{ $admission->dischargeSummary->final_diagnosis }}</div></div>
                <div class="detail-item field-span-2"><span class="detail-label">Follow-up</span><div class="detail-value">{{ $admission->dischargeSummary->follow_up_instructions ?: '-' }}</div></div>
            </div>
        @endif
        <form method="POST" action="{{ route('admissions.discharge', $admission) }}" class="form-grid">
            @csrf
            <div><label>Discharge type</label><select name="discharge_type">@foreach(['improved','referred','against_medical_advice','transferred','deceased','absconded'] as $type)<option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>@endforeach</select></div>
            <div><label>Follow-up date</label><input type="date" name="follow_up_date"></div>
            <div class="field-span-2"><label>Final diagnosis</label><textarea name="final_diagnosis" rows="2" required>{{ optional($admission->dischargeSummary)->final_diagnosis }}</textarea></div>
            <div class="field-span-2"><label>Condition on discharge</label><textarea name="condition_on_discharge" rows="2">{{ optional($admission->dischargeSummary)->condition_on_discharge }}</textarea></div>
            <div class="field-span-2"><label>Hospital course</label><textarea name="hospital_course" rows="3">{{ optional($admission->dischargeSummary)->hospital_course }}</textarea></div>
            <div class="field-span-2"><label>Treatment given</label><textarea name="treatment_given" rows="3">{{ optional($admission->dischargeSummary)->treatment_given }}</textarea></div>
            <div class="field-span-2"><label>Investigations</label><textarea name="investigations" rows="3">{{ optional($admission->dischargeSummary)->investigations }}</textarea></div>
            <div class="field-span-2"><label>Discharge medications</label><textarea name="discharge_medications" rows="3">{{ optional($admission->dischargeSummary)->discharge_medications }}</textarea></div>
            <div class="field-span-2"><label>Follow-up instructions</label><textarea name="follow_up_instructions" rows="3">{{ optional($admission->dischargeSummary)->follow_up_instructions }}</textarea></div>
            <div><button class="primary-button" type="submit">Finalize discharge</button></div>
        </form>
    </div>
@endsection
