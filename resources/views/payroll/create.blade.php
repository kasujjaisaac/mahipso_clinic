@extends('layouts.app')

@section('title', 'Prepare Payroll')
@section('page_title', 'Prepare payroll')
@section('page_subtitle', 'Create a monthly payroll run from active employees and active contract salaries.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('payroll.index') }}">Back to payroll</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('payroll.store') }}">
        @csrf
        <div class="form-grid">
            <div class="field">
                <label>Payroll Month</label>
                <input type="month" name="period_month" value="{{ old('period_month', now()->format('Y-m')) }}" required>
            </div>
            <div class="field">
                <label>Branch</label>
                <select name="branch_id">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field field-span-2">
                <label>Notes</label>
                <textarea name="notes">{{ old('notes') }}</textarea>
            </div>
        </div>
        <button class="primary-button" type="submit">Create payroll run</button>
    </form>
</div>
@endsection
