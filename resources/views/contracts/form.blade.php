@csrf
<div class="form-grid">
    <div class="field">
        <label>Employee</label>
        <select name="employee_id" required>
            <option value="">Select employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ old('employee_id', $contract->employee_id) == $employee->id ? 'selected' : '' }}>
                    {{ $employee->employee_no }} - {{ $employee->first_name }} {{ $employee->last_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Contract No</label>
        <input name="contract_no" value="{{ old('contract_no', $contract->contract_no) }}" required>
    </div>
    <div class="field">
        <label>Contract Type</label>
        <select name="contract_type" required>
            @foreach(['permanent', 'fixed_term', 'part_time', 'volunteer', 'consultant'] as $type)
                <option value="{{ $type }}" {{ old('contract_type', $contract->contract_type) === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Job Title</label>
        <input name="job_title" value="{{ old('job_title', $contract->job_title) }}">
    </div>
    <div class="field">
        <label>Start Date</label>
        <input type="date" name="start_date" value="{{ old('start_date', optional($contract->start_date)->format('Y-m-d') ?: $contract->start_date) }}" required>
    </div>
    <div class="field">
        <label>End Date</label>
        <input type="date" name="end_date" value="{{ old('end_date', optional($contract->end_date)->format('Y-m-d')) }}">
    </div>
    <div class="field">
        <label>Salary Amount</label>
        <input type="number" step="0.01" min="0" name="salary_amount" value="{{ old('salary_amount', $contract->salary_amount ?? 0) }}" required>
    </div>
    <div class="field">
        <label>Status</label>
        <select name="status" required>
            @foreach(['draft', 'active', 'expired', 'terminated'] as $status)
                <option value="{{ $status }}" {{ old('status', $contract->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Signed Date</label>
        <input type="date" name="signed_at" value="{{ old('signed_at', optional($contract->signed_at)->format('Y-m-d')) }}">
    </div>
    <div class="field field-span-2">
        <label>Terms</label>
        <textarea name="terms">{{ old('terms', $contract->terms) }}</textarea>
    </div>
</div>
<button class="primary-button" type="submit">Save contract</button>
