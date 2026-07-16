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

    {{-- ── SIDEBAR ─────────────────────────────────────── --}}
    <aside class="w-[260px] flex-shrink-0 bg-[#1C2434] flex flex-col h-full overflow-y-auto">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-white font-bold text-lg tracking-tight">HR-DWMS</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-5 space-y-1">
            @yield('sidebar')
        </nav>

        {{-- User Info + Logout --}}
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-primary-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-semibold text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">
                        {{ Auth::user()->name }}
                    </p>
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
                    @php $unread = Auth::user()->unreadNotifications->count(); @endphp
                    @if($unread > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white
                                     text-[10px] rounded-full flex items-center justify-center font-medium">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>

                {{-- Tanggal --}}
                <div class="text-xs text-gray-500 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error'))
            <div class="px-6 pt-4">
                @if(session('success'))
                    <div id="flash-success"
                         class="flex items-center gap-3 p-4 mb-2 bg-green-50 border border-green-200
                                text-green-700 rounded-xl text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                        <button onclick="document.getElementById('flash-success').remove()"
                                class="ml-auto text-green-500 hover:text-green-700 text-lg leading-none">✕</button>
                    </div>
                @endif
                @if(session('error'))
                    <div id="flash-error"
                         class="flex items-center gap-3 p-4 mb-2 bg-red-50 border border-red-200
                                text-red-700 rounded-xl text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-11.25a.75.75 0 011.5 0v4.5a.75.75 0 01-1.5 0v-4.5zm.75 7.5a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                        <button onclick="document.getElementById('flash-error').remove()"
                                class="ml-auto text-red-500 hover:text-red-700 text-lg leading-none">✕</button>
                    </div>
                @endif
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
        </main>

    </div>
</div>
</body>
</html>