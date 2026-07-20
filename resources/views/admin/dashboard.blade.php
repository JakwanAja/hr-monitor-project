@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas tim HR hari ini')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')
    @include('components.notification-popup')

    {{-- ── Stat Cards ──────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-5 mb-8">

        {{-- Total Tugas --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Total Tugas Hari Ini</p>
                <div class="w-9 h-9 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">assignment aktif hari ini</p>
        </div>

        {{-- Selesai --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Tugas Selesai</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}% dari total
            </p>
        </div>

        {{-- Belum Selesai --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Belum Selesai</p>
                <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">masih perlu diselesaikan</p>
        </div>
    </div>

    {{-- ── Progress Per User ───────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-700">Progres Tim Hari Ini</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Role</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Selesai</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Total</th>
                    <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Progress</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($perUser as $user)
                    @php
                        $pct = $user->total_tasks > 0
                            ? round(($user->completed_tasks / $user->total_tasks) * 100)
                            : 0;
                        $barColor = $pct === 100
                            ? 'bg-green-500'
                            : ($pct >= 50 ? 'bg-primary-500' : 'bg-yellow-500');
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center
                                            justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-primary-600">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-28">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $user->role === 'hr_staff' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ $user->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 w-24">
                            <span class="font-semibold text-green-600">{{ $user->completed_tasks }}</span>
                        </td>
                        <td class="px-6 py-4 w-24">
                            <span class="text-gray-600">{{ $user->total_tasks }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-600 w-8">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada data pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection