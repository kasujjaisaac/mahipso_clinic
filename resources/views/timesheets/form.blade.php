@extends('layouts.app')

@php
    $isEdit = $timesheet->exists;
@endphp

@section('title', $isEdit ? 'Edit Timesheet' : 'New Timesheet')
@section('page_title', $isEdit ? 'Edit monthly timesheet' : 'New monthly timesheet')
@section('page_subtitle', 'Complete the daily work entries for the selected month.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('timesheets.index') }}">Back to timesheets</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ $isEdit ? route('timesheets.update', $timesheet) : route('timesheets.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="field">
                    <label>Name</label>
                    <input type="text" value="{{ auth()->user()->name }}" disabled>
                </div>

                <div class="field">
                    <label>Prepared date</label>
                    <input type="date" name="prepared_at" value="{{ old('prepared_at', optional($timesheet->prepared_at)->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                </div>

                <div class="field">
                    <label>Job title</label>
                    <input type="text" name="job_title" value="{{ old('job_title', $timesheet->job_title ?? auth()->user()->job_title) }}">
                    @error('job_title')<p class="subtle">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label>Month</label>
                    <input type="month" name="month" value="{{ old('month', optional($timesheet->month)->format('Y-m') ?? now()->format('Y-m')) }}" required>
                    @error('month')<p class="subtle">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label>Employee number</label>
                    <input type="text" name="employee_number" value="{{ old('employee_number', $timesheet->employee_number ?? auth()->user()->employee_number) }}">
                    @error('employee_number')<p class="subtle">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label>Line supervisor</label>
                    <input type="text" value="{{ optional(auth()->user()->lineSupervisor)->name ?? 'Not assigned' }}" disabled>
                </div>
            </div>

            <div class="table-wrap" style="margin-top: 1rem;">
                <table id="timesheet-entries">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Specification of work</th>
                            <th>Time start</th>
                            <th>Time finish</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $index => $entry)
                            @php
                                $day = is_array($entry) ? $entry['day'] : $entry->day;
                                $work = is_array($entry) ? ($entry['work_specification'] ?? '') : $entry->work_specification;
                                $start = is_array($entry) ? ($entry['time_start'] ?? '') : $entry->time_start;
                                $finish = is_array($entry) ? ($entry['time_finish'] ?? '') : $entry->time_finish;
                            @endphp
                            <tr>
                                <td>
                                    {{ $day }}
                                    <input type="hidden" name="entries[{{ $index }}][day]" value="{{ $day }}">
                                </td>
                                <td><input name="entries[{{ $index }}][work_specification]" value="{{ old("entries.$index.work_specification", $work) }}"></td>
                                <td><input class="time-start" type="time" name="entries[{{ $index }}][time_start]" value="{{ old("entries.$index.time_start", $start) }}" oninput="calculateTimesheetHours()"></td>
                                <td><input class="time-finish" type="time" name="entries[{{ $index }}][time_finish]" value="{{ old("entries.$index.time_finish", $finish) }}" oninput="calculateTimesheetHours()"></td>
                                <td class="hours-cell">0.00</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th colspan="4">Total hours worked</th><th id="total-hours">0.00</th></tr>
                    </tfoot>
                </table>
            </div>

            <div class="form-grid" style="margin-top: 1rem;">
                <div class="field field-span-2">
                    <label>Staff comments</label>
                    <textarea name="staff_comments">{{ old('staff_comments', $timesheet->staff_comments) }}</textarea>
                    @error('staff_comments')<p class="subtle">{{ $message }}</p>@enderror
                </div>

                <div></div>
                <div class="inline-actions">
                    <button class="ghost-button" type="submit" name="action" value="draft">Save draft</button>
                    <button class="primary-button" type="submit" name="action" value="submit">Submit to supervisor</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function calculateTimesheetHours() {
            let total = 0;
            document.querySelectorAll('#timesheet-entries tbody tr').forEach((row) => {
                const start = row.querySelector('.time-start').value;
                const finish = row.querySelector('.time-finish').value;
                let hours = 0;

                if (start && finish) {
                    const startDate = new Date(`2000-01-01T${start}:00`);
                    let finishDate = new Date(`2000-01-01T${finish}:00`);
                    if (finishDate < startDate) {
                        finishDate = new Date(`2000-01-02T${finish}:00`);
                    }
                    hours = (finishDate - startDate) / 36e5;
                }

                row.querySelector('.hours-cell').textContent = hours.toFixed(2);
                total += hours;
            });

            document.getElementById('total-hours').textContent = total.toFixed(2);
        }

        calculateTimesheetHours();
    </script>
@endsection
