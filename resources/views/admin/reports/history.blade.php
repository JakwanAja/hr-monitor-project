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
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @foreach($task->assignedUsers as $assignee)
                    @php
                        $assignment = $task->assignments->firstWhere('user_id', $assignee->id);
                        $isDone     = $assignment?->is_completed;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            {{ $task->task_date->translatedFormat('d M Y') }}
                        </td>

                        {{-- Judul --}}
                        <td class="px-6 py-4 w-44">
                            <div class="truncate max-w-[160px] font-medium text-gray-800"
                                 title="{{ $task->title }}">
                                {{ $task->title }}
                            </div>
                        </td>

                        {{-- Penerima --}}
                        <td class="px-6 py-4 w-36">
                            <div class="truncate max-w-[120px] text-gray-700 text-xs font-medium"
                                 title="{{ $assignee->name }}">
                                {{ $assignee->name }}
                            </div>
                            <p class="text-xs text-gray-400">
                                {{ $assignee->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }}
                            </p>
                        </td>

                        {{-- Sumber --}}
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

                        {{-- Catatan --}}
                        <td class="px-6 py-4">
                            <div class="truncate max-w-[180px] text-gray-500 text-xs"
                                 title="{{ $assignment?->note ?? '-' }}">
                                {{ $assignment?->note ?? '-' }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 w-28">
                            @if($isDone)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                             text-xs font-medium bg-green-50 text-green-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-xs font-medium bg-yellow-50 text-yellow-700">
                                    Belum Selesai
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                        {{ $date || $userId ? 'Tidak ada data sesuai filter.' : 'Belum ada riwayat tugas.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection