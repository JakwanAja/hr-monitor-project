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
                <p class="text-sm font-medium text-gray-500">Selesai</p>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            <p class="text-xs text-gray-400 mt-1">sudah diselesaikan</p>
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
            <p class="text-xs text-gray-400 mt-1">masih dalam pengerjaan</p>
        </div>

        {{-- Tidak Dikerjakan 
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500">Tidak Dikerjakan</p>
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['not_done'] }}</p>
            <p class="text-xs text-gray-400 mt-1">melewati batas waktu</p>
        </div> --}}
    </div> 

    {{-- ── Ranking Minggu Ini ──────────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700">Ranking Minggu Ini</h2>
            <a href="{{ route('admin.reports.ranking') }}"
            class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua →
            </a>
        </div>

        <div class="grid grid-cols-3 gap-4">
            @forelse($rankings as $index => $item)
                @php
                    $rank = $index + 1;
                    $medal = match($rank) {
                        1 => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'icon' => 'text-yellow-500', 'score' => 'text-yellow-600'],
                        2 => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon' => 'text-gray-400', 'score' => 'text-gray-600'],
                        3 => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'text-amber-600', 'score' => 'text-amber-600'],
                        default => ['bg' => 'bg-white', 'border' => 'border-gray-200', 'icon' => 'text-gray-300', 'score' => 'text-gray-600'],
                    };
                @endphp
                <div class="{{ $medal['bg'] }} border {{ $medal['border'] }} rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        {{-- Medal Icon --}}
                        <svg class="w-7 h-7 {{ $medal['icon'] }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        {{-- Skor --}}
                        <div class="text-right">
                            <p class="text-2xl font-bold {{ $medal['score'] }}">{{ $item['score'] }}</p>
                            <p class="text-xs text-gray-400">poin</p>
                        </div>
                    </div>
                    {{-- User Info --}}
                    <div class="flex items-center gap-3 mt-2">
                        <div class="w-9 h-9 rounded-full bg-white border border-gray-200
                                    flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-gray-600">
                                {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">
                                {{ $item['user']->name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $item['user']->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white border border-gray-200 rounded-xl px-6 py-8 text-center">
                    <p class="text-sm text-gray-400">Belum ada data ranking minggu ini.</p>
                </div>
            @endforelse
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