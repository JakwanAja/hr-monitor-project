@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')
    <p class="text-gray-500 text-sm">Staff Dashboard — Phase 3 coming soon.</p>
@endsection