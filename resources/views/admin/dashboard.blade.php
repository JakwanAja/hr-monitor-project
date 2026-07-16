@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')
    <p class="text-gray-500 text-sm">Admin Dashboard — Phase 2 coming soon.</p>
@endsection