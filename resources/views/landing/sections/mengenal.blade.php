        <!-- ── 3. "Get to Know Wikrama" Editorial Tabbed Showcase (Bright & Warm Education) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="mengenal">
            <!-- Background Ornaments: Dot Matrix & Ambient Glow Orbs -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>
                <div class="absolute -top-32 right-10 w-96 h-96 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 left-10 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="google-tag shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#FF6B4A]"></span>
                        <span>{{ $cfg['get_to_know']['tag'] ?? 'Mengenal Lebih Dekat' }}</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                        {{ $cfg['get_to_know']['title'] ?? 'Menemukan Potensi Sejati di Wikrama' }}
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 leading-relaxed font-normal">
                        {{ $cfg['get_to_know']['subtitle'] ?? 'Jelajahi ekosistem pembelajaran holistik yang menggabungkan keunggulan akademik, riset teknologi industri, dan pembinaan kepemimpinan islami.' }}
                    </p>

                    <!-- Editorial Tab Navigation (Bright & Warm Pills) -->
                    <div class="mt-8 flex flex-wrap justify-center sm:inline-flex p-1 sm:p-1.5 rounded-2xl sm:rounded-full bg-white border border-slate-200 shadow-sm gap-1 sm:gap-0 max-w-full">
                        <button type="button" 
                                @click="activeKnowTab = 'admissions'"
                                :class="activeKnowTab === 'admissions' ? 'tab-pill-active font-bold' : 'tab-pill-inactive font-bold'"
                                :style="activeKnowTab === 'admissions' ? 'background-color: #FF6B4A; background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%); color: #ffffff !important;' : ''"
                                class="px-4 sm:px-6 py-2 rounded-xl sm:rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            {{ $cfg['get_to_know']['tabs']['admissions']['name'] ?? 'Penerimaan Siswa' }}
                        </button>
                        <button type="button" 
                                @click="activeKnowTab = 'academics'"
                                :class="activeKnowTab === 'academics' ? 'tab-pill-active font-bold' : 'tab-pill-inactive font-bold'"
                                :style="activeKnowTab === 'academics' ? 'background-color: #FF6B4A; background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%); color: #ffffff !important;' : ''"
                                class="px-4 sm:px-6 py-2 rounded-xl sm:rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            {{ $cfg['get_to_know']['tabs']['academics']['name'] ?? 'Kompetensi Keahlian' }}
                        </button>
                        <button type="button" 
                                @click="activeKnowTab = 'boarding'"
                                :class="activeKnowTab === 'boarding' ? 'tab-pill-active font-bold' : 'tab-pill-inactive font-bold'"
                                :style="activeKnowTab === 'boarding' ? 'background-color: #FF6B4A; background-image: linear-gradient(135deg, #FF6B4A 0%, #FF5722 100%); color: #ffffff !important;' : ''"
                                class="px-4 sm:px-6 py-2 rounded-xl sm:rounded-full text-xs sm:text-sm transition-all cursor-pointer">
                            {{ $cfg['get_to_know']['tabs']['boarding']['name'] ?? 'Boarding & Karakter' }}
                        </button>
                    </div>
                </div>

                <!-- Tab Panel 1: Admissions (Bento Card Style) -->
                <div x-show="activeKnowTab === 'admissions'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($cfg['get_to_know']['tabs']['admissions']['cards'] ?? [] as $idx => $c)
                    <div class="card-bento overflow-hidden flex flex-col justify-between h-full group" data-animate="tilt" data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                <img src="{{ asset($c['image']) }}" alt="{{ $c['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 bg-[#FF6B4A] text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-xs">
                                    {{ $c['badge'] }}
                                </span>
                            </div>
                            <div class="p-6 pt-5">
                                <h3 class="text-lg font-bold text-slate-900 mb-2 leading-snug group-hover:text-[#FF6B4A] transition-colors">
                                    {{ $c['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                                    {{ $c['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2">
                            <a href="{{ $c['url'] }}" class="text-xs font-bold text-[#FF6B4A] inline-flex items-center gap-1.5 hover:text-[#E85535] transition-colors">
                                <span>{{ $c['cta'] }}</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tab Panel 2: Academics (Bento Card Style) -->
                <div x-show="activeKnowTab === 'academics'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($cfg['get_to_know']['tabs']['academics']['cards'] ?? [] as $idx => $c)
                    <div class="card-bento overflow-hidden flex flex-col justify-between h-full group" data-animate="tilt" data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                <img src="{{ asset($c['image']) }}" alt="{{ $c['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 bg-[#2563EB] text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-xs">
                                    {{ $c['badge'] }}
                                </span>
                            </div>
                            <div class="p-5 pt-4">
                                <h3 class="text-base font-bold text-slate-900 mb-2 leading-snug group-hover:text-[#2563EB] transition-colors">
                                    {{ $c['title'] }}
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                    {{ $c['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-5 pb-5 pt-2">
                            <a href="{{ $c['url'] }}" class="text-xs font-bold text-[#2563EB] inline-flex items-center gap-1.5 hover:text-[#1D4ED8] transition-colors">
                                <span>{{ $c['cta'] }}</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tab Panel 3: Boarding & Character (Bento Card Style) -->
                <div x-show="activeKnowTab === 'boarding'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($cfg['get_to_know']['tabs']['boarding']['cards'] ?? [] as $idx => $c)
                    <div class="card-bento overflow-hidden flex flex-col justify-between h-full group" data-animate="tilt" data-animate-delay="{{ $idx + 1 }}">
                        <div>
                            <div class="aspect-[16/10] w-full relative overflow-hidden bg-slate-100">
                                <img src="{{ asset($c['image']) }}" alt="{{ $c['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <span class="absolute top-3 left-3 bg-[#10B981] text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-xs">
                                    {{ $c['badge'] }}
                                </span>
                            </div>
                            <div class="p-6 pt-5">
                                <h3 class="text-lg font-bold text-slate-900 mb-2 leading-snug group-hover:text-[#10B981] transition-colors">
                                    {{ $c['title'] }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                                    {{ $c['desc'] }}
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2">
                            <a href="{{ $c['url'] }}" class="text-xs font-bold text-[#059669] inline-flex items-center gap-1.5 hover:text-[#047857] transition-colors">
                                <span>{{ $c['cta'] }}</span>
                                <span class="group-hover-arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
