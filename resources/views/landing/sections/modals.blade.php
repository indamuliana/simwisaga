    <!-- ── Lightbox Modals ── -->
    <!-- Lightbox SPMB Flyer Modal -->
    <div x-show="flyerModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-3 sm:p-4 backdrop-blur-sm"
         @keydown.escape.window="flyerModal = false">
        <div class="relative max-w-2xl w-full max-h-[95vh] flex flex-col items-center bg-slate-900/90 p-3 sm:p-4 rounded-3xl border border-white/20" @click.outside="flyerModal = false">
            <div class="w-full flex items-center justify-between pb-2 mb-2 border-b border-white/10">
                <span class="text-xs font-bold text-orange-400">Brosur &amp; Flyer SPMB</span>
                <button type="button" 
                        @click="flyerModal = false" 
                        class="text-white hover:text-orange-400 text-sm font-bold flex items-center gap-1 cursor-pointer py-1 px-2.5 rounded-lg bg-white/10">
                    <span>Tutup</span> &times;
                </button>
            </div>
            <img src="{{ asset('images/landing/flyer-spmb-2027.jpg') }}" 
                 alt="Flyer SPMB Full" 
                 class="rounded-2xl max-h-[70vh] w-auto max-w-full object-contain shadow-2xl">
            <div class="mt-3 flex w-full justify-center">
                <a href="{{ $cfg['branding']['spmb_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" target="_blank" rel="noopener" class="btn-google-primary text-xs sm:text-sm w-full sm:w-auto text-center font-bold">
                    Buka Formulir Pendaftaran Online &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Universal Image Lightbox Modal -->
    <div x-show="imageModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4 backdrop-blur-md"
         @keydown.escape.window="imageModal = false">
        <div class="relative max-w-4xl w-full max-h-[95vh] flex flex-col items-center bg-white rounded-3xl overflow-hidden shadow-2xl p-5 border border-slate-200" 
             @click.outside="imageModal = false">
            <div class="w-full flex items-center justify-between pb-3 border-b border-slate-200 mb-3">
                <div class="min-w-0 pr-3">
                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-orange-600 block" x-text="modalImageCategory"></span>
                    <h4 class="text-sm sm:text-base font-extrabold text-slate-900 truncate" x-text="modalImageTitle"></h4>
                </div>
                <button type="button" 
                        @click="imageModal = false" 
                        class="p-1 rounded-full text-slate-500 hover:text-red-500 transition-colors shrink-0"
                        aria-label="Tutup Pratinjau">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="relative max-h-[70vh] w-full flex items-center justify-center overflow-hidden rounded-2xl bg-slate-950">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E" :src="modalImageSrc" :alt="modalImageTitle" class="max-h-[66vh] w-auto max-w-full object-contain">
            </div>
            <div class="w-full pt-3 flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Dokumentasi Resmi &bull; {{ $schoolName }}</span>
                <a :href="modalImageSrc" target="_blank" rel="noopener" class="text-[#2563EB] hover:text-[#1D4ED8] hover:underline font-bold">
                    Buka Ukuran Asli &rarr;
                </a>
            </div>
        </div>
    </div>
