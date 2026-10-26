        <!-- ── 8. "Community Voices" Testimonials (Bright & Warm Education) ── -->
        <section class="py-12 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="suara-komunitas">
            <!-- Background Ornaments: Bokeh Light Spot & Radial Tint -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 right-10 w-96 h-96 bg-orange-200/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 left-10 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="google-tag google-tag-amber shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Suara Komunitas &bull; Community Voices</span>
                    </span>
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                        Apa Kata Mereka Tentang Wikrama?
                    </h2>
                    <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-slate-600 leading-relaxed font-normal">
                        Kisah nyata dan ulasan mendalam dari siswa, orang tua, pendidik, dan alumni mengenai pengalaman bertumbuh di kampus Wikrama.
                    </p>
                </div>

                <!-- 4 Community Perspectives Cards (Quote Bubble Style) -->
                @php
                    $dbTestimonials = class_exists(\App\Modules\AdministrasiWeb\Models\WebTestimonial::class)
                        ? \App\Modules\AdministrasiWeb\Models\WebTestimonial::getActiveTestimonials()
                        : collect();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if($dbTestimonials->isNotEmpty())
                        @foreach($dbTestimonials as $idx => $v)
                        <div class="card-quote-bubble flex flex-col justify-between h-full group" data-animate="fade-up" data-animate-delay="{{ ($idx % 4) + 1 }}">
                            <!-- Watermark Quote -->
                            <div class="absolute top-2 right-4 font-serif text-5xl text-blue-500/10 pointer-events-none select-none">&rdquo;</div>

                            <div>
                                <div class="flex items-center justify-between mb-3 pr-6">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $v->category }}
                                    </span>
                                    <div class="flex items-center text-amber-400 text-xs">★★★★★</div>
                                </div>
                                
                                @if($v->question)
                                <h4 class="font-bold text-sm text-slate-900 mb-2 leading-snug group-hover:text-[#2563EB] transition-colors">
                                    &ldquo;{{ $v->question }}&rdquo;
                                </h4>
                                @endif

                                <blockquote class="text-xs sm:text-sm text-slate-700 leading-relaxed font-editorial italic font-medium">
                                    &ldquo;{{ $v->quote }}&rdquo;
                                </blockquote>
                            </div>

                            <div class="pt-5 mt-4 border-t border-slate-100 flex items-center gap-3">
                                <img src="{{ $v->imageUrl() }}" alt="{{ $v->name }}" class="w-10 h-10 rounded-full object-cover shrink-0 border-2 border-blue-200">
                                <div class="min-w-0">
                                    <span class="font-bold text-xs text-slate-900 block truncate">{{ $v->name }}</span>
                                    <span class="text-[10px] text-slate-500 block truncate font-medium">{{ $v->role }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        @foreach($cfg['community_voices'] ?? [] as $idx => $v)
                        <div class="card-quote-bubble flex flex-col justify-between h-full group" data-animate="fade-up" data-animate-delay="{{ $idx + 1 }}">
                            <!-- Watermark Quote -->
                            <div class="absolute top-2 right-4 font-serif text-5xl text-blue-500/10 pointer-events-none select-none">&rdquo;</div>

                            <div>
                                <div class="flex items-center justify-between mb-3 pr-6">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $v['category'] }}
                                    </span>
                                    <div class="flex items-center text-amber-400 text-xs">★★★★★</div>
                                </div>
                                
                                <h4 class="font-bold text-sm text-slate-900 mb-2 leading-snug group-hover:text-[#2563EB] transition-colors">
                                    &ldquo;{{ $v['question'] }}&rdquo;
                                </h4>

                                <blockquote class="text-xs sm:text-sm text-slate-700 leading-relaxed font-editorial italic font-medium">
                                    &ldquo;{{ $v['quote'] }}&rdquo;
                                </blockquote>
                            </div>

                            <div class="pt-5 mt-4 border-t border-slate-100 flex items-center gap-3">
                                <img src="{{ asset($v['image']) }}" alt="{{ $v['name'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 border-2 border-blue-200">
                                <div class="min-w-0">
                                    <span class="font-bold text-xs text-slate-900 block truncate">{{ $v['name'] }}</span>
                                    <span class="text-[10px] text-slate-500 block truncate font-medium">{{ $v['role'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
