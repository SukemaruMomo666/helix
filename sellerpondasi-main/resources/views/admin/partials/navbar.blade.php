{{-- ==============================================================================
     GOD-TIER ADMIN NAVBAR (resources/views/admin/partials/navbar.blade.php)
     Dilengkapi Tema Dinamis, Glassmorphism, & Animasi Mulus (True Black OLED)
     ============================================================================== --}}

<div class="flex-1 flex justify-between items-center w-full h-full">

    {{-- BAGIAN KIRI: Judul Halaman & Indikator Sistem --}}
    <div class="flex items-center gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white tracking-tight leading-none m-0 transition-colors duration-500">
                @yield('title', 'Command Center')
            </h2>
            <div class="hidden md:flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mt-1.5 opacity-90">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)] dark:shadow-[0_0_12px_rgba(52,211,153,0.9)]"></span>
                </span>
                System Secured
            </div>
        </div>
    </div>

    {{-- BAGIAN KANAN: Fitur & Profil --}}
    <div class="flex items-center gap-3 sm:gap-5" x-data="{ profileOpen: false }">

        {{-- Search Bar (Desktop) --}}
        <div class="relative group hidden md:block">
            <i class="mdi mdi-magnify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 dark:text-slate-500 dark:group-focus-within:text-white transition-colors text-lg"></i>
            <input type="text"
                   class="w-48 lg:w-72 pl-11 pr-14 py-2.5 bg-slate-100/50 dark:bg-[#0a0a0a]/50 border border-slate-200/60 dark:border-white/[0.05] text-sm text-slate-700 dark:text-slate-200 rounded-full focus:bg-white dark:focus:bg-[#000000] focus:border-blue-400 dark:focus:border-white/[0.15] focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-white/[0.05] outline-none transition-all duration-300 font-semibold placeholder:text-slate-400 dark:placeholder:text-slate-500 shadow-inner dark:shadow-none backdrop-blur-sm"
                   placeholder="Lacak ID, Log...">
            <kbd class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-black bg-white dark:bg-white/[0.05] text-slate-400 dark:text-slate-300 px-1.5 py-0.5 rounded shadow-sm dark:shadow-none border border-slate-200 dark:border-white/[0.1] uppercase tracking-wider transition-colors duration-300">Ctrl+K</kbd>
        </div>

        {{-- Toggle Theme Button --}}
        <button @click="darkMode = !darkMode" class="relative w-10 h-10 rounded-full bg-white dark:bg-[#0a0a0a] border border-slate-200 dark:border-white/[0.05] text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:text-slate-400 dark:hover:text-amber-400 dark:hover:bg-white/[0.05] dark:hover:border-white/[0.1] transition-all duration-300 flex items-center justify-center outline-none shadow-sm dark:shadow-none hover:shadow-md hover:scale-105 active:scale-95 group overflow-hidden">
            <i class="mdi mdi-white-balance-sunny text-xl text-amber-500 absolute transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" :class="darkMode ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 rotate-90 scale-50'"></i>
            <i class="mdi mdi-weather-night text-xl text-indigo-400 absolute transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" :class="!darkMode ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-90 scale-50'"></i>
        </button>

        {{-- Notification Button --}}
        <button class="relative w-10 h-10 rounded-full bg-white dark:bg-[#0a0a0a] border border-slate-200 dark:border-white/[0.05] text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/[0.05] dark:hover:border-white/[0.1] transition-all duration-300 flex items-center justify-center outline-none shadow-sm dark:shadow-none hover:shadow-md hover:scale-105 active:scale-95 group">
            <i class="mdi mdi-bell-outline text-xl group-hover:animate-wiggle"></i>
            <span class="absolute top-0 right-0 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500 border-2 border-white dark:border-[#0a0a0a] transition-colors duration-300"></span>
            </span>
        </button>

        {{-- Divider --}}
        <div class="hidden sm:block w-px h-8 bg-slate-200 dark:bg-white/[0.05] mx-1 transition-colors duration-300"></div>

        {{-- Profile Dropdown --}}
        <div class="relative">
{{-- Profile Trigger --}}
            <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                    class="flex items-center gap-3 p-1.5 pr-4 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-slate-600 dark:hover:bg-slate-700 hover:shadow-md transition-all duration-300 outline-none shadow-sm dark:shadow-none group">

                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-blue-500/30 group-hover:scale-105 group-hover:ring-2 ring-blue-500/20 dark:ring-slate-600 transition-all duration-300">
                    {{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->name ?? 'A', 0, 1)) }}
                </div>

                <div class="hidden md:block text-left">
                    <div class="text-xs font-black text-slate-800 dark:text-slate-100 leading-tight transition-colors duration-300">
                        {{ Auth::user()->nama ?? Auth::user()->name ?? 'Administrator' }}
                    </div>
                    <div class="text-[9px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest mt-0.5 transition-colors duration-300">
                        Super Access
                    </div>
                </div>

                <i class="mdi mdi-chevron-down text-slate-400 dark:text-slate-500 text-lg transition-transform duration-300 hidden md:block" :class="profileOpen ? 'rotate-180 text-blue-500 dark:text-white' : 'group-hover:text-blue-500 dark:group-hover:text-white'"></i>
            </button>

            {{-- Dropdown Menu (Glassmorphism & Smooth Alpine Transitions) --}}
            <div x-show="profileOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                 class="absolute right-0 mt-3 w-64 bg-white/90 dark:bg-[#050505]/95 backdrop-blur-xl border border-slate-200/50 dark:border-white/[0.05] rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,1)] overflow-hidden z-50 origin-top-right ring-1 ring-black/5 dark:ring-white/[0.02]">

                {{-- Dropdown Header --}}
                <div class="px-5 py-4 bg-slate-50/50 dark:bg-white/[0.02] border-b border-slate-100 dark:border-white/[0.05] transition-colors duration-300">
                    <p class="text-sm font-black text-slate-800 dark:text-white truncate mb-0.5">{{ Auth::user()->nama ?? Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email ?? 'admin@pondasikita.com' }}</p>
                </div>

                {{-- Dropdown Links --}}
                <div class="p-2 space-y-1">
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:bg-blue-50 dark:hover:bg-white/[0.05] transition-all duration-200 no-underline group">
                        <i class="mdi mdi-account-cog-outline text-lg text-slate-400 dark:text-slate-500 group-hover:text-blue-500 dark:group-hover:text-white transition-colors"></i> Pengaturan Profil
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-white hover:bg-blue-50 dark:hover:bg-white/[0.05] transition-all duration-200 no-underline group">
                        <i class="mdi mdi-shield-key-outline text-lg text-slate-400 dark:text-slate-500 group-hover:text-blue-500 dark:group-hover:text-white transition-colors"></i> Log Keamanan
                    </a>
                </div>

                {{-- Logout Section --}}
                <div class="p-3 border-t border-slate-100 dark:border-white/[0.05] bg-slate-50/30 dark:bg-white/[0.02] transition-colors duration-300">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-black text-red-600 dark:text-rose-400 bg-red-50 dark:bg-[#4c0519]/40 hover:bg-red-600 dark:hover:bg-rose-500 dark:border dark:border-transparent dark:hover:border-rose-400/50 hover:text-white dark:hover:text-white transition-all duration-300 outline-none focus:ring-4 focus:ring-red-500/20 active:scale-[0.98] group">
                            <i class="mdi mdi-power text-lg group-hover:scale-110 transition-transform duration-300"></i> Keluar Sistem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
