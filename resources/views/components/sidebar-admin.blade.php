{{-- Dashboard --}}
<a href="{{ route('admin.dashboard') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

{{-- Manajemen Pengguna --}}
<a href="{{ route('admin.users.index') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    Manajemen Pengguna
</a>

{{-- Manajemen Tugas (collapsible) --}}
@php
    $taskActive = request()->routeIs('admin.tasks.*');
@endphp
<div x-data="{ open: {{ $taskActive ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                   {{ $taskActive ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span class="flex-1 text-left">Manajemen Tugas</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-transition class="mt-1 ml-4 pl-4 border-l border-white/10 space-y-1">
        {{-- Buat Tugas --}}
        <a href="{{ route('admin.tasks.index') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('admin.tasks.index') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Tugas
        </a>

        {{-- Tugas HR Staff --}}
        <a href="{{ route('admin.tasks.staff') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('admin.tasks.staff') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Tugas HR Staff
        </a>

        {{-- Tugas HR Assistant --}}
        <a href="{{ route('admin.tasks.assistant') }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all
                  {{ request()->routeIs('admin.tasks.assistant') ? 'text-white bg-white/10' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Tugas HR Assistant
        </a>
    </div>
</div>

{{-- Default Task --}}
<a href="{{ route('admin.default-tasks.index') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('admin.default-tasks.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
    Tugas Default
</a>

{{-- Divider --}}
<div class="pt-4 pb-2">
    <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-widest">Laporan</p>
</div>

{{-- Produktivitas --}}
<a href="{{ route('admin.reports.productivity') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('admin.reports.productivity') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    Produktivitas
</a>

{{-- Riwayat --}}
<a href="{{ route('admin.reports.history') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('admin.reports.history') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Riwayat Tugas
</a>

{{-- Ranking --}}
<a href="{{ route('admin.reports.ranking') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('admin.reports.ranking') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
    </svg>
    Ranking
</a>