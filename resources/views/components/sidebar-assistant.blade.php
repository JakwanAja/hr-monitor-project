{{-- Dashboard --}}
<a href="{{ route('assistant.dashboard') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

{{-- Tugas Hari Ini --}}
<a href="{{ route('assistant.tasks.index') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.tasks.index') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
    </svg>
    Tugas Hari Ini
</a>        

{{-- Riwayat --}}
<a href="{{ route('assistant.tasks.history') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all
          {{ request()->routeIs('assistant.tasks.history') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    Riwayat Tugas
</a>