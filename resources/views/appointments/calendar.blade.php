@extends('layouts.app')

@section('title', 'Appointments Calendar')
@section('section', 'Scheduling')
@section('kicker', 'Appointment Calendar')
@section('page_title', 'Appointments Calendar')
@section('page_subtitle', 'Visualize and manage all appointments in a calendar view.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('appointments.index') }}">Back to list</a>
@endsection

@section('content')
<div class="panel">
    <div id="calendar"></div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar.io -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: @json($events),
        eventClick: function(info) {
            window.location.href = '/appointments/' + info.event.id;
        },
        eventClassNames: function(arg) {
            return 'status-' + arg.event.extendedProps.status;
        },
        eventDidMount: function(info) {
            var tooltip = info.event.title + ' \n' + info.event.extendedProps.service_type + ' • ' + info.event.extendedProps.status_label;
            info.el.setAttribute('title', tooltip);
        }
    });
    calendar.render();
});
</script>
<style>
    #calendar { max-width: 100%; margin: 0 auto; background: #fff; border-radius: 4px; box-shadow: var(--shadow); padding: 1rem; }
    .fc-event.status-completed { background: #22c55e !important; border: none; }
    .fc-event.status-canceled, .fc-event.status-no_show { background: #ef4444 !important; border: none; }
    .fc-event.status-scheduled, .fc-event.status-confirmed { background: #3b82f6 !important; border: none; }
    .fc-event.status-checked_in { background: #f59e42 !important; border: none; }
</style>
@endpush
