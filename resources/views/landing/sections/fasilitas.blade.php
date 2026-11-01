        <!-- ── 9. Interactive Campus Facilities & Bento Labs Gallery (Wikrama Royal Archive) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="fasilitas" x-data="{ facilityTab: 'all' }">
            <!-- Background Ornaments: Architectural Grid Dots & Soft Ambient Glow -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
                <div class="absolute -top-24 left-1/4 w-80 h-80 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/4 w-80 h-80 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="google-tag google-tag-blue shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#00519E]"></span>
                        <span>Infrastruktur &amp; Sarana Industri Modern</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                        Fasilitas &amp; Ekosistem Kampus
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 leading-relaxed font-normal">
                        Mendukung pembelajaran praktikum intensif dengan laboratorium mutakhir dan lingkungan Adiwiyata yang hijau dan berstandar internasional.
                    </p>

                    <!-- Filter Categories (Bespoke SVG Iconography & Wikrama Navy/Gold Pills) -->
                    @php
                        $dbFacilities = class_exists(\App\Modules\AdministrasiWeb\Models\WebFacility::class)
                            ? \App\Modules\AdministrasiWeb\Models\WebFacility::getActiveFacilities()
                            : collect();
                        $facilityCount = $dbFacilities->isNotEmpty() ? $dbFacilities->count() : count($cfg['fasilitas'] ?? []);
                    @endphp
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
                        <button type="button" 
                                @click="facilityTab = 'all'" 
                                :class="facilityTab === 'all' ? 'tab-pill-active font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#00519E] hover:text-[#00519E] hover:bg-[#EBF4FC]'"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all duration-200 cursor-pointer shadow-xs">
                            Semua Fasilitas ({{ $facilityCount }})
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'lab'" 
                                :class="facilityTab === 'lab' ? 'tab-pill-active font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#00519E] hover:text-[#00519E] hover:bg-[#EBF4FC]'"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all duration-200 cursor-pointer shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Lab Komputer &amp; Coding</span>
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'kelas'" 
                                :class="facilityTab === 'kelas' ? 'tab-pill-active font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#00519E] hover:text-[#00519E] hover:bg-[#EBF4FC]'"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all duration-200 cursor-pointer shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Kelas Digital &amp; TeFa</span>
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'gedung'" 
                                :class="facilityTab === 'gedung' ? 'tab-pill-active font-bold shadow-md' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#00519E] hover:text-[#00519E] hover:bg-[#EBF4FC]'"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all duration-200 cursor-pointer shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Gedung &amp; Adiwiyata</span>
                        </button>
                    </div>
                </div>

                <!-- Facilities Asymmetrical Bento Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 sm:gap-8">
                    @php
                        $facilitiesList = $dbFacilities->isNotEmpty() ? $dbFacilities : collect($cfg['fasilitas'] ?? []);
                    @endphp
                    @foreach($facilitiesList as $idx => $f)
                    @php
                        $isObject = is_object($f);
                        $category = $isObject ? $f->category : ($f['category'] ?? '');
                        $imgUrl = $isObject ? $f->imageUrl() : asset($f['image']);
                        $title = $isObject ? $f->title : ($f['title'] ?? '');
                        $badge = $isObject ? $f->badge : ($f['badge'] ?? '');
                        $desc = $isObject ? $f->desc : ($f['desc'] ?? '');
                        $highlight = $isObject ? $f->highlight : ($f['highlight'] ?? '');
                        $catLabel = $isObject ? $f->cat_label : ($f['cat_label'] ?? '');

                        // Bento spans: Flagship card spans 7 cols, companion 5 cols; subsequent cards span 4 cols
                        $colSpanClass = 'lg:col-span-4';
                        if ($idx === 0) {
                            $colSpanClass = 'md:col-span-2 lg:col-span-7';
                        } elseif ($idx === 1) {
                            $colSpanClass = 'lg:col-span-5';
                        }
                    @endphp
                    <div x-show="facilityTab === 'all' || facilityTab === '{{ $category }}'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="{{ $colSpanClass }} card-gallery-tile flex flex-col justify-between h-full group bg-white"
                         data-animate="scale" data-animate-delay="{{ ($idx % 3) + 1 }}">
                        <div>
                            <div class="relative {{ $idx === 0 ? 'aspect-[16/9] sm:aspect-[21/10]' : 'aspect-[16/10]' }} w-full overflow-hidden bg-slate-100 cursor-pointer"
                                 @click="openImageModal('{{ $imgUrl }}', '{{ $title }}', '{{ $catLabel }}')">
                                <img src="{{ $imgUrl }}" 
                                     alt="{{ $title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                     loading="lazy" 
                                     decoding="async">
                                @if($badge)
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold bg-[#00519E] text-white shadow-xs">
                                    {{ $badge }}
                                </span>
                                @endif
                                <div class="absolute inset-0 bg-[#00305F]/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center text-white backdrop-blur-[2px]">
                                    <span class="text-xs font-bold uppercase tracking-wider bg-black/60 px-3.5 py-1.5 rounded-full border border-white/30 flex items-center gap-1.5 shadow-md">
                                        <span>Perbesar Foto</span>
                                        <span class="group-hover-arrow">&rarr;</span>
                                    </span>
                                </div>
                            </div>
                            <div class="p-5 {{ $idx === 0 ? 'sm:p-6' : '' }} pt-4">
                                <h3 class="{{ $idx === 0 ? 'text-lg sm:text-xl' : 'text-base' }} font-bold text-slate-900 leading-snug group-hover:text-[#00519E] transition-colors">
                                    {{ $title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed font-normal">
                                    {{ $desc }}
                                </p>
                            </div>
                        </div>
                        <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between text-xs font-medium">
                            <span class="text-slate-700 font-bold text-[11px]">{{ $highlight }}</span>
                            <button type="button" 
                                    @click="openImageModal('{{ $imgUrl }}', '{{ $title }}', '{{ $catLabel }}')"
                                    class="text-[#00519E] hover:text-[#00305F] font-bold cursor-pointer inline-flex items-center gap-1 transition-colors">
                                <span>Lihat Pratinjau</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
