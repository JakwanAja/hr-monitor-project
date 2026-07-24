@extends('layouts.app')

@section('title', 'Tugas dari Atasan')
@section('page-title', 'Tugas dari Atasan')
@section('page-subtitle', 'Tugas yang diberikan Admin dan HR Staff kepada kamu hari ini')

@section('sidebar')
    @include('components.sidebar-assistant')
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Total: <span class="font-semibold text-gray-700">{{ $tasks->count() }}</span> tugas
    </p>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Judul</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-36">Dari</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-28">Status</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-20">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @php
                    $assignment  = $task->assignments->first();
                    $status      = $assignment?->is_completed ?? 'pending';
                    $isFromAdmin = $task->creator?->role === 'admin';
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 w-48">
                        <div class="truncate max-w-[170px] font-medium text-gray-800"
                             title="{{ $task->title }}">
                            {{ $task->title }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="truncate max-w-[200px] text-gray-500"
                             title="{{ $task->description ?? '-' }}">
                            {{ $task->description ?? '-' }}
                        </div>
                    </td>

                    {{-- Kolom Dari --}}
                    <td class="px-6 py-4 w-36">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                     text-xs font-medium
                                     {{ $isFromAdmin ? 'bg-primary-50 text-primary-700' : 'bg-blue-50 text-blue-700' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $task->creator?->name ?? '-' }}
                        </span>
                        <p class="text-xs text-gray-400 mt-0.5 pl-1">
                            {{ $isFromAdmin ? 'Admin' : 'HR Staff' }}
                        </p>
                    </td>

                    {{-- Status --}}
                    <td class="px-6 py-4 w-28">
                        <x-task-status-badge :status="$status" />
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 w-20">
                        <div class="flex items-center justify-end whitespace-nowrap">
                            @if($status === 'pending')
                                <button
                                    onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                    title="Tandai Selesai">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            @elseif($status === 'completed')
                                <span class="text-xs text-gray-400 italic">Selesai</span>
                            @else
                                <span class="text-xs text-red-400 italic">Tidak Dikerjakan</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Tidak ada tugas dari atasan hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Checklist --}}
<div id="modal-complete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Tandai Selesai</h3>
                <p id="complete-task-title" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('modal-complete').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-complete" action="" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan Penyelesaian
                    <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="note" rows="4"
                          placeholder="Tuliskan laporan singkat atau catatan penyelesaian tugas ini..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                <p class="mt-1 text-xs text-gray-400">
                    Setelah ditandai selesai, tugas tidak dapat diubah kembali.
                </p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-complete').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700
                               text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Selesai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCompleteModal(id, title) {
        document.getElementById('complete-task-title').textContent = title;
        document.getElementById('form-complete').action = `/assistant/tasks/assigned/${id}/complete`;
        document.getElementById('modal-complete').classList.remove('hidden');
    }
</script>

@endsection