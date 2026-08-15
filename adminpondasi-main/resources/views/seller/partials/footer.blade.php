{{-- FOOTER PREMIUM TAILWIND --}}
<footer class="mt-10 pt-6 pb-8 border-t border-slate-200">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">

        {{-- Copyright --}}
        <div class="text-sm font-medium text-slate-500">
            &copy; {{ date('Y') }} <span class="font-black text-slate-800">Pondasikita</span> Seller Center.
            <span class="hidden sm:inline">All rights reserved.</span>
        </div>

        {{-- Tautan Bantuan Tambahan (Opsional tapi bikin kelihatan pro) --}}
        <div class="flex items-center gap-4 sm:gap-6 text-sm font-bold text-slate-400">
            <a href="#" class="hover:text-blue-600 transition-colors">Pusat Bantuan</a>
            <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
            <a href="#" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a>
            <div class="w-1 h-1 bg-slate-300 rounded-full hidden sm:block"></div>
            <a href="#" class="hover:text-blue-600 transition-colors hidden sm:block">Syarat & Ketentuan</a>
        </div>

    </div>
</footer>
