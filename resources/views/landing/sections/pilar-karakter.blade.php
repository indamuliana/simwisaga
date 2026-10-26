        <!-- ── 13. Filosofi Pilar 1: Tiada Pendidikan Bermutu, Tanpa Pendidikan Karakter (Bright & Warm) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden" id="pilar-karakter">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 right-1/3 w-80 h-80 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 left-1/3 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-10 sm:mb-12" data-animate="fade-up">
                    <div class="max-w-3xl">
                        <span class="google-tag shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-[#FF6B4A]"></span>
                            <span>{{ $cfg['pilar_karakter']['tag'] ?? 'Pilar Mutu & Karakter' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 mt-3">
                            {{ $cfg['pilar_karakter']['title'] ?? 'Tiada pendidikan bermutu, tanpa pendidikan karakter' }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 mt-3 font-normal">
                            {{ $cfg['pilar_karakter']['desc'] ?? 'Delapan pondasi keunggulan terintegrasi yang membentuk ekosistem pendidikan vokasi berkualitas unggul di SMK Wikrama 1 Garut.' }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ $cfg['pilar_karakter']['btn_url'] ?? 'https://spmb.smkwikrama1garut.sch.id/' }}" 
                           target="_blank" rel="noopener" 
                           class="btn-google-primary text-xs sm:text-sm !py-2.5 sm:!py-3 !px-5 sm:!px-6 shadow-md hover:shadow-xl inline-flex items-center gap-2 w-full sm:w-auto justify-center font-bold">
                            <span>{{ $cfg['pilar_karakter']['btn_text'] ?? 'Daftar Sekarang' }}</span>
                            <span class="text-base">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- 8 Cards Grid (Line-Art Card Style) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($cfg['pilar_karakter']['items'] ?? [] as $idx => $item)
                    <div class="card-bento overflow-hidden group flex flex-col justify-between" data-animate="scale" data-animate-delay="{{ ($idx % 4) + 1 }}">
                        <div>
                            <div class="aspect-[16/10] w-full overflow-hidden relative bg-slate-100">
                                <img src="{{ asset($item['image'] ?? 'images/web/campus-environment.jpg') }}" 
                                     alt="{{ $item['title'] ?? 'Pilar Wikrama' }}" 
                                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#FF6B4A] text-white shadow-xs">
                                    {{ $item['badge'] ?? 'Keunggulan' }}
                                </span>
                                <h3 class="absolute bottom-3 left-3 right-3 text-base sm:text-lg font-bold text-white drop-shadow">
                                    {{ $item['title'] }}
                                </h3>
                            </div>
                            <div class="p-4 sm:p-5">
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                                    {{ $item['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-4 pb-4 sm:px-5 sm:pb-5 pt-0">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#FF6B4A] group-hover:translate-x-1 transition-all">
                                <span>Nilai Utama Wikrama</span>
                                <span>&rarr;</span>
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
