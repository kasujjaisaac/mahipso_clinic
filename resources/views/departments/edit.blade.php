@extends('layouts.app')

@section('title', 'Edit Department')
@section('page_title', 'Edit department')
@section('page_subtitle', $department->name)

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('departments.show', $department) }}">Back to department</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('departments.update', $department) }}">
        @method('PUT')
        @include('departments.form')
    </form>
</div>
@endsection
