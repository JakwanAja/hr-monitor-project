@extends('layouts.app')

@section('title', 'Riwayat Tugas')
@section('page-title', 'Riwayat Tugas')
@section('page-subtitle', 'Rekap historis tugas seluruh anggota tim')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('admin.reports.history') }}"
          class="flex flex-wrap items-center gap-3">

        {{-- Filter User --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Pengguna:</label>
            <select name="user_id"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        {{ $userId == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                        ({{ $user->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Tanggal --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Tanggal:</label>
            <input type="date" name="date" value="{{ $date ?? '' }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>

        <button type="submit"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white
                       text-sm font-medium rounded-lg transition">
            Filter
        </button>

        @if($date || $userId)
            <a href="{{ route('admin.reports.history') }}"
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
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Tanggal</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-44">Judul</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-36">Penerima</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Sumber</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Catatan</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Status</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-16">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @foreach($task->assignedUsers as $assignee)
                    @php
                        $assignment = $task->assignments->firstWhere('user_id', $assignee->id);
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            {{ $task->task_date->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4 w-44">
                            <div class="truncate max-w-[160px] font-medium text-gray-800"
                                title="{{ $task->title }}">
                                {{ $task->title }}
                            </div>
                        </td>
                        <td class="px-6 py-4 w-36">
                            <div class="truncate max-w-[120px] text-gray-700 text-xs font-medium"
                                title="{{ $assignee->name }}">
                                {{ $assignee->name }}
                            </div>
                            <p class="text-xs text-gray-400">
                                {{ $assignee->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 w-28">
                            @if($task->type === 'self')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                            text-xs font-medium bg-gray-100 text-gray-600">
                                    Mandiri
                                </span>
                            @elseif($task->type === 'assigned')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                            text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $task->creator?->name ?? 'Admin' }}
                                </span>
                            @elseif($task->type === 'default')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                            text-xs font-medium bg-purple-50 text-purple-700">
                                    Rutin
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="truncate max-w-[180px] text-gray-500 text-xs"
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
                                        assignees: {{ json_encode($task->assignedUsers->map(fn($u) => [
                                            'name'   => $u->name,
                                            'role'   => $u->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant',
                                            'status' => $task->assignments->firstWhere('user_id', $u->id)?->is_completed ?? 'pending',
                                        ])) }}
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
                @endforeach
            @empty  
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                        {{ $date || $userId ? 'Tidak ada data sesuai filter.' : 'Belum ada riwayat tugas.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection