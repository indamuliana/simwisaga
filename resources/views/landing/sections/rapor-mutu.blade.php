        <!-- ── 12. National Education Report Card (Validasi Mutu Resmi Kemendikdasmen) ── -->
        <section class="py-16 sm:py-24 bg-[#FAF9F6] text-slate-800 border-t border-slate-200/80 relative overflow-hidden scroll-mt-28 sm:scroll-mt-32" id="rapor-mutu">
            <span id="suara-komunitas" class="absolute -top-20"></span>

            <!-- Subtle Ambient Background Glows -->
            <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
                <div class="absolute -top-24 left-1/4 w-96 h-96 bg-blue-100/35 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 right-1/4 w-96 h-96 bg-sky-100/30 rounded-full blur-3xl"></div>
            </div>

            <!-- Scoped Styles for Guaranteed Vibrant Rendering without Tailwind JIT dependency -->
            <style>
                #rapor-mutu .rapor-progress-track {
                    background-color: #E2E8F0;
                    border: 1px solid #CBD5E1;
                    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
                }
                #rapor-mutu .rapor-progress-fill {
                    background: linear-gradient(90deg, #0284C7 0%, #00519E 60%, #0369A1 100%);
                    box-shadow: 0 2px 8px rgba(0, 81, 158, 0.3);
                }
                #rapor-mutu .gov-badge-blue {
                    background-color: #EBF4FC;
                    color: #00305F;
                    border-color: rgba(0, 81, 158, 0.22);
                }
            </style>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                    
                    <!-- Progress Bar Metrics (Left: 6 Cols) -->
                    <div class="lg:col-span-6 text-left" data-animate="fade-up">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#EBF4FC] text-[#00519E] border border-[#00519E]/20 shadow-2xs mb-3">
                            <svg class="w-3.5 h-3.5 text-[#00519E] shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#00519E] animate-pulse"></span>
                            <span>{{ $cfg['rapor_mutu']['tag'] ?? 'Rapor Pendidikan Resmi Kemendikdasmen' }}</span>
                        </div>
                        <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-2 leading-tight">
                            {{ $cfg['rapor_mutu']['title'] ?? 'Peringkat 20% Teratas Tingkat Nasional' }}
                        </h2>
                        
                        <div class="mt-2.5 mb-8">
                            <a href="https://raportpendidikan.kemdikbud.go.id" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="inline-flex items-center gap-1.5 text-xs text-[#00519E] hover:text-[#00305F] font-mono font-medium transition-colors group">
                                <span class="underline underline-offset-2 decoration-[#00519E]/40 group-hover:decoration-[#00519E]">
                                    *{{ $cfg['rapor_mutu']['source'] ?? 'Validasi Data Resmi: Kemendikdasmen RI (raportpendidikan.kemdikbud.go.id)' }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-[#00519E] opacity-70 group-hover:opacity-100 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Cohesive Gov-Tech Progress Bars -->
                        <div class="space-y-5 sm:space-y-6">
                            @foreach($cfg['rapor_mutu']['metrics'] ?? [] as $m)
                            @php
                                $badgeText = $m['badge'] ?? '';
                                preg_match('/^(\d+)%(.*)$/', $badgeText, $matches);
                                $targetNumber = isset($matches[1]) ? (int)$matches[1] : (int)($m['value'] ?? 0);
                                $suffixText = isset($matches[2]) ? trim($matches[2]) : '';
                            @endphp
                            <div>
                                <div class="flex items-center justify-between gap-2 text-xs sm:text-sm font-bold text-slate-900 mb-2">
                                    <div class="flex items-center gap-2 min-w-0 pr-1">
                                        <span class="w-2 h-2 rounded-full bg-[#00519E] shrink-0"></span>
                                        <span class="leading-snug text-slate-800 font-semibold">{{ $m['label'] }}</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 font-mono font-bold shrink-0 text-xs sm:text-sm px-2.5 py-0.5 rounded-lg gov-badge-blue border tabular-nums shadow-2xs">
                                        <span class="rapor-counter-val" data-target="{{ $targetNumber }}">0</span>%
                                        @if($suffixText)
                                            <span class="hidden sm:inline font-semibold text-[10px] sm:text-[11px] text-[#00519E]/85">
                                                {{ str_starts_with($suffixText, '(') ? $suffixText : '(' . $suffixText . ')' }}
                                            </span>
                                        @endif
                                    </span>
                                </div>
                                <div class="rapor-progress-track w-full h-3.5 rounded-full overflow-hidden p-0.5 shadow-inner">
                                    <div class="rapor-progress-fill h-full rounded-full transition-all duration-1000 ease-out" 
                                         data-progress="{{ $m['value'] ?? 75 }}"
                                         style="width: 0%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Awards & Accreditations (Audited Matrix / Data Rows - Right: 6 Cols) -->
                    <div class="lg:col-span-6 text-left" data-animate="fade-up" data-animate-delay="1">
                        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-md relative overflow-hidden">
                            <!-- Header Section Panel -->
                            <div class="flex items-center justify-between gap-3.5 mb-6 pb-4 border-b border-slate-100 relative z-10">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#00519E] to-sky-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                        <svg class="w-5 h-5 drop-shadow-xs" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                                            Pengakuan Mutu &amp; Akreditasi Lembaga
                                        </h3>
                                        <p class="text-xs text-slate-500 font-normal">
                                            Rekognisi keunggulan BAN-PDM, kementerian &amp; mitra internasional
                                        </p>
                                    </div>
                                </div>
                                <div class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold text-[#00519E] bg-[#EBF4FC] border border-[#00519E]/20 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Audit Terpadu</span>
                                </div>
                            </div>

                            <!-- Audited Matrix / Structured Data Rows -->
                            <div class="space-y-3 relative z-10">
                                @foreach($cfg['rapor_mutu']['awards'] ?? [] as $aw)
                                <div class="p-3.5 sm:p-4 rounded-xl border border-slate-200/90 hover:border-[#00519E]/40 bg-slate-50/70 hover:bg-[#EBF4FC]/40 transition-all duration-200 flex items-center justify-between gap-3 group">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 group-hover:border-[#00519E]/30 text-[#00519E] flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                            <svg class="w-4 h-4 text-[#00519E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold text-[#00305F] bg-[#EBF4FC] border border-[#00519E]/25 tracking-wide font-mono">
                                                    {{ $aw['badge'] }}
                                                </span>
                                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#00519E] transition-colors">
                                                    {{ $aw['title'] }}
                                                </h4>
                                            </div>
                                            <p class="text-[11px] sm:text-xs text-slate-600 font-normal mt-0.5 leading-relaxed">
                                                {{ $aw['desc'] }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="shrink-0 flex items-center pl-2">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                            </svg>
                                            <span class="hidden sm:inline">Terverifikasi</span>
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Institutional Footer Validation Stamp -->
                            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600 relative z-10">
                                <span class="inline-flex items-center gap-1.5 font-semibold text-[#00519E]">
                                    <svg class="w-3.5 h-3.5 text-[#00519E]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Capaian Terbaik Vokasi se-Jawa Barat</span>
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-md border border-slate-200 font-mono">
                                    <span>Kemendikdasmen RI &bull; Valid</span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Animated Counter & Progress Fill Observer ── -->
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
                            const duration = 2000;
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