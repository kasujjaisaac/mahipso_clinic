@extends('layouts.app')

@section('title', 'New Contract')
@section('page_title', 'New staff contract')
@section('page_subtitle', 'Record contract type, dates, salary, and employment terms.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('contracts.index') }}">Back to contracts</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('contracts.store') }}">
        @include('contracts.form')
    </form>
</div>
@endsection
