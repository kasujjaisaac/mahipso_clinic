@csrf
<div class="form-grid">
    <div class="field">
        <label>Employee</label>
        <select name="employee_id" required>
            <option value="">Select employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ old('employee_id', $appraisal->employee_id) == $employee->id ? 'selected' : '' }}>
                    {{ $employee->employee_no }} - {{ $employee->first_name }} {{ $employee->last_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Period Start</label>
        <input type="date" name="period_start" value="{{ old('period_start', optional($appraisal->period_start)->format('Y-m-d') ?: $appraisal->period_start) }}" required>
    </div>
    <div class="field">
        <label>Period End</label>
        <input type="date" name="period_end" value="{{ old('period_end', optional($appraisal->period_end)->format('Y-m-d') ?: $appraisal->period_end) }}" required>
    </div>
    <div class="field">
        <label>Score</label>
        <input type="number" step="0.01" min="0" max="100" name="score" value="{{ old('score', $appraisal->score) }}">
    </div>
    <div class="field">
        <label>Rating</label>
        <input name="rating" value="{{ old('rating', $appraisal->rating) }}" placeholder="Excellent, Good, Needs improvement">
    </div>
    <div class="field">
        <label>Status</label>
        <select name="status" required>
            @foreach(['draft', 'completed', 'acknowledged'] as $status)
                <option value="{{ $status }}" {{ old('status', $appraisal->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Reviewed Date</label>
        <input type="date" name="reviewed_at" value="{{ old('reviewed_at', optional($appraisal->reviewed_at)->format('Y-m-d') ?: $appraisal->reviewed_at) }}">
    </div>
    <div class="field field-span-2">
        <label>Strengths</label>
        <textarea name="strengths">{{ old('strengths', $appraisal->strengths) }}</textarea>
    </div>
    <div class="field field-span-2">
        <label>Improvement Areas</label>
        <textarea name="improvement_areas">{{ old('improvement_areas', $appraisal->improvement_areas) }}</textarea>
    </div>
    <div class="field field-span-2">
        <label>Goals</label>
        <textarea name="goals">{{ old('goals', $appraisal->goals) }}</textarea>
    </div>
</div>
<button class="primary-button" type="submit">Save appraisal</button>
