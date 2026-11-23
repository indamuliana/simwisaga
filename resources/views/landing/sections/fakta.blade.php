        <!-- ── 4. Impact & Key Metrics Section (Wikrama Royal Archive) ── -->
        <section class="py-16 sm:py-24 lg:py-28 relative overflow-hidden bg-[#FAF9F6] text-slate-800" id="fakta">
            <!-- Background Ornaments: Soft Ambient Glow -->
            <div class="absolute inset-0 pointer-events-none z-0">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-100/40 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-100/30 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(#CBD5E1_1px,transparent_1px)] [background-size:28px_28px] opacity-40"></div>
            </div>

            <style>
                #fakta .fakta-card {
                    aspect-ratio: 1 / 1 !important;
                    width: 100%;
                    height: auto;
                    position: relative;
                    border-radius: 28px;
                    overflow: hidden;
                    cursor: pointer;
                    background-color: #0f172a;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12);
                    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                }

                #fakta .fakta-card:hover {
                    transform: translateY(-8px) !important;
                }

                #fakta .fakta-card-navy:hover {
                    box-shadow: 0 25px 35px -8px rgba(0, 81, 158, 0.45);
                }

                #fakta .fakta-card-forest:hover {
                    box-shadow: 0 25px 35px -8px rgba(31, 81, 63, 0.45);
                }

                #fakta .fakta-card-amber:hover {
                    box-shadow: 0 25px 35px -8px rgba(217, 119, 6, 0.45);
                }

                #fakta .fakta-diagonal-overlay {
                    clip-path: polygon(0 0, 36% 0, 84% 100%, 0 100%);
                    transition: clip-path 0.55s cubic-bezier(0.25, 1, 0.5, 1);
                    opacity: 0.98;
                }

                #fakta .fakta-card:hover .fakta-diagonal-overlay {
                    clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
                }

                #fakta .fakta-card-desc {
                    opacity: 0;
                    max-height: 0;
                    overflow: hidden;
                    transform: translateY(10px);
                    transition: opacity 0.35s ease 0.08s, max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.35s ease 0.08s;
                }

                #fakta .fakta-card:hover .fakta-card-desc {
                    opacity: 1;
                    max-height: 220px;
                    transform: translateY(0);
                }

                @media (max-width: 640px) {
                    #fakta .fakta-diagonal-overlay {
                        clip-path: polygon(0 0, 40% 0, 88% 100%, 0 100%);
                    }
                }
            </style>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                
                <!-- Section Header (Tanpa Badge Sesuai Permintaan) -->
                <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16" data-animate="fade-up">
                    <h2 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 mt-2">
                        Prestasi Bukti Dedikasi
                    </h2>
                </div>

                @php
                    // Filter: buang kartu 100% sertifikasi, ambil 3 kartu utama
                    $facts = collect($cfg['fast_facts'] ?? [])
                        ->filter(fn($f) => !str_contains($f['number'] ?? '', '100%') && !str_contains($f['label'] ?? '', 'Sertifikasi'))
                        ->take(3)
                        ->values();

                    // 3 Warna Berbeda Sesuai Palet Trinitas Wikrama (Navy, Forest Green, Amber Gold)
                    $cardThemes = [
                        0 => [
                            'name' => 'navy',
                            'bg_gradient' => 'linear-gradient(135deg, #00519E 0%, #00366B 100%)',
                        ],
                        1 => [
                            'name' => 'forest',
                            'bg_gradient' => 'linear-gradient(135deg, #1F513F 0%, #13382B 100%)',
                        ],
                        2 => [
                            'name' => 'amber',
                            'bg_gradient' => 'linear-gradient(135deg, #D97706 0%, #B45309 100%)',
                        ],
                    ];
                @endphp

                <!-- 3 Columns Metric Cards (Bujur Sangkar 1:1 Permanen, Hover Full Overlay & Deskripsi) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto">
                    @foreach($facts as $idx => $f)
                    @php
                        $theme = $cardThemes[$idx % 3];
                        $rawNum = trim($f['number'] ?? '0');
                        $hasNum = preg_match('/^(\d+)(.*)$/', $rawNum, $numMatches);
                        $countVal = $hasNum ? $numMatches[1] : null;
                        $suffixVal = $hasNum ? $numMatches[2] : '';
                    @endphp
                    <a href="{{ url('/prestasi') }}" 
                       class="fakta-card fakta-card-{{ $theme['name'] }} group relative block text-left" 
                       data-animate="scale" 
                       data-animate-delay="{{ $idx + 1 }}">
                        
                        <!-- Background Photo (Terlihat di Area Kanan-Atas saat Normal, Tertutup Penuh saat Hover) -->
                        <img src="{{ class_exists(\App\Modules\AdministrasiWeb\Models\WebSetting::class) ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($f['image'] ?? null, 'images/web/hero-tjkt.jpg') : asset($f['image']) }}" 
                             alt="{{ $f['label'] }}" 
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out z-0" 
                             loading="lazy"
                             decoding="async">
                        
                        <!-- Diagonal Color Overlay (Normal: Diagonal Slice, Hover: Menutup 100% Seluruh Kartu) -->
                        <div class="fakta-diagonal-overlay absolute inset-0 z-1 pointer-events-none" 
                             style="background: {{ $theme['bg_gradient'] }};"
                             aria-hidden="true"></div>

                        <!-- Card Content (Bottom-Left in White) -->
                        <div class="relative z-10 p-6 sm:p-8 flex flex-col justify-end text-left w-full h-full">
                            <div class="mt-auto">
                                @if($countVal !== null)
                                    <div class="jis-counter-number text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight drop-shadow-sm mb-2"
                                         data-count="{{ $countVal }}"
                                         data-suffix="{{ $suffixVal }}">
                                        {{ $rawNum }}
                                    </div>
                                @else
                                    <div class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight drop-shadow-sm mb-2">
                                        {{ $rawNum }}
                                    </div>
                                @endif
                                
                                <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white leading-snug drop-shadow-xs">
                                    {{ $f['label'] }}
                                </h3>

                                <!-- Deskripsi & CTA Link (Muncul Halus saat Hover Sesuai Desain Referensi Kartu Alumni) -->
                                <div class="fakta-card-desc">
                                    <p class="mt-3 text-xs sm:text-sm lg:text-[0.925rem] text-white/90 leading-relaxed font-normal">
                                        {{ $f['desc'] }}
                                    </p>
                                    <div class="mt-4 pt-1 inline-flex items-center gap-2 text-xs font-bold text-white tracking-wider uppercase">
                                        <span>Pelajari Lebih Lanjut</span>
                                        <svg class="w-3 h-3 text-[#F59E0B] fill-current" viewBox="0 0 20 20">
                                            <path d="M6 4l8 6-8 6V4z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </a>
                    @endforeach
                </div>

                <!-- Prestigious Wikrama CTA Button Connecting to Public Prestasi Showcase -->
                <div class="mt-12 sm:mt-16 text-center" data-animate="fade-up">
                    <a href="{{ url('/prestasi') }}" 
                       class="btn-jis-pill-gold !py-3.5 !px-8 text-sm sm:text-base font-extrabold shadow-lg inline-flex items-center gap-2.5 group">
                        <svg class="w-5 h-5 text-[#00305F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                        <span>Jelajahi Seluruh Prestasi &amp; Penghargaan Resmi</span>
                        <span class="text-base font-bold group-hover:translate-x-1.5 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
