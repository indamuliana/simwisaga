        <!-- ── Dynamic Module Landing Blocks (Database Module Registry - Bright & Warm) ── -->
        @if(!empty($landingBlocks) && count($landingBlocks) > 0)
        <section class="py-10 sm:py-12 bg-[#FAF9F6] border-t border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-6">
                    <span class="google-tag shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#FF6B4A]"></span>
                        <span>Layanan Terintegrasi Realtime</span>
                    </span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-2">Transparansi Layanan Sistem {{ $appName }}</h2>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($landingBlocks as $block)
                        <div class="card-bento p-4 sm:p-5 text-center bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider block truncate">{{ $block['label'] ?? '' }}</span>
                            <p class="text-2xl sm:text-3xl font-mono-code font-extrabold text-[#FF6B4A] mt-1">{{ $block['value'] ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
