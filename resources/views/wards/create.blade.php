@extends('layouts.app')

@section('title', 'Create Ward')
@section('section', 'Inpatient Management')
@section('kicker', 'Ward Setup')
@section('page_title', 'New ward')
@section('page_subtitle', 'Create a ward and optionally generate its initial beds.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('wards.index') }}">Back to bed board</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('wards.store') }}" class="form-grid">
            @csrf
            <div>
                <label>Branch</label>
                <select name="branch_id" required>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<p class="subtle">{{ $message }}</p>@enderror
            </div>
            <div><label>Name</label><input name="name" value="{{ old('name') }}" required>@error('name')<p class="subtle">{{ $message }}</p>@enderror</div>
            <div><label>Code</label><input name="code" value="{{ old('code') }}"></div>
            <div>
                <label>Type</label>
                <select name="type">@foreach(['medical','surgical','maternity','pediatric','icu','isolation','observation','other'] as $type)<option value="{{ $type }}" @selected(old('type', 'medical') === $type)>{{ ucfirst($type) }}</option>@endforeach</select>
            </div>
            <div>
                <label>Gender restriction</label>
                <select name="gender_restriction">@foreach(['none','male','female'] as $gender)<option value="{{ $gender }}" @selected(old('gender_restriction', 'none') === $gender)>{{ ucfirst($gender) }}</option>@endforeach</select>
            </div>
            <div><label>Initial bed count</label><input type="number" min="0" max="200" name="bed_count" value="{{ old('bed_count', 0) }}"></div>
            <div><label><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
            <div><button class="primary-button" type="submit">Create ward</button></div>
        </form>
    </div>
@endsection
