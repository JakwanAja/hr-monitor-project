@extends('layouts.app')

@section('title', 'Progres HR Assistant')
@section('page-title', 'Progres HR Assistant')
@section('page-subtitle', 'Pantau aktivitas dan progres seluruh HR Assistant hari ini')

@section('sidebar')
    @include('components.sidebar-staff')
@endsection

@section('content')

{{-- Stat Cards Ringkasan --}}
@php
    $totalAssistants  = $assistants->count();
    $allDone          = $assistants->filter(fn($a) => $a->total_tasks > 0 && $a->completed_tasks === $a->total_tasks)->count();
    $totalTasksToday  = $assistants->sum('total_tasks');
    $totalCompleted   = $assistants->sum('completed_tasks');
@endphp

<div class="grid grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Total Assistant</p>
            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalAssistants }}</p>
        <p class="text-xs text-gray-400 mt-1">HR Assistant aktif</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Semua Selesai</p>
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-green-600">{{ $allDone }}</p>
        <p class="text-xs text-gray-400 mt-1">dari {{ $totalAssistants }} assistant</p>
    </div>

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
        <p class="text-3xl font-bold text-gray-800">{{ $totalTasksToday }}</p>
        <p class="text-xs text-gray-400 mt-1">assignment aktif</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Sudah Diselesaikan</p>
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-green-600">{{ $totalCompleted }}</p>
        <p class="text-xs text-gray-400 mt-1">
            {{ $totalTasksToday > 0 ? round(($totalCompleted / $totalTasksToday) * 100) : 0 }}% dari total
        </p>
    </div>
</div>

{{-- Detail per Assistant --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-700">Detail Progres per Assistant</h2>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Nama</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Selesai</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Total</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-24">Tidak</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Skor Minggu</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Skor Bulan</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Progres</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($assistants as $assistant)
                @php
                    $pct = $assistant->total_tasks > 0
                        ? round(($assistant->completed_tasks / $assistant->total_tasks) * 100)
                        : 0;
                    $barColor = $pct === 100
                        ? 'bg-green-500'
                        : ($pct >= 50 ? 'bg-primary-500' : 'bg-yellow-500');
                    $scoreWeek  = $scoreData[$assistant->id]['week'] ?? 0;
                    $scoreMonth = $scoreData[$assistant->id]['month'] ?? 0;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center
                                        justify-center flex-shrink-0">
                                <span class="text-xs font-semibold text-purple-600">
                                    {{ strtoupper(substr($assistant->name, 0, 1)) }}
                                </span>
                            </div>
                            <span class="font-medium text-gray-800">{{ $assistant->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 w-24">
                        <span class="font-semibold text-green-600">{{ $assistant->completed_tasks }}</span>
                    </td>
                    <td class="px-6 py-4 w-24">
                        <span class="text-gray-600">{{ $assistant->total_tasks }}</span>
                    </td>
                    <td class="px-6 py-4 w-24">
                        <span class="font-semibold text-red-500">{{ $assistant->not_done_tasks }}</span>
                    </td>
                    <td class="px-6 py-4 w-32">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                     text-xs font-medium bg-yellow-50 text-yellow-700">
                            ⭐ {{ $scoreWeek }} poin
                        </span>
                    </td>
                    <td class="px-6 py-4 w-32">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                     text-xs font-medium bg-purple-50 text-purple-700">
                            ⭐ {{ $scoreMonth }} poin
                        </span>
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
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Tidak ada HR Assistant aktif.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection