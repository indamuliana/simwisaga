        <!-- ── 9. Interactive Campus Facilities & Bento Labs Gallery (Full-Width Cinematic Destination Slider) ── -->
        @php
            $dbFacilities = class_exists(\App\Modules\AdministrasiWeb\Models\WebFacility::class)
                ? \App\Modules\AdministrasiWeb\Models\WebFacility::getActiveFacilities()
                : collect();
            $rawList = $dbFacilities->isNotEmpty() ? $dbFacilities : collect($cfg['fasilitas'] ?? []);
            
            $facilitiesData = $rawList->map(function($f, $i) {
                $isObj = is_object($f);
                return [
                    'id' => $isObj ? $f->id : ($i + 1),
                    'title' => $isObj ? $f->title : ($f['title'] ?? ''),
                    'category' => $isObj ? $f->category : ($f['category'] ?? 'lab'),
                    'cat_label' => $isObj ? $f->cat_label : ($f['cat_label'] ?? 'Lab Komputer & Coding'),
                    'badge' => $isObj ? $f->badge : ($f['badge'] ?? ''),
                    'desc' => $isObj ? $f->desc : ($f['desc'] ?? ''),
                    'highlight' => $isObj ? $f->highlight : ($f['highlight'] ?? ''),
                    'image' => $isObj ? $f->imageUrl() : \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($f['image'] ?? null, 'images/web/hero-pplg.jpg'),
                ];
            })->values()->all();
        @endphp

        <section class="w-full relative min-h-[480px] sm:min-h-[520px] lg:min-h-[540px] flex flex-col justify-between overflow-hidden bg-slate-950 text-white select-none"
                 id="fasilitas"
                 x-data="{
                     facilities: {{ Js::from($facilitiesData) }},
                     currentIndex: 0,
                     timer: null,
                     isHovered: false,

                     get activeItem() {
                         if (!this.facilities.length) return {};
                         return this.facilities[this.currentIndex % this.facilities.length] || this.facilities[0];
                     },

                     next() {
                         if (!this.facilities.length) return;
                         this.currentIndex = (this.currentIndex + 1) % this.facilities.length;
                     },

                     prev() {
                         if (!this.facilities.length) return;
                         this.currentIndex = (this.currentIndex - 1 + this.facilities.length) % this.facilities.length;
                     },

                     setIndex(idx) {
                         this.currentIndex = idx;
                     },

                     startAutoplay() {
                         this.timer = setInterval(() => {
                             if (!this.isHovered) {
                                 this.next();
                             }
                         }, 6000);
                     },

                     stopAutoplay() {
                         if (this.timer) clearInterval(this.timer);
                     }
                 }"
                 x-init="startAutoplay()"
                 @mouseenter="isHovered = true"
                 @mouseleave="isHovered = false">

            <!-- ── Dynamic Cinematic Background Crossfade ── -->
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <template x-for="(item, idx) in facilities" :key="item.id">
                    <div class="absolute inset-0 transition-opacity duration-1000 ease-out"
                         :class="activeItem.id === item.id ? 'opacity-100 scale-100' : 'opacity-0 scale-105'">
                        <img :src="item.image" 
                             :alt="item.title" 
                             class="w-full h-full object-cover" 
                             loading="lazy"
                             decoding="async">
                    </div>
                </template>

                <!-- Cinematic Vignette & Deep Multi-Gradient Overlays -->
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/75 to-slate-950/40 z-10"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-slate-950/70 z-10"></div>
                <!-- Subtle Ambient Atmosphere -->
                <div class="absolute inset-0 bg-[radial-gradient(transparent_0%,rgba(0,0,0,0.4)_100%)] z-10"></div>
            </div>

            <!-- ── Top Header Bar (Tanpa Tab Filter - 1 Kelompok Utuh) ── -->
            <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8">
                <div class="flex items-center justify-between gap-4 border-b border-white/15 pb-3">
                    <!-- Section Title Badge Tag -->
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white/90 text-xs font-semibold shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span>{{ $cfg['fasilitas_heading']['tag'] ?? 'Infrastruktur & Sarana Industri Modern' }}</span>
                        </span>
                    </div>

                    <!-- Clean Gallery Count Indicator -->
                    <span class="text-xs text-white/60 font-medium hidden sm:inline-block">
                        Total <span class="text-amber-400 font-bold" x-text="facilities.length"></span> Sarana &amp; Fasilitas
                    </span>
                </div>
            </div>

            <!-- ── Main Showcase Body: Left Active Story & Right Preview Cards ── -->
            <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 my-auto">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-center">
                    
                    <!-- Left Column: Featured Facility Details -->
                    <div class="lg:col-span-6 xl:col-span-5 text-left">
                        <!-- Subtitle with Gold Dash Indicator -->
                        <div class="flex items-center gap-2 text-amber-400 text-xs sm:text-sm font-bold uppercase tracking-wider mb-2">
                            <span class="w-5 h-0.5 bg-amber-400 inline-block"></span>
                            <span x-text="activeItem.cat_label || activeItem.badge"></span>
                        </div>

                        <!-- Display Title (Lebih Ringkas & Proporsional) -->
                        <h2 class="text-2xl sm:text-4xl lg:text-[2.6rem] font-black uppercase tracking-tight text-white leading-tight mb-3 drop-shadow-md"
                            x-text="activeItem.title">
                        </h2>

                        <!-- Description Paragraph -->
                        <p class="text-xs sm:text-sm text-slate-200/90 leading-relaxed mb-4 font-normal max-w-lg line-clamp-3"
                           x-text="activeItem.desc">
                        </p>

                        <!-- Key Specification / Highlight Pill -->
                        <template x-if="activeItem.highlight">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs text-white/95 font-medium mb-5 shadow-xs">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span x-text="activeItem.highlight"></span>
                            </div>
                        </template>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 flex-wrap">
                            <button type="button"
                                    @click="openImageModal(activeItem.image, activeItem.title, activeItem.cat_label)"
                                    class="w-10 h-10 rounded-full bg-amber-400 hover:bg-amber-300 text-slate-950 flex items-center justify-center transition-transform duration-200 hover:scale-105 shadow-md cursor-pointer focus-visible:outline-hidden"
                                    title="Perbesar Foto HD">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                </svg>
                            </button>

                            <button type="button"
                                    @click="openImageModal(activeItem.image, activeItem.title, activeItem.cat_label)"
                                    class="px-5 py-2 rounded-full border border-white/40 hover:border-white text-white hover:bg-white/10 text-xs font-bold uppercase tracking-wider transition-all duration-200 cursor-pointer backdrop-blur-xs flex items-center gap-2 shadow-xs hover:translate-x-0.5 focus-visible:outline-hidden">
                                <span>Lihat Pratinjau HD</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Floating Preview Carousel Cards -->
                    <div class="lg:col-span-6 xl:col-span-7 overflow-hidden">
                        <div class="flex items-center gap-3.5 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory py-2 px-1">
                            <template x-for="(item, idx) in facilities" :key="item.id">
                                <div @click="setIndex(idx)"
                                     class="relative flex-none w-[150px] sm:w-[170px] md:w-[185px] h-[220px] sm:h-[250px] rounded-2xl overflow-hidden cursor-pointer snap-start transition-all duration-300 group border"
                                     :class="currentIndex === idx 
                                        ? 'ring-2 ring-amber-400 border-amber-400 scale-102 shadow-2xl z-10' 
                                        : 'border-white/20 opacity-80 hover:opacity-100 hover:scale-102 shadow-md'">
                                    
                                    <!-- Card Image -->
                                    <img :src="item.image" 
                                         :alt="item.title" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" 
                                         loading="lazy">

                                    <!-- Dark Bottom Gradient for Text Legibility -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/50 to-transparent pointer-events-none"></div>

                                    <!-- Active Glow Overlay Indicator -->
                                    <div class="absolute inset-0 border-2 border-amber-400 rounded-2xl pointer-events-none"
                                         x-show="currentIndex === idx"></div>

                                    <!-- Content Overlay Inside Card -->
                                    <div class="absolute inset-x-0 bottom-0 p-3 sm:p-3.5 z-10 flex flex-col justify-end text-left">
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300">
                                            <span class="w-2.5 h-0.5 bg-amber-400 inline-block"></span>
                                            <span class="truncate" x-text="item.cat_label"></span>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-bold text-white uppercase leading-snug mt-1 group-hover:text-amber-300 transition-colors line-clamp-2"
                                            x-text="item.title">
                                        </h4>
                                        <p class="text-[10px] text-amber-300/90 mt-1 truncate"
                                           x-text="item.badge || item.highlight">
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Bottom Navigation Controls Bar ── -->
            <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-5 sm:pb-7 pt-1">
                <div class="flex items-center justify-between sm:justify-end gap-4 border-t border-white/10 pt-3">
                    <!-- Prev & Next Circular Arrow Buttons -->
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="prev()"
                                aria-label="Slide sebelumnya"
                                class="w-9 h-9 rounded-full border border-white/30 hover:border-white text-white hover:bg-white/10 flex items-center justify-center transition-colors duration-200 cursor-pointer shadow-xs focus-visible:outline-hidden">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button type="button" 
                                @click="next()"
                                aria-label="Slide berikutnya"
                                class="w-9 h-9 rounded-full border border-white/30 hover:border-white text-white hover:bg-white/10 flex items-center justify-center transition-colors duration-200 cursor-pointer shadow-xs focus-visible:outline-hidden">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Horizontal Progress Line -->
                    <div class="h-0.5 bg-white/20 w-24 sm:w-36 rounded-full overflow-hidden relative">
                        <div class="h-full bg-amber-400 transition-all duration-500 rounded-full"
                             :style="'width: ' + (((currentIndex + 1) / Math.max(facilities.length, 1)) * 100) + '%'"></div>
                    </div>

                    <!-- Page Number Counter (e.g. 01 / 03) -->
                    <div class="text-xs sm:text-sm font-bold font-mono tracking-wider">
                        <span class="text-amber-400 text-sm sm:text-base" x-text="String(currentIndex + 1).padStart(2, '0')"></span>
                        <span class="text-white/40"> / </span>
                        <span class="text-white/80" x-text="String(facilities.length).padStart(2, '0')"></span>
                    </div>
                </div>
            </div>

        </section>
