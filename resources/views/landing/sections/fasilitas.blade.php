        <!-- ── 9. Interactive Campus Facilities & Labs Gallery (Bright & Warm Education) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="fasilitas" x-data="{ facilityTab: 'all' }" data-mouse-glow>
            <!-- Background Ornaments: Architectural Grid Dots & Soft Ambient Glow -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
                <div class="absolute -top-24 left-1/4 w-80 h-80 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/4 w-80 h-80 bg-blue-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="google-tag shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#FF6B4A]"></span>
                        <span>Infrastruktur &amp; Sarana Industri</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                        Fasilitas &amp; Ekosistem Kampus Modern
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 leading-relaxed font-normal">
                        Mendukung pembelajaran praktikum intensif dengan laboratorium mutakhir dan lingkungan Adiwiyata yang hijau asri.
                    </p>

                    <!-- Filter Categories (Themed) -->
                    @php
                        $dbFacilities = class_exists(\App\Modules\AdministrasiWeb\Models\WebFacility::class)
                            ? \App\Modules\AdministrasiWeb\Models\WebFacility::getActiveFacilities()
                            : collect();
                        $facilityCount = $dbFacilities->isNotEmpty() ? $dbFacilities->count() : count($cfg['fasilitas'] ?? []);
                    @endphp
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
                        <button type="button" 
                                @click="facilityTab = 'all'" 
                                :class="facilityTab === 'all' ? 'tab-pill-active font-bold' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#FF6B4A] hover:text-[#FF6B4A] hover:bg-orange-50'"
                                :style="facilityTab === 'all' ? 'background-color: #FF6B4A; background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%); color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs">
                            Semua Fasilitas ({{ $facilityCount }})
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'lab'" 
                                :class="facilityTab === 'lab' ? 'tab-pill-active font-bold' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#FF6B4A] hover:text-[#FF6B4A] hover:bg-orange-50'"
                                :style="facilityTab === 'lab' ? 'background-color: #FF6B4A; background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%); color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs flex items-center gap-1.5">
                            <span>💻</span>
                            <span>Lab Komputer &amp; Coding</span>
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'kelas'" 
                                :class="facilityTab === 'kelas' ? 'tab-pill-active font-bold' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#FF6B4A] hover:text-[#FF6B4A] hover:bg-orange-50'"
                                :style="facilityTab === 'kelas' ? 'background-color: #FF6B4A; background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%); color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs flex items-center gap-1.5">
                            <span>📱</span>
                            <span>Kelas Digital &amp; TeFa</span>
                        </button>
                        <button type="button" 
                                @click="facilityTab = 'gedung'" 
                                :class="facilityTab === 'gedung' ? 'tab-pill-active font-bold' : 'bg-white text-slate-700 font-bold border border-slate-200 hover:border-[#FF6B4A] hover:text-[#FF6B4A] hover:bg-orange-50'"
                                :style="facilityTab === 'gedung' ? 'background-color: #FF6B4A; background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%); color: #ffffff !important;' : ''"
                                class="px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm transition-all cursor-pointer shadow-xs flex items-center gap-1.5">
                            <span>🏫</span>
                            <span>Gedung &amp; Adiwiyata</span>
                        </button>
                    </div>
                </div>

                <!-- Facilities Grid (Gallery Tile Cards) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @if($dbFacilities->isNotEmpty())
                        @foreach($dbFacilities as $idx => $f)
                        <div x-show="facilityTab === 'all' || facilityTab === '{{ $f->category }}'"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="card-gallery-tile flex flex-col justify-between h-full group"
                             data-animate="scale" data-animate-delay="{{ ($idx % 3) + 1 }}">
                            <div>
                                <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100 cursor-pointer"
                                     @click="openImageModal('{{ $f->imageUrl() }}', '{{ $f->title }}', '{{ $f->cat_label }}')">
                                    <img src="{{ $f->imageUrl() }}" alt="{{ $f->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    @if($f->badge)
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold bg-[#FF6B4A] text-white shadow-xs">
                                        {{ $f->badge }}
                                    </span>
                                    @endif
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                        <span class="text-xs font-bold uppercase tracking-wider bg-black/60 px-3.5 py-1.5 rounded-full border border-white/30 flex items-center gap-1">
                                            Perbesar Foto &rarr;
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5 pt-4">
                                    <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-[#FF6B4A] transition-colors">
                                        {{ $f->title }}
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed font-normal">
                                        {{ $f->desc }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between text-xs font-medium">
                                <span class="text-slate-700 font-bold text-[11px]">{{ $f->highlight }}</span>
                                <button type="button" 
                                        @click="openImageModal('{{ $f->imageUrl() }}', '{{ $f->title }}', '{{ $f->cat_label }}')"
                                        class="text-[#FF6B4A] hover:text-[#E85535] font-bold cursor-pointer inline-flex items-center gap-1">
                                    <span>Lihat Pratinjau</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        @foreach($cfg['fasilitas'] ?? [] as $idx => $f)
                        <div x-show="facilityTab === 'all' || facilityTab === '{{ $f['category'] }}'"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="card-gallery-tile flex flex-col justify-between h-full group"
                             data-animate="scale" data-animate-delay="{{ ($idx % 3) + 1 }}">
                            <div>
                                <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100 cursor-pointer"
                                     @click="openImageModal('{{ asset($f['image']) }}', '{{ $f['title'] }}', '{{ $f['cat_label'] }}')">
                                    <img src="{{ asset($f['image']) }}" alt="{{ $f['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold bg-[#FF6B4A] text-white shadow-xs">
                                        {{ $f['badge'] }}
                                    </span>
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                        <span class="text-xs font-bold uppercase tracking-wider bg-black/60 px-3.5 py-1.5 rounded-full border border-white/30 flex items-center gap-1">
                                            Perbesar Foto &rarr;
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5 pt-4">
                                    <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-[#FF6B4A] transition-colors">
                                        {{ $f['title'] }}
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed font-normal">
                                        {{ $f['desc'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between text-xs font-medium">
                                <span class="text-slate-700 font-bold text-[11px]">{{ $f['highlight'] }}</span>
                                <button type="button" 
                                        @click="openImageModal('{{ asset($f['image']) }}', '{{ $f['title'] }}', '{{ $f['cat_label'] }}')"
                                        class="text-[#FF6B4A] hover:text-[#E85535] font-bold cursor-pointer inline-flex items-center gap-1">
                                    <span>Lihat Pratinjau</span>
                                    <span class="group-hover-arrow">&rarr;</span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
