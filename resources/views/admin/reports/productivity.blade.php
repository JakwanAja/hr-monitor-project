@extends('layouts.app')

@section('title', 'Laporan Produktivitas')
@section('page-title', 'Laporan Produktivitas')
@section('page-subtitle', 'Rekap penyelesaian tugas harian per pengguna')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

{{-- Filter Tanggal --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('admin.reports.productivity') }}"
          class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700">Tanggal:</label>
        <input type="date" name="date" value="{{ $date }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary-500">
        <button type="submit"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white
                       text-sm font-medium rounded-lg transition">
            Tampilkan
        </button>
    </form>
</div>

{{-- Laporan Per User --}}
<div class="space-y-4">
    @foreach($report as $item)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Header User --}}
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
                        <span class="text-sm font-semibold text-primary-600">
                            {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $item['user']->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $item['user']->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $item['total'] }}</p>
                        <p class="text-xs text-gray-400">Total</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-green-600">{{ $item['completed'] }}</p>
                        <p class="text-xs text-gray-400">Selesai</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-yellow-600">{{ $item['pending'] }}</p>
                        <p class="text-xs text-gray-400">Pending</p>
                    </div>
                    @php
                        $pct = $item['total'] > 0
                            ? round(($item['completed'] / $item['total']) * 100)
                            : 0;
                    @endphp
                    <div class="text-center">
                        <p class="text-lg font-bold
                            {{ $pct === 100 ? 'text-green-600' : ($pct >= 50 ? 'text-primary-600' : 'text-yellow-600') }}">
                            {{ $pct }}%
                        </p>
                        <p class="text-xs text-gray-400">Progress</p>
                    </div>
                </div>
            </div>

            {{-- Detail Tugas --}}
            @if($item['tasks']->count() > 0)
                <div class="divide-y divide-gray-50">
                    @foreach($item['tasks'] as $assignment)
                        <div class="px-6 py-3 flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                        {{ $assignment->is_completed ? 'border-green-500 bg-green-500' : 'border-gray-300' }}">
                                @if($assignment->is_completed)
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 flex-1
                                      {{ $assignment->is_completed ? 'line-through text-gray-400' : '' }}">
                                {{ $assignment->task?->title ?? '-' }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $assignment->task?->type === 'self'
                                    ? 'bg-gray-100 text-gray-600'
                                    : ($assignment->task?->type === 'default'
                                        ? 'bg-purple-50 text-purple-700'
                                        : 'bg-blue-50 text-blue-700') }}">
                                {{ match($assignment->task?->type) {
                                    'self'    => 'Mandiri',
                                    'default' => 'Rutin',
                                    default   => 'Ditugaskan',
                                } }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-4 text-sm text-gray-400 text-center">
                    Tidak ada tugas pada tanggal ini.
                </div>
            @endif
        </div>
    @endforeach
</div>

@endsection