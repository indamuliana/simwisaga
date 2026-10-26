        <!-- ── 15. Filosofi Pilar 3: Tiada Masyarakat Pembelajar Sekolah (Bright & Warm) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden" id="pilar-pemimpin">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute top-0 right-10 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-10 w-96 h-96 bg-orange-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-4xl mx-auto mb-10 sm:mb-12" data-animate="fade-up">
                    <span class="google-tag shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#FF6B4A]"></span>
                        <span>{{ $cfg['pilar_pemimpin']['tag'] ?? 'SMK Wikrama 1 Garut' }}</span>
                    </span>
                    <h2 class="text-xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-3 leading-snug">
                        {{ $cfg['pilar_pemimpin']['title'] ?? 'Tiada masyarakat pembelajar sekolah, tanpa pemimpin perubahan sekolah, dan tanpa kesungguhan warga sekolah' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-3 font-normal">
                        {{ $cfg['pilar_pemimpin']['desc'] ?? 'Kepemimpinan visioner dan komitmen total seluruh pendidik, tenaga kependidikan, serta santri dalam mewujudkan sekolah unggul berdaya saing global.' }}
                    </p>
                </div>

                <!-- 4 Feature Cards (Blueprint Spec Style) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($cfg['pilar_pemimpin']['items'] ?? [] as $idx => $item)
                    <div class="card-blueprint overflow-hidden group flex flex-col justify-between" data-animate="tilt" data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <div class="aspect-[16/10] w-full overflow-hidden relative bg-slate-100">
                                <img src="{{ asset($item['image'] ?? 'images/web/campus-environment.jpg') }}" 
                                     alt="{{ $item['title'] }}" 
                                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-80"></div>
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#2563EB] text-white shadow-xs">
                                    {{ $item['badge'] ?? 'Inovasi Belajar' }}
                                </span>
                            </div>
                            <div class="p-4 sm:p-5">
                                <h3 class="text-base font-bold text-slate-900 mb-1.5 group-hover:text-[#2563EB] transition-colors">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                    {{ $item['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-4 pb-4 sm:px-5 sm:pb-5 pt-0">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#2563EB] group-hover:translate-x-1 transition-all">
                                <span>Budaya Unggulan</span>
                                <span>&rarr;</span>
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
