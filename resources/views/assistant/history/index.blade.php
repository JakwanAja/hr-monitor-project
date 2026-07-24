@extends('layouts.app')

@section('title', 'Riwayat Tugas')
@section('page-title', 'Riwayat Tugas')
@section('page-subtitle', 'Rekap seluruh tugas harian kamu')

@section('sidebar')
    @include('components.sidebar-assistant')
@endsection

@section('content')

{{-- Filter Tanggal --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('assistant.tasks.history') }}"
          class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700">Filter Tanggal:</label>
        <input type="date" name="date" value="{{ $date ?? '' }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm
                      focus:outline-none focus:ring-2 focus:ring-primary-500">
        <button type="submit"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white
                       text-sm font-medium rounded-lg transition">
            Filter
        </button>
        @if($date)
            <a href="{{ route('assistant.tasks.history') }}"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800
                      border border-gray-300 rounded-lg transition">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Tanggal</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Judul</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-32">Sumber</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Catatan</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Status</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-16">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @php
                    $assignment = $task->assignments->first();
                @endphp
                <tr class="hover:bg-gray-50 transition">

                    {{-- Tanggal --}}
                    <td class="px-6 py-4 w-32 text-gray-500 text-xs">
                        {{ $task->task_date->translatedFormat('d M Y') }}
                    </td>

                    {{-- Judul --}}
                    <td class="px-6 py-4 w-48">
                        <div class="truncate max-w-[170px] font-medium text-gray-800"
                             title="{{ $task->title }}">
                            {{ $task->title }}
                        </div>
                    </td>

                    {{-- Sumber --}}
                    <td class="px-6 py-4 w-32">
                        @if($task->type === 'self')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                         text-xs font-medium bg-gray-100 text-gray-600">
                                Mandiri
                            </span>
                        @elseif($task->type === 'assigned')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                         text-xs font-medium bg-blue-50 text-blue-700"
                                  title="Dari: {{ $task->creator?->name ?? 'Admin' }}">
                                {{ $task->creator?->name ?? 'Admin' }}
                            </span>
                        @elseif($task->type === 'default')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                         text-xs font-medium bg-purple-50 text-purple-700">
                                Rutin
                            </span>
                        @endif
                    </td>

                    {{-- Catatan --}}
                    <td class="px-6 py-4">
                        <div class="truncate max-w-[220px] text-gray-500 text-xs"
                             title="{{ $assignment?->note ?? '-' }}">
                            {{ $assignment?->note ?? '-' }}
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-6 py-4 w-28">
                        <x-task-status-badge :status="$assignment?->is_completed ?? 'pending'" />
                    </td>

                    {{-- Detail --}}
                    <td class="px-6 py-4 w-16">
                        <div class="flex justify-end">
                            <button
                                onclick="openDetailModal({
                                    title: '{{ addslashes($task->title) }}',
                                    description: '{{ addslashes($task->description ?? '') }}',
                                    type: '{{ $task->type }}',
                                    date: '{{ $task->task_date->translatedFormat('d M Y') }}',
                                    source: '{{ addslashes($task->creator?->name ?? 'Sistem') }}',
                                    status: '{{ $assignment?->is_completed ?? 'pending' }}',
                                    note: '{{ addslashes($assignment?->note ?? '') }}',
                                    assignees: []
                                })"
                                class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                title="Lihat Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                        {{ $date ? 'Tidak ada tugas pada tanggal tersebut.' : 'Belum ada riwayat tugas.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection