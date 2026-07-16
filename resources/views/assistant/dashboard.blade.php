@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('sidebar')
    @include('components.sidebar-assistant')
@endsection

@section('content')
    <p class="text-gray-500 text-sm">Assistant Dashboard — Phase 3 coming soon.</p>
@endsection