        <!-- ── 7. Leadership & Institutional Vision (Bright & Warm Education) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="profil">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 right-1/4 w-80 h-80 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 left-1/4 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                    
                    <!-- Principal Card (Executive Crest Card) -->
                    <div class="lg:col-span-5" data-animate="slide-right">
                        <div class="card-bento p-5 sm:p-8 bg-white border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.05)]">
                            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-5">
                                <div class="shrink-0 relative">
                                    <div class="w-28 sm:w-32 h-36 sm:h-40 rounded-2xl overflow-hidden border-2 border-orange-200 shadow-md bg-white mx-auto">
                                        <img src="{{ class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class) ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($cfg['principal']['image'] ?? null, 'images/web/principal-kunedi.jpg') : asset($cfg['principal']['image'] ?? 'images/web/principal-kunedi.jpg') }}" 
                                             alt="{{ $cfg['principal']['name'] ?? 'Kepala Sekolah' }}" 
                                             class="w-full h-full object-cover object-top"
                                             loading="lazy">
                                    </div>
                                    <span class="absolute -bottom-2 -right-2 bg-gradient-to-r from-orange-500 to-[#FF6B4A] text-white font-bold text-[10px] px-3 py-0.5 rounded-full shadow-md">
                                        {{ $cfg['principal']['role'] ?? 'Kepala Sekolah' }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-[#FF6B4A] uppercase tracking-wider block">Pimpinan Lembaga</span>
                                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5">
                                        {{ !empty($cfg['principal']['name']) ? $cfg['principal']['name'] : ($sekolah->kepala_sekolah_nama ?? 'Kunedi, S.Si.') }}
                                    </h3>
                                    <span class="text-xs text-slate-500 font-semibold block mt-0.5">{{ $schoolName }}</span>
                                    <p class="text-xs sm:text-sm text-slate-700 mt-3 font-editorial italic leading-relaxed font-medium">
                                        &ldquo;{{ $cfg['principal']['quote'] ?? 'Sekolah kejuruan teladan nasional yang berbudaya lingkungan, berkarakter sesuai profil pelajar Pancasila, berbasis teknologi informasi.' }}&rdquo;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vision & 4 Pillars (Pillar Brick Cards) -->
                    <div class="lg:col-span-7" data-animate="slide-left" data-animate-delay="1">
                        <div>
                            <span class="google-tag shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-[#FF6B4A]"></span>
                                <span>Visi &amp; Komitmen Kelembagaan</span>
                            </span>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                                {{ $cfg['principal']['vision_title'] ?? 'Teladan Nasional Berbudaya Lingkungan & Teknologi' }}
                            </h2>
                            <p class="text-xs sm:text-sm md:text-base text-slate-600 mt-3 leading-relaxed font-normal">
                                {{ $cfg['principal']['vision_desc'] ?? 'Menjadi sekolah kejuruan teladan nasional yang berbudaya lingkungan berkelanjutan, berbasis teknologi informasi, berkomitmen mencetak lulusan berkarakter mulia.' }}
                            </p>
                        </div>

                        <div class="mt-8">
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block mb-3">
                                4 Pilar Misi Wikrama
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($cfg['principal']['pillars'] ?? [] as $pil)
                                <div class="card-pillar-brick group">
                                    <div class="flex items-center gap-2.5 mb-1.5">
                                        <span class="w-7 h-7 rounded-xl bg-orange-100 text-orange-600 border border-orange-200 font-bold text-xs flex items-center justify-center shrink-0 group-hover:bg-[#FF6B4A] group-hover:text-white transition-colors">
                                            {{ $pil['num'] }}
                                        </span>
                                        <h4 class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-[#FF6B4A] transition-colors">{{ $pil['title'] }}</h4>
                                    </div>
                                    <p class="text-[11px] sm:text-xs text-slate-600 leading-normal pl-9 font-normal">{{ $pil['desc'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
