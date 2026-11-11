        <!-- ── 12. National Education Report Card (Wikrama Royal Archive) ── -->
        <section class="py-14 sm:py-20 lg:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden" id="rapor-mutu">
            <!-- Background Ornaments -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-20 left-10 w-80 h-80 bg-blue-100/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 right-10 w-80 h-80 bg-amber-100/30 rounded-full blur-3xl"></div>
            </div>

            <style>
                .rapor-progress-track {
                    width: 100%;
                    background-color: #e2e8f0;
                    height: 12px;
                    border-radius: 9999px;
                    overflow: hidden;
                    padding: 2px;
                    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
                }
                .rapor-progress-fill {
                    height: 100%;
                    border-radius: 9999px;
                    background: linear-gradient(90deg, #00519E 0%, #0284C7 50%, #1F513F 100%);
                    width: 0%;
                    transition: width 2.4s cubic-bezier(0.22, 1, 0.36, 1);
                    box-shadow: 0 1px 4px rgba(0, 81, 158, 0.25);
                }
            </style>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                    
                    <!-- Progress Bar Metrics -->
                    <div class="lg:col-span-6" data-animate="slide-right">
                        <span class="google-tag google-tag-green shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-[#1F513F]"></span>
                            <span>{{ $cfg['rapor_mutu']['tag'] ?? 'Rapor Pendidikan Resmi Kemendikdasmen' }}</span>
                        </span>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-3 sm:mt-4">
                            {{ $cfg['rapor_mutu']['title'] ?? 'Peringkat 20% Teratas Nasional' }}
                        </h2>
                        <p class="text-xs text-slate-500 font-semibold mt-2 mb-6 sm:mb-8 font-mono-code">
                            *{{ $cfg['rapor_mutu']['source'] ?? 'Validasi Data Resmi: Kemendikdasmen RI (raportpendidikan.kemdikbud.go.id)' }}
                        </p>

                        <!-- High-Contrast Institutional Progress Bars with Dynamic Gradient & Animation -->
                        <div class="space-y-5 sm:space-y-6">
                            @foreach($cfg['rapor_mutu']['metrics'] ?? [] as $m)
                            @php
                                $badgeText = $m['badge'] ?? '';
                                preg_match('/^(\d+)%(.*)$/', $badgeText, $matches);
                                $targetNumber = isset($matches[1]) ? (int)$matches[1] : (int)($m['value'] ?? 0);
                                $suffixText = isset($matches[2]) ? trim($matches[2]) : '';
                            @endphp
                            <div>
                                <div class="flex items-center justify-between gap-3 text-xs sm:text-sm font-bold text-slate-900 mb-2">
                                    <span class="leading-tight">{{ $m['label'] }}</span>
                                    <span class="text-[#00519E] font-mono-code font-bold shrink-0 text-xs sm:text-sm">
                                        <span class="rapor-counter-val" data-target="{{ $targetNumber }}">0</span>%@if($suffixText) <span>{{ $suffixText }}</span>@endif
                                    </span>
                                </div>
                                <div class="rapor-progress-track">
                                    <div class="rapor-progress-fill" data-progress="{{ $m['value'] }}" style="width: 0%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Awards & Accreditations (Card Bento Style) -->
                    <div class="lg:col-span-6" data-animate="slide-left" data-animate-delay="1">
                        <div class="card-bento p-6 sm:p-8 bg-white border border-slate-200/90 shadow-[0_10px_30px_-5px_rgba(0,48,95,0.06)]">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
                                <span class="text-[#F5D84A] font-black text-xl">★</span> 
                                <span>Pengakuan Mutu &amp; Akreditasi Lembaga</span>
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($cfg['rapor_mutu']['awards'] ?? [] as $aw)
                                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/90 hover:border-[#00519E] transition-colors duration-200">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EBF4FC] text-[#00519E] border border-blue-200 mb-1.5">
                                        {{ $aw['badge'] }}
                                    </span>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $aw['title'] }}</h4>
                                    <p class="text-[11px] text-slate-600 font-normal mt-0.5">{{ $aw['desc'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                (function() {
                    function initRaporMutuAnimation() {
                        const section = document.getElementById('rapor-mutu');
                        if (!section) return;

                        const bars = section.querySelectorAll('.rapor-progress-fill');
                        const counters = section.querySelectorAll('.rapor-counter-val');
                        let hasAnimated = false;

                        function triggerProgressAnimation() {
                            if (hasAnimated) return;
                            hasAnimated = true;

                            // 1. Animate Width Bar
                            bars.forEach(function(bar) {
                                const target = bar.getAttribute('data-progress') || '0';
                                bar.style.width = target + '%';
                            });

                            // 2. Animate Counter Number
                            const duration = 2400;
                            const startTime = performance.now();

                            function stepCounter(currentTime) {
                                const elapsed = currentTime - startTime;
                                const progress = Math.min(elapsed / duration, 1);
                                const ease = 1 - Math.pow(1 - progress, 3); // cubic ease-out

                                counters.forEach(function(counter) {
                                    const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                                    counter.textContent = Math.round(ease * target);
                                });

                                if (progress < 1) {
                                    requestAnimationFrame(stepCounter);
                                }
                            }
                            requestAnimationFrame(stepCounter);
                        }

                        if ('IntersectionObserver' in window) {
                            const observer = new IntersectionObserver(function(entries) {
                                entries.forEach(function(entry) {
                                    if (entry.isIntersecting) {
                                        triggerProgressAnimation();
                                        observer.unobserve(entry.target);
                                    }
                                });
                            }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });
                            observer.observe(section);
                        } else {
                            triggerProgressAnimation();
                        }
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initRaporMutuAnimation);
                    } else {
                        initRaporMutuAnimation();
                    }
                })();
            </script>
        </section>
