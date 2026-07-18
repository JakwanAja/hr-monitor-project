@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('sidebar')
    @include('components.sidebar-assistant')
@endsection

@section('content')
    @include('components.notification-popup')
    <p class="text-gray-500 text-sm">Assistant Dashboard — Phase 5 coming soon.</p>
@endsection