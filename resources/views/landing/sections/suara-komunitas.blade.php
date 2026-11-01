        <!-- ── 8. "Community Voices" Testimonials (Wikrama Royal Archive) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 relative overflow-hidden" id="suara-komunitas">
            <!-- Background Ornaments: Bokeh Light Spot & Radial Tint -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 right-10 w-96 h-96 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 left-10 w-96 h-96 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-14" data-animate="fade-up">
                    <span class="google-tag google-tag-amber shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#E2B714]"></span>
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
                    $testimonialsList = $dbTestimonials->isNotEmpty() ? $dbTestimonials : collect($cfg['community_voices'] ?? []);
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($testimonialsList as $idx => $v)
                    @php
                        $isObject = is_object($v);
                        $category = $isObject ? $v->category : ($v['category'] ?? 'Komunitas');
                        $question = $isObject ? $v->question : ($v['question'] ?? null);
                        $quote = $isObject ? $v->quote : ($v['quote'] ?? '');
                        $imgUrl = $isObject ? $v->imageUrl() : asset($v['image']);
                        $name = $isObject ? $v->name : ($v['name'] ?? '');
                        $role = $isObject ? $v->role : ($v['role'] ?? '');
                    @endphp
                    <div class="card-quote-bubble flex flex-col justify-between h-full group bg-white" data-animate="fade-up" data-animate-delay="{{ ($idx % 4) + 1 }}">
                        <!-- Watermark Quote -->
                        <div class="absolute top-2 right-4 font-serif text-5xl text-[#00519E]/10 pointer-events-none select-none">&rdquo;</div>

                        <div>
                            <div class="flex items-center justify-between mb-3 pr-6">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#EBF4FC] text-[#00519E] border border-blue-200">
                                    {{ $category }}
                                </span>
                                <!-- Crisp SVG 5 Stars -->
                                <div class="flex items-center gap-0.5 text-[#F5D84A]">
                                    @for($s = 0; $s < 5; $s++)
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    @endfor
                                </div>
                            </div>
                            
                            @if($question)
                            <h4 class="font-bold text-sm text-slate-900 mb-2 leading-snug group-hover:text-[#00519E] transition-colors">
                                &ldquo;{{ $question }}&rdquo;
                            </h4>
                            @endif

                            <blockquote class="text-xs sm:text-sm text-slate-700 leading-relaxed font-editorial italic font-medium">
                                &ldquo;{{ $quote }}&rdquo;
                            </blockquote>
                        </div>

                        <div class="pt-5 mt-4 border-t border-slate-100 flex items-center gap-3">
                            <img src="{{ $imgUrl }}" 
                                 alt="{{ $name }}" 
                                 class="w-10 h-10 rounded-full object-cover shrink-0 border-2 border-blue-200" 
                                 loading="lazy" 
                                 decoding="async">
                            <div class="min-w-0">
                                <span class="font-bold text-xs text-slate-900 block truncate">{{ $name }}</span>
                                <span class="text-[10px] text-slate-500 block truncate font-medium">{{ $role }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
