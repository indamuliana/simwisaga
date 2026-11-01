        <!-- ── 7. Leadership & Institutional Vision (Wikrama Royal Archive) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="profil">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 right-1/4 w-80 h-80 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 left-1/4 w-80 h-80 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                    
                    <!-- Left: Principal Vertical Portrait (Executive Hallmark Portrait) -->
                    <div class="lg:col-span-5" data-animate="slide-right">
                        <div class="relative bg-white rounded-3xl p-3 sm:p-4 border border-slate-200/80 shadow-[0_12px_36px_-6px_rgba(0,48,95,0.08)] overflow-hidden group">
                            <!-- Vertical Photo Container (Aspect 4:5) -->
                            <div class="relative aspect-[4/5] w-full rounded-2xl overflow-hidden bg-slate-100 shadow-inner">
                                <img src="{{ class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class) ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($cfg['principal']['image'] ?? null, 'images/web/principal-kunedi.jpg') : asset($cfg['principal']['image'] ?? 'images/web/principal-kunedi.jpg') }}" 
                                     alt="{{ $cfg['principal']['name'] ?? 'Kepala Sekolah' }}" 
                                     class="w-full h-full object-cover object-top group-hover:scale-102 transition-transform duration-500 ease-out"
                                     loading="lazy"
                                     decoding="async">
                                <!-- Subtle gradient vignette at bottom for text legibility -->
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>
                                
                                <!-- Executive Floating Tag on Top -->
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md text-[#00519E] font-bold text-[11px] shadow-sm border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#00519E]"></span>
                                        <span>{{ $cfg['principal']['role'] ?? 'Kepala Sekolah' }}</span>
                                    </span>
                                </div>

                                <!-- Name & School Badge at bottom inside photo -->
                                <div class="absolute bottom-3 inset-x-3 text-white">
                                    <h3 class="text-base sm:text-lg font-extrabold tracking-tight drop-shadow-sm leading-tight text-white">
                                        {{ !empty($cfg['principal']['name']) ? $cfg['principal']['name'] : ($sekolah->kepala_sekolah_nama ?? 'Kunedi, S.Si.') }}
                                    </h3>
                                    <span class="text-xs text-white/80 font-medium block mt-0.5">
                                        Pimpinan Lembaga &bull; {{ $schoolName }}
                                    </span>
                                </div>
                            </div>

                            <!-- Quote strip below photo -->
                            <div class="p-3 sm:p-4 bg-slate-50/90 rounded-2xl mt-3 border border-slate-100">
                                <p class="text-xs sm:text-sm text-slate-700 font-manifesto italic leading-relaxed text-center">
                                    &ldquo;{{ $cfg['principal']['quote'] ?? 'Sekolah kejuruan teladan nasional yang berbudaya lingkungan, berkarakter sesuai profil pelajar Pancasila, berbasis teknologi informasi.' }}&rdquo;
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Vision & 4 Pillars (Minimalist Compact Grid) -->
                    <div class="lg:col-span-7" data-animate="slide-left" data-animate-delay="1">
                        <div>
                            <span class="google-tag google-tag-blue shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-[#00519E]"></span>
                                <span>Visi &amp; Komitmen Kelembagaan</span>
                            </span>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                                {{ $cfg['principal']['vision_title'] ?? 'Teladan Nasional Berbudaya Lingkungan & Teknologi' }}
                            </h2>
                            <p class="text-xs sm:text-sm md:text-base text-slate-600 mt-3 leading-relaxed font-normal">
                                {{ $cfg['principal']['vision_desc'] ?? 'Menjadi sekolah kejuruan teladan nasional yang berbudaya lingkungan berkelanjutan, berbasis teknologi informasi, berkomitmen mencetak lulusan berkarakter mulia.' }}
                            </p>
                        </div>

                        <div class="mt-7 sm:mt-8">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#00519E] block mb-3.5">
                                4 Pilar Misi Wikrama
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
                                @foreach($cfg['principal']['pillars'] ?? [] as $pil)
                                <div class="group p-3.5 sm:p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-400 transition-all duration-200">
                                    <div class="flex items-start gap-3">
                                        <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-800 border border-slate-300/90 font-mono-code font-bold text-xs flex items-center justify-center shrink-0 group-hover:bg-black group-hover:text-white group-hover:border-black transition-all duration-200 shadow-2xs">
                                            {{ $pil['num'] }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm leading-snug group-hover:text-[#00519E] transition-colors duration-200">
                                                {{ $pil['title'] }}
                                            </h4>
                                            <p class="text-[11px] sm:text-xs text-slate-600 leading-relaxed mt-1 font-normal">
                                                {{ $pil['desc'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>