@extends('layouts.app')

@section('title', 'New Department')
@section('page_title', 'New department')
@section('page_subtitle', 'Create a department that can be assigned to staff records.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('departments.index') }}">Back to departments</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('departments.store') }}">
        @include('departments.form')
    </form>
</div>
@endsection
