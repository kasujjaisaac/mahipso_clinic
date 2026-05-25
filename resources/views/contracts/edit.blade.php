@extends('layouts.app')

@section('title', 'Edit Contract')
@section('page_title', 'Edit staff contract')
@section('page_subtitle', $contract->contract_no)

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('contracts.show', $contract) }}">Back to contract</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('contracts.update', $contract) }}">
        @method('PUT')
        @include('contracts.form')
    </form>
</div>
@endsection
