<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HR-DWMS') — HR-DWMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F1F5F9] font-sans antialiased h-full">
<div class="flex h-screen overflow-hidden">
    <aside class="w-[260px] flex-shrink-0 bg-[#1C2434] flex flex-col h-full overflow-y-auto">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <img src="{{ asset('images/seveninc_logo.png') }}" alt="Seveninc Logo" class="w-9 h-9 object-contain flex-shrink-0">
            <span class="text-white font-bold text-2xl tracking-tight">Seven Inc</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-5 space-y-1">
            @yield('sidebar')
        </nav>

        {{-- User Info + Logout --}}
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                {{-- Avatar: klik → ke halaman profil --}}
                <a href="{{ route('profile.show') }}"
                   class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-transparent
                          hover:ring-primary-400 transition"
                   title="Lihat Profil">
                    @if(Auth::user()->image)
                        <img src="{{ asset('storage/' . Auth::user()->image) }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-primary-600 flex items-center justify-center">
                            <span class="text-sm font-semibold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </a>

                <div class="flex-1 min-w-0">
                    <a href="{{ route('profile.show') }}"
                       class="block text-sm font-semibold text-white truncate hover:text-primary-300 transition">
                        {{ Auth::user()->name }}
                    </a>
                    <p class="text-xs text-slate-400 truncate">
                        {{ match(Auth::user()->role) {
                            'admin'        => 'Administrator',
                            'hr_staff'     => 'HR Staff',
                            'hr_assistant' => 'HR Assistant',
                            default        => Auth::user()->role,
                        } }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="text-slate-400 hover:text-red-400 transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN AREA ────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0 shadow-sm">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">
                    @yield('page-title', 'Dashboard')
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    @yield('page-subtitle', '')
                </p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Notifikasi --}}
                <a href="{{ route('notifications.index') }}"
                class="relative p-2 text-gray-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @php
                    try {
                        $unread = Auth::user()->unreadNotifications->count();
                    } catch (\Exception $e) {
                        $unread = 0;
                    }
                @endphp
                @if($unread > 0)
                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white
                                text-[10px] rounded-full flex items-center justify-center font-medium">
                        {{ $unread > 9 ? '9+' : $unread }}
                    </span>
                @endif
                </a>

                {{-- Tanggal --}}
                <div class="text-xs text-gray-500 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 leading-tight text-center">
                    <div>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('D, d M Y') }}</div>
                </div>

                {{-- Divider --}}
                <div class="w-px h-6 bg-gray-200"></div>

                {{-- Avatar Topbar → Profil --}}
                <a href="{{ route('profile.show') }}"
                   title="Profil Saya"
                   class="flex items-center gap-2.5 pl-1 hover:opacity-80 transition">
                    <div class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-gray-200 flex-shrink-0">
                        @if(Auth::user()->image)
                            <img src="{{ asset('storage/' . Auth::user()->image) }}"
                                 alt="{{ Auth::user()->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-primary-600 flex items-center justify-center">
                                <span class="text-xs font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="hidden sm:block leading-tight">
                        <p class="text-xs font-semibold text-gray-700 truncate max-w-[100px]">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-400">
                            {{ match(Auth::user()->role) {
                                'admin'        => 'Administrator',
                                'hr_staff'     => 'HR Staff',
                                'hr_assistant' => 'HR Assistant',
                                default        => Auth::user()->role,
                            } }}
                        </p>
                    </div>
                </a>
            </div>
        </header>

        {{-- Toast Pop-up (success / error) --}}
        @include('components.toast')

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
        </main>

    </div>
    @include('components.task-detail-modal')
</div>
</body>
</html>