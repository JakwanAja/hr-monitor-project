@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun dan keamanan Anda')

@section('sidebar')
    @if(Auth::user()->isAdmin())
        @include('components.sidebar-admin')
    @elseif(Auth::user()->isHrStaff())
        @include('components.sidebar-staff')
    @else
        @include('components.sidebar-assistant')
    @endif
@endsection

@section('content')

@php
    $user     = Auth::user();
    $roleLabel = match($user->role) {
        'admin'        => 'Administrator',
        'hr_staff'     => 'HR Staff',
        'hr_assistant' => 'HR Assistant',
        default        => $user->role,
    };
    $roleColor = match($user->role) {
        'admin'        => 'bg-red-50 text-red-700',
        'hr_staff'     => 'bg-blue-50 text-blue-700',
        'hr_assistant' => 'bg-purple-50 text-purple-700',
        default        => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- ── KARTU HEADER PROFIL ─────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-5">

            {{-- Avatar --}}
            <div class="relative flex-shrink-0" x-data="avatarPreview()">
                <div class="w-20 h-20 rounded-full overflow-hidden ring-4 ring-primary-100">
                    <img id="avatar-preview"
                         src="{{ $user->image ? asset('storage/' . $user->image) : '' }}"
                         alt="{{ $user->name }}"
                         class="{{ $user->image ? '' : 'hidden' }} w-full h-full object-cover">
                    <div id="avatar-initials"
                         class="{{ $user->image ? 'hidden' : '' }} w-full h-full bg-primary-600 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-gray-900 truncate">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</p>
                <span class="inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColor }}">
                    {{ $roleLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── FORM EDIT PROFIL ─────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Informasi Profil</h3>
            <p class="text-xs text-gray-500 mt-0.5">Perbarui nama, email, dan foto profil Anda</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
              class="px-6 py-5 space-y-5">
            @csrf
            @method('PATCH')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $user->name) }}"
                       required maxlength="100"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm transition
                              focus:outline-none focus:ring-2 focus:ring-primary-500
                              {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       required maxlength="100"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm transition
                              focus:outline-none focus:ring-2 focus:ring-primary-500
                              {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Foto Profil --}}
            <div x-data="{
                    hasPhoto: {{ $user->image ? 'true' : 'false' }},
                    removePhoto: false,
                    previewUrl: '{{ $user->image ? asset('storage/' . $user->image) : '' }}',
                    handleFile(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        this.previewUrl = URL.createObjectURL(file);
                        this.hasPhoto = true;
                        this.removePhoto = false;
                        // Sync global avatar preview
                        document.getElementById('avatar-preview').src = this.previewUrl;
                        document.getElementById('avatar-preview').classList.remove('hidden');
                        document.getElementById('avatar-initials').classList.add('hidden');
                    },
                    triggerRemove() {
                        this.removePhoto = true;
                        this.hasPhoto = false;
                        this.previewUrl = '';
                        document.getElementById('avatar-preview').classList.add('hidden');
                        document.getElementById('avatar-initials').classList.remove('hidden');
                        this.$refs.fileInput.value = '';
                    }
                }">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>

                {{-- Preview & kontrol foto --}}
                <div class="flex items-start gap-4">
                    {{-- Thumbnail preview --}}
                    <div class="w-16 h-16 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 flex-shrink-0">
                        <template x-if="hasPhoto && previewUrl">
                            <img :src="previewUrl" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!hasPhoto || !previewUrl">
                            <div class="w-full h-full bg-primary-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </template>
                    </div>

                    {{-- Tombol & info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium
                                          text-primary-600 border border-primary-300 hover:bg-primary-50
                                          rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Pilih Foto
                                <input type="file" name="image" x-ref="fileInput"
                                       accept="image/jpg,image/jpeg,image/png,image/webp"
                                       @change="handleFile($event)"
                                       class="hidden">
                            </label>

                            <button type="button"
                                    x-show="hasPhoto"
                                    @click="triggerRemove()"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium
                                           text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Foto
                            </button>
                        </div>

                        <p class="mt-1.5 text-xs text-gray-400">JPG, PNG, WebP. Maks 2 MB.</p>

                        {{-- Hidden field untuk sinyal hapus foto --}}
                        <input type="hidden" name="remove_image" :value="removePhoto ? '1' : '0'">
                    </div>
                </div>

                @error('image')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700
                               text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- ── FORM GANTI PASSWORD ──────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Ganti Password</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pastikan password baru minimal 8 karakter</p>
        </div>

        <form action="{{ route('profile.password') }}" method="POST"
              class="px-6 py-5 space-y-5">
            @csrf
            @method('PATCH')

            {{-- Password Lama --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password Saat Ini <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'"
                           name="current_password"
                           required
                           class="w-full px-4 py-2.5 pr-10 border rounded-lg text-sm transition
                                  focus:outline-none focus:ring-2 focus:ring-primary-500
                                  {{ $errors->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password Baru <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'"
                           name="password"
                           required minlength="8"
                           class="w-full px-4 py-2.5 pr-10 border rounded-lg text-sm transition
                                  focus:outline-none focus:ring-2 focus:ring-primary-500
                                  {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password Baru --}}
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'"
                           name="password_confirmation"
                           required
                           class="w-full px-4 py-2.5 pr-10 border rounded-lg text-sm transition
                                  focus:outline-none focus:ring-2 focus:ring-primary-500 border-gray-300">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600
                               text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Ganti Password
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
