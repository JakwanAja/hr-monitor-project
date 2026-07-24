@extends('layouts.app')

@section('title', 'Buat Tugas')
@section('page-title', 'Buat Tugas')
@section('page-subtitle', 'Distribusikan tugas harian kepada HR Staff dan HR Assistant')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        Tugas hari ini:
        <span class="font-semibold text-gray-700">{{ $tasks->count() }}</span> tugas
    </p>
    <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700
                   text-white text-sm font-medium rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Tugas
    </button>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Judul</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Deskripsi</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600 w-48">Penerima</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600 w-20">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tasks as $task)
                @php
                    $hasCompleted = $task->assignments->where('is_completed', 'completed')->count() > 0;
                    $hasNotDone   = $task->assignments->where('is_completed', 'not_done')->count() > 0;
                @endphp
                <tr class="hover:bg-gray-50 transition">

                    {{-- Judul --}}
                    <td class="px-6 py-4 w-48">
                        <div class="truncate max-w-[170px] font-medium text-gray-800"
                             title="{{ $task->title }}">
                            {{ $task->title }}
                        </div>
                    </td>

                    {{-- Deskripsi --}}
                    <td class="px-6 py-4">
                        <div class="truncate max-w-[220px] text-gray-500"
                             title="{{ $task->description ?? '-' }}">
                            {{ $task->description ?? '-' }}
                        </div>
                    </td>

                    {{-- Penerima --}}
                    <td class="px-6 py-4 w-48">
                        <div class="flex flex-wrap gap-1">
                            @foreach($task->assignedUsers as $assignee)
                                @php
                                    $assignment = $task->assignments->firstWhere('user_id', $assignee->id);
                                    $done       = $assignment?->is_completed === 'completed';
                                    $notDone    = $assignment?->is_completed === 'not_done';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             text-xs font-medium
                                             {{ $done ? 'bg-green-50 text-green-700' : ($notDone ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                    @if($done)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($notDone)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ $assignee->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 w-20">
                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                            @if(!$hasCompleted && !$hasNotDone)
                                <button
                                    onclick="openEditModal(
                                        {{ $task->id }},
                                        '{{ addslashes($task->title) }}',
                                        '{{ addslashes($task->description ?? '') }}',
                                        {{ json_encode($task->assignedUsers->pluck('id')) }}
                                    )"
                                    class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button
                                    onclick="openDeleteModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                    title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                <button
                                    onclick="openDetailModal({
                                        title: '{{ addslashes($task->title) }}',
                                        description: '{{ addslashes($task->description ?? '') }}',
                                        type: '{{ $task->type }}',
                                        date: '{{ $task->task_date->translatedFormat('d M Y') }}',
                                        source: '{{ addslashes($task->creator?->name ?? 'Sistem') }}',
                                        status: 'multiple',
                                        note: null,
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
                                            d="M2.458 12C3.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            @elseif($hasCompleted)
                                <span class="text-xs text-gray-400 italic">Terkunci</span>
                            @else
                                <span class="text-xs text-red-400 italic">Tidak Dikerjakan</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada tugas hari ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── MODAL TAMBAH ───────────────────────────────────── --}}
<div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-800">Buat Tugas</h3>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.tasks.store') }}" method="POST"
              class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi / Instruksi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Penerima Tugas <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($assignableUsers as $user)
                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                   class="h-4 w-4 text-primary-600 border-gray-300 rounded">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $user->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('user_ids')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white
                               text-sm font-medium rounded-lg transition">
                    Kirim Tugas
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT ─────────────────────────────────────── --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-base font-semibold text-gray-800">Edit Tugas</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="form-edit" action="" method="POST"
              class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" id="edit-title" name="title" required maxlength="200"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi / Instruksi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="edit-description" name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Penerima Tugas <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($assignableUsers as $user)
                        <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                   class="edit-user-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $user->role === 'hr_staff' ? 'HR Staff' : 'HR Assistant' }}
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white
                               text-sm font-medium rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL HAPUS ─────────────────────────────────────── --}}
<div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800 mb-1">Hapus Tugas</h3>
            <p class="text-sm text-gray-500 mb-6">
                Yakin ingin menghapus
                <span id="delete-task-title" class="font-semibold text-gray-700"></span>?
            </p>
            <form id="form-delete" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button"
                            onclick="document.getElementById('modal-delete').classList.add('hidden')"
                            class="px-5 py-2 text-sm text-gray-600 font-medium border border-gray-300 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white
                                   text-sm font-medium rounded-lg transition">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, title, description, currentUserIds) {
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-description').value = description;
        document.getElementById('form-edit').action = `/admin/tasks/${id}`;
        document.querySelectorAll('.edit-user-checkbox').forEach(cb => {
            cb.checked = currentUserIds.includes(parseInt(cb.value));
        });
        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function openDeleteModal(id, title) {
        document.getElementById('delete-task-title').textContent = title;
        document.getElementById('form-delete').action = `/admin/tasks/${id}`;
        document.getElementById('modal-delete').classList.remove('hidden');
    }
</script>

@endsection