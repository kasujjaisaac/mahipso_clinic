@extends('layouts.app')

@section('title', 'Ward Details')
@section('section', 'Inpatient Management')
@section('kicker', 'Ward')
@section('page_title', $ward->name)
@section('page_subtitle', 'Manage ward details, bed statuses, and current occupancy.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('admissions.create') }}">Admit patient</a>
    <a class="ghost-button" href="{{ route('wards.index') }}">Bed board</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('wards.update', $ward) }}" class="form-grid">
            @csrf
            @method('PUT')
            <div><label>Name</label><input name="name" value="{{ old('name', $ward->name) }}" required></div>
            <div><label>Code</label><input name="code" value="{{ old('code', $ward->code) }}"></div>
            <div><label>Type</label><select name="type">@foreach(['medical','surgical','maternity','pediatric','icu','isolation','observation','other'] as $type)<option value="{{ $type }}" @selected(old('type', $ward->type) === $type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
            <div><label>Gender restriction</label><select name="gender_restriction">@foreach(['none','male','female'] as $gender)<option value="{{ $gender }}" @selected(old('gender_restriction', $ward->gender_restriction) === $gender)>{{ ucfirst($gender) }}</option>@endforeach</select></div>
            <div><label><input type="checkbox" name="is_active" value="1" @checked($ward->is_active)> Active</label></div>
            <div><button class="primary-button" type="submit">Update ward</button></div>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="section-title">Add bed</h2></div>
        <form method="POST" action="{{ route('wards.beds.store', $ward) }}" class="toolbar-form">
            @csrf
            <input name="bed_number" placeholder="Bed number" required>
            <select name="status"><option value="available">Available</option><option value="reserved">Reserved</option><option value="cleaning">Cleaning</option><option value="maintenance">Maintenance</option></select>
            <input name="notes" placeholder="Notes">
            <button class="primary-button" type="submit">Add bed</button>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header"><h2 class="section-title">Beds</h2></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Bed</th><th>Status</th><th>Patient</th><th>Notes</th><th>Update</th></tr></thead>
                <tbody>
                    @foreach($ward->beds as $bed)
                        <tr>
                            <td>{{ $bed->bed_number }}</td>
                            <td><span class="status-pill {{ $bed->status }}">{{ ucfirst($bed->status) }}</span></td>
                            <td>
                                @if($bed->currentAdmission)
                                    <a href="{{ route('admissions.show', $bed->currentAdmission) }}">{{ $bed->currentAdmission->patient->full_name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $bed->notes ?: '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('beds.update', $bed) }}" class="toolbar-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status">
                                        @foreach(['available','reserved','cleaning','maintenance','occupied'] as $status)
                                            <option value="{{ $status }}" @selected($bed->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <input name="notes" value="{{ $bed->notes }}" placeholder="Notes">
                                    <button class="ghost-button" type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
